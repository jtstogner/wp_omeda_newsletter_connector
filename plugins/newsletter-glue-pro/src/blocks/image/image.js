import React from 'react';

/**
 * WordPress dependencies
 */
import { isBlobURL } from '@wordpress/blob';
import {
  BlockAlignmentControl,
  BlockControls,
  store as blockEditorStore,
  __experimentalImageEditor as ImageEditor,
  __experimentalImageSizeControl as ImageSizeControl,
  __experimentalImageURLInputUI as ImageURLInputUI,
  InspectorControls,
  MediaReplaceFlow,
  RichText,
  __experimentalUseBorderProps as useBorderProps,
} from '@wordpress/block-editor';
import {
  createBlock,
  switchToBlockType,
} from '@wordpress/blocks';
import {
  ExternalLink,
  PanelBody,
  PanelRow,
  ResizableBox,
  Spinner,
  TextareaControl,
  TextControl,
  ToggleControl,
  ToolbarButton,
} from '@wordpress/components';
import { usePrevious, useViewportMatch } from '@wordpress/compose';
import { store as coreStore } from '@wordpress/core-data';
import { useDispatch, useSelect } from '@wordpress/data';
import {
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from '@wordpress/element';
import { __, isRTL, sprintf } from '@wordpress/i18n';
import {
  caption as captionIcon,
  crop,
  overlayText,
  upload,
} from '@wordpress/icons';
import { store as noticesStore } from '@wordpress/notices';
import { getFilename } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { createUpgradedEmbedBlock } from '../embed/util';
import { isExternalImage } from './edit';
import useClientWidth from './use-client-width';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

import { theme } from '../../defaults/theme.js';

/**
 * Module constants
 */
import { ALLOWED_MEDIA_TYPES, MIN_SIZE } from './constants';

export default function Image({
  temporaryURL,
  attributes,
  setAttributes,
  isSelected,
  insertBlocksAfter,
  onReplace,
  onSelectImage,
  onSelectURL,
  onUploadError,
  containerRef,
  context,
  clientId,
  isContentLocked,
  className
}) {
  const {
    url = '',
    alt,
    caption,
    align,
    id,
    href,
    rel,
    linkClass,
    linkDestination,
    title,
    width,
    height,
    mobile_width,
    mobile_height,
    linkTarget,
    sizeSlug,
  } = attributes;

  let threshold = attributes.threshold;

  const imageRef = useRef();
  const prevCaption = usePrevious(caption);
  const [showCaption, setShowCaption] = useState(!!caption);
  const { allowResize = true } = context;
  const { getBlock } = useSelect(blockEditorStore);

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  const { image, multiImageSelection } = useSelect(
    (select) => {
      const { getMedia } = select(coreStore);
      const { getMultiSelectedBlockClientIds, getBlockName } =
        select(blockEditorStore);
      const multiSelectedClientIds = getMultiSelectedBlockClientIds();
      return {
        image:
          id && isSelected
            ? getMedia(id, { context: 'view' })
            : null,
        multiImageSelection:
          multiSelectedClientIds.length &&
          multiSelectedClientIds.every(
            (_clientId) =>
              getBlockName(_clientId) === 'core/image'
          ),
      };
    },
    [id, isSelected, clientId]
  );

  if (attributes.padding) {
    let top_space = parseInt(attributes.padding.left);
    let bottom_space = parseInt(attributes.padding.right);
    threshold = threshold - top_space - bottom_space;
  }

  const { canInsertCover, imageEditing, mediaUpload } =
    useSelect(
      (select) => {
        const {
          getBlockRootClientId,
          getSettings,
          canInsertBlockType,
        } = select(blockEditorStore);

        const rootClientId = getBlockRootClientId(clientId);
        const settings = getSettings();

        return {
          imageEditing: settings.imageEditing,
          imageSizes: settings.imageSizes,
          maxWidth: threshold,
          mediaUpload: settings.mediaUpload,
          canInsertCover: canInsertBlockType(
            'core/cover',
            rootClientId
          ),
        };
      },
      [clientId]
    );
  const { replaceBlocks, toggleSelection } = useDispatch(blockEditorStore);
  const { createErrorNotice, createSuccessNotice } =
    useDispatch(noticesStore);
  const isLargeViewport = useViewportMatch('medium');
  const isWideAligned = ['wide', 'full'].includes(align);
  const [
    { loadedNaturalWidth, loadedNaturalHeight },
    setLoadedNaturalSize,
  ] = useState({});
  const [isEditingImage, setIsEditingImage] = useState(false);
  const [externalBlob, setExternalBlob] = useState();
  const clientWidth = useClientWidth(containerRef, [align]);
  const isResizable =
    allowResize &&
    !isContentLocked &&
    !(isWideAligned && isLargeViewport);

  const canUploadMedia = !!mediaUpload;

  // If an image is externally hosted, try to fetch the image data. This may
  // fail if the image host doesn't allow CORS with the domain. If it works,
  // we can enable a button in the toolbar to upload the image.
  useEffect(() => {
    if (
      !isExternalImage(id, url) ||
      !isSelected ||
      !canUploadMedia ||
      externalBlob
    ) {
      return;
    }

    window
      .fetch(url)
      .then((response) => response.blob())
      .then((blob) => setExternalBlob(blob))
      // Do nothing, cannot upload.
      .catch(() => { });
  }, [id, url, isSelected, externalBlob, canUploadMedia]);

  // We need to show the caption when changes come from
  // history navigation(undo/redo).
  useEffect(() => {
    if (caption && !prevCaption) {
      setShowCaption(true);
    }
  }, [caption, prevCaption]);

  useEffect(() => {
    let newHeight;
    if (attributes.width) {
      let ratio = imageRef.current?.naturalHeight / imageRef.current?.naturalWidth;
      let hSize = attributes.threshold < attributes.width ? attributes.threshold : attributes.width;
      newHeight = hSize * ratio;
      if (attributes.threshold < attributes.width) {
        setAttributes({ width: Math.floor(attributes.threshold) });
      }
      if (newHeight) {
        setAttributes({ height: Math.floor(newHeight) });
      }
    }
  }, [attributes.threshold]);

  // Focus the caption when we click to add one.
  const captionRef = useCallback(
    (node) => {
      if (node && !caption) {
        node.focus();
      }
    },
    [caption]
  );

  // Get naturalWidth and naturalHeight from image ref, and fall back to loaded natural
  // width and height. This resolves an issue in Safari where the loaded natural
  // width and height is otherwise lost when switching between alignments.
  // See: https://github.com/WordPress/gutenberg/pull/37210.
  var imageMaxWidth = imageRef.current?.naturalWidth > threshold ? threshold : imageRef.current?.naturalWidth;
  let imageMaxHeight;
  if (imageRef.current?.naturalWidth > threshold) {
    const getImageRatio = imageRef.current?.naturalHeight / imageRef.current?.naturalWidth;
    imageMaxHeight = imageMaxWidth * getImageRatio;
  } else {
    imageMaxHeight = imageRef.current?.naturalHeight;
  }

  const { naturalWidth, naturalHeight } = useMemo(() => {
    return {
      naturalWidth:
        parseInt(imageMaxWidth) ||
        loadedNaturalWidth ||
        undefined,
      naturalHeight:
        parseInt(imageMaxHeight) ||
        loadedNaturalHeight ||
        undefined,
    };
  }, [
    loadedNaturalWidth,
    loadedNaturalHeight,
    imageRef.current?.complete,
    attributes.threshold,
  ]);

  function onResizeStart() {
    toggleSelection(false);
  }

  function onResizeStop() {
    toggleSelection(true);
  }

  function onImageError() {
    // Check if there's an embed block that handles this URL, e.g., instagram URL.
    // See: https://github.com/WordPress/gutenberg/pull/11472
    const embedBlock = createUpgradedEmbedBlock({ attributes: { url } });

    if (undefined !== embedBlock) {
      onReplace(embedBlock);
    }
  }

  function onSetHref(props) {
    setAttributes(props);
  }

  function onSetTitle(value) {
    // This is the HTML title attribute, separate from the media object
    // title.
    setAttributes({ title: value });
  }

  function updateAlt(newAlt) {
    setAttributes({ alt: newAlt });
  }

  function updateImage(newSizeSlug) {
    const newUrl = image?.media_details?.sizes?.[newSizeSlug]?.source_url;
    if (!newUrl) {
      return null;
    }

    setAttributes({
      url: newUrl,
      width: undefined,
      height: undefined,
      sizeSlug: newSizeSlug,
    });
  }

  function uploadExternal() {
    mediaUpload({
      filesList: [externalBlob],
      onFileChange([img]) {
        onSelectImage(img);

        if (isBlobURL(img.url)) {
          return;
        }

        setExternalBlob();
        createSuccessNotice(__('Image uploaded.'), {
          type: 'snackbar',
        });
      },
      allowedTypes: ALLOWED_MEDIA_TYPES,
      onError(message) {
        createErrorNotice(message, { type: 'snackbar' });
      },
    });
  }

  function updateAlignment(nextAlign) {
    const extraUpdatedAttributes = ['wide', 'full'].includes(nextAlign)
      ? { width: undefined, height: undefined }
      : {};
    setAttributes({
      ...extraUpdatedAttributes,
      align: nextAlign,
    });
  }

  useEffect(() => {
    if (!isSelected) {
      setIsEditingImage(false);
      if (!caption) {
        setShowCaption(false);
      }
    }
  }, [isSelected, caption]);

  const canEditImage = id && naturalWidth && naturalHeight && imageEditing;
  const allowCrop = !multiImageSelection && canEditImage && !isEditingImage;

  function switchToCover() {
    replaceBlocks(
      clientId,
      switchToBlockType(getBlock(clientId), 'core/cover')
    );
  }

  var maxImageW = naturalWidth;
  let maxImageH;
  if (maxImageW > threshold) {
    const imageratio = naturalHeight / naturalWidth;
    maxImageH = maxImageW * imageratio;
  } else {
    maxImageH = naturalHeight;
  }

  const controls = (
    <>
      <BlockControls group="block">
        {!isContentLocked && (
          <BlockAlignmentControl
            value={align}
            onChange={updateAlignment}
          />
        )}
        {!isContentLocked && (
          <ToolbarButton
            onClick={() => {
              setShowCaption(!showCaption);
              if (showCaption && caption) {
                setAttributes({ caption: undefined });
              }
            }}
            icon={captionIcon}
            isPressed={showCaption}
            label={
              showCaption
                ? __('Remove caption')
                : __('Add caption')
            }
          />
        )}
        {!multiImageSelection && !isEditingImage && (
          <ImageURLInputUI
            url={href || ''}
            onChangeUrl={onSetHref}
            linkDestination={linkDestination}
            mediaUrl={(image && image.source_url) || url}
            mediaLink={image && image.link}
            linkTarget={linkTarget}
            linkClass={linkClass}
            rel={rel}
          />
        )}
        {allowCrop && (
          <ToolbarButton
            onClick={() => setIsEditingImage(true)}
            icon={crop}
            label={__('Crop')}
          />
        )}
        {externalBlob && (
          <ToolbarButton
            onClick={uploadExternal}
            icon={upload}
            label={__('Upload external image')}
          />
        )}
        {!multiImageSelection && canInsertCover && (
          <ToolbarButton
            icon={overlayText}
            label={__('Add text over image')}
            onClick={switchToCover}
          />
        )}
      </BlockControls>
      {!multiImageSelection && !isEditingImage && (
        <BlockControls group="other">
          <MediaReplaceFlow
            mediaId={id}
            mediaURL={url}
            allowedTypes={ALLOWED_MEDIA_TYPES}
            accept="image/*"
            onSelect={onSelectImage}
            onSelectURL={onSelectURL}
            onError={onUploadError}
          />
        </BlockControls>
      )}
      <InspectorControls>
        <PanelBody title={__('Settings')} className={`${deviceType != 'Desktop' ? 'ng-mobile-control' : ''}`}>
          {!multiImageSelection && (deviceType !== 'Mobile') && (
            <TextareaControl
              __nextHasNoMarginBottom
              label={__('Alternative text')}
              value={alt}
              onChange={updateAlt}
              help={
                <>
                  <ExternalLink href="https://www.w3.org/WAI/tutorials/images/decision-tree">
                    {__(
                      'Describe the purpose of the image.'
                    )}
                  </ExternalLink>
                  <br />
                  {__('Leave empty if decorative.')}
                </>
              }
            />
          )}
          {deviceType === 'Mobile' && (
            <PanelRow>
              <ToggleControl
                label={__('Keep original image dimensions on mobile', 'newsletter-glue')}
                checked={attributes.mobile_keep_size}
                onChange={(value) => {
                  setAttributes({ mobile_keep_size: value });
                }}
              />
            </PanelRow>
          )}

          {deviceType !== 'Mobile' && (
            <ImageSizeControl
              onChangeImage={updateImage}
              onChange={(value) => setAttributes(value)}
              slug={sizeSlug}
              width={width}
              height={height}
              imageSizeOptions={false}
              isResizable={true}
              imageWidth={maxImageW}
              imageHeight={maxImageH}
              imageSizeHelp={__(
                'Select the size of the source image.'
              )}
            />)}

          {deviceType === 'Mobile' && !attributes.mobile_keep_size && (
            <ImageSizeControl
              onChangeImage={updateImage}
              onChange={(value) => {
                if (value.width) {
                  setAttributes({ mobile_width: value.width });
                }
                if (value.height) {
                  setAttributes({ mobile_height: value.height });
                }
              }}
              slug={sizeSlug}
              width={mobile_width}
              height={mobile_height}
              imageSizeOptions={false}
              isResizable={true}
              imageWidth={mobile_width}
              imageHeight={mobile_height}
              imageSizeHelp={__(
                'Select the size of the source image.'
              )}
            />)}

        </PanelBody>
        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>
      <InspectorControls group="advanced">
        <TextControl
          __nextHasNoMarginBottom
          label={__('Title attribute')}
          value={title || ''}
          onChange={onSetTitle}
          help={
            <>
              {__(
                'Describe the role of this image on the page.'
              )}
              <ExternalLink href="https://www.w3.org/TR/html52/dom.html#the-title-attribute">
                {__(
                  '(Note: many devices and browsers do not display this text.)'
                )}
              </ExternalLink>
            </>
          }
        />
      </InspectorControls>
    </>
  );

  const filename = getFilename(url);
  let defaultedAlt;

  if (alt) {
    defaultedAlt = alt;
  } else if (filename) {
    defaultedAlt = sprintf(
      /* translators: %s: file name */
      __('This image has an empty alt attribute; its file name is %s'),
      filename
    );
  } else {
    defaultedAlt = __('This image has an empty alt attribute');
  }

  const borderProps = useBorderProps(attributes);
  const isRounded = attributes.className?.includes('is-style-rounded');

  var imageWidth = attributes.width || naturalWidth;
  var imageHeight = attributes.height || naturalHeight;

  let imageStyles = {
    borderWidth: attributes.borderSize ? attributes.borderSize : undefined,
    borderStyle: attributes.borderSize ? 'solid' : 'none',
    borderColor: attributes.borderSize ? attributes.border : 'transparent',
    borderRadius: attributes.radius ? attributes.radius : undefined,
    boxSizing: 'border-box',
  };

  let img = (
    <>
      <img
        src={temporaryURL || url}
        alt={defaultedAlt}
        onError={() => onImageError()}
        onLoad={(event) => {
          setLoadedNaturalSize({
            loadedNaturalWidth: event.target?.naturalWidth > threshold ? threshold : event.target?.naturalWidth,
            loadedNaturalHeight: event.target?.naturalHeight,
          });
          if (!attributes.width) {
            setAttributes({
              width: imageWidth,
              height: imageHeight,
            });
          }
        }}
        ref={imageRef}
        className={borderProps.className}
        style={imageStyles}
        width={isMobile ? mobile_width : imageWidth}
        height={isMobile ? mobile_height : imageHeight}
      />
      {temporaryURL && <Spinner />}
    </>
  );

  let imageWidthWithinContainer;
  let imageHeightWithinContainer;

  if (clientWidth && naturalWidth && naturalHeight) {
    const exceedMaxWidth = naturalWidth > clientWidth;
    const ratio = naturalHeight / naturalWidth;
    imageWidthWithinContainer = exceedMaxWidth ? clientWidth : naturalWidth;
    imageHeightWithinContainer = exceedMaxWidth
      ? clientWidth * ratio
      : naturalHeight;
  }

  if (canEditImage && isEditingImage) {
    img = (
      <ImageEditor
        id={id}
        url={url}
        width={width}
        height={height}
        clientWidth={clientWidth}
        naturalHeight={naturalHeight}
        naturalWidth={naturalWidth}
        onSaveImage={(imageAttributes) => {
          setAttributes(imageAttributes);
        }}
        onFinishEditing={() => {
          setIsEditingImage(false);
        }}
        borderProps={isRounded ? undefined : borderProps}
      />
    );
  } else if (!isResizable || !imageWidthWithinContainer) {
    img = <div style={{ width, height }}>{img}</div>;
  } else {
    const currentWidth = (isMobile && mobile_width ? mobile_width : width) || imageWidthWithinContainer;
    const currentHeight = (isMobile && mobile_height ? mobile_height : height) || imageHeightWithinContainer;

    const ratio = naturalWidth / naturalHeight;
    const minWidth =
      naturalWidth < naturalHeight ? MIN_SIZE : MIN_SIZE * ratio;
    const minHeight =
      naturalHeight < naturalWidth ? MIN_SIZE : MIN_SIZE / ratio;

    // With the current implementation of ResizableBox, an image needs an
    // explicit pixel value for the max-width. In absence of being able to
    // set the content-width, this max-width is currently dictated by the
    // vanilla editor style. The following variable adds a buffer to this
    // vanilla style, so 3rd party themes have some wiggleroom. This does,
    // in most cases, allow you to scale the image beyond the width of the
    // main column, though not infinitely.
    // @todo It would be good to revisit this once a content-width variable
    // becomes available.
    let maxWidthBuffer = isMobile ? 345 : threshold;

    let showRightHandle = false;
    let showLeftHandle = false;

    // See https://github.com/WordPress/gutenberg/issues/7584.
    if (align === 'center') {
      // When the image is centered, show both handles.
      showRightHandle = true;
      showLeftHandle = true;
    } else if (isRTL()) {
      // In RTL mode the image is on the right by default.
      // Show the right handle and hide the left handle only when it is
      // aligned left. Otherwise always show the left handle.
      if (align === 'left') {
        showRightHandle = true;
      } else {
        showLeftHandle = true;
      }
    } else {
      // Show the left handle and hide the right handle only when the
      // image is aligned right. Otherwise always show the right handle.
      if (align === 'right') {
        showLeftHandle = true;
      } else {
        showRightHandle = true;
      }
    }

    let resizableW = width ? width : 'auto';
    if (isMobile) {
      if (mobile_width) {
        resizableW = mobile_width;
      } else {
        resizableW = 345;
      }
    }

    let resizableH = height ? height : 'auto';
    if (isMobile) {
      if (mobile_height) {
        resizableH = mobile_height;
      } else {
        resizableH = 'auto';
      }
    }

    img = (
      <ResizableBox
        size={{
          width: resizableW,
          height: resizableH,
        }}
        showHandle={isSelected}
        minWidth={minWidth}
        maxWidth={maxWidthBuffer}
        minHeight={minHeight}
        maxHeight={maxWidthBuffer / ratio}
        lockAspectRatio
        enable={{
          top: false,
          right: showRightHandle,
          bottom: true,
          left: showLeftHandle,
        }}
        onResizeStart={onResizeStart}
        onResizeStop={(event, direction, elt, delta) => {
          onResizeStop();
          if (isMobile) {
            setAttributes({
              mobile_width: parseInt(currentWidth + delta.width, 10),
              mobile_height: parseInt(currentHeight + delta.height, 10),
            });
          } else {
            setAttributes({
              width: parseInt(currentWidth + delta.width, 10),
              height: parseInt(currentHeight + delta.height, 10),
            });
          }
        }}
        resizeRatio={align === 'center' ? 2 : 1}
      >
        {img}
      </ResizableBox>
    );
  }

  const imageAlign = ['center', 'right', 'left'].includes(align) ? align : 'center';

  const color = attributes.color ? attributes.color : theme.color;

  const captionStyle = {
    fontFamily: nglue_backend.font_names[attributes.font.key],
    color: color,
    fontSize: isMobile ? attributes.mobile_size : attributes.fontsize,
    fontWeight: attributes.fontweight.key,
  };

  const bottom = isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom;

  let tdStyle;
  let tdCaptionStyle;
  tdStyle = {
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
  };

  if (showCaption) {
    tdStyle.paddingBottom = 0;
    tdCaptionStyle = {
      paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
      paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
      paddingBottom: bottom,
      lineHeight: 1.5,
      fontSize: isMobile ? attributes.mobile_size : attributes.fontsize,
      fontFamily: nglue_backend.font_names[attributes.font.key],
    }
  }

  return (
    <>
      { /* Hide controls during upload to avoid component remount,
				which causes duplicated image upload. */ }
      {!temporaryURL && controls}
      <tr><td className="ng-block-td" align={imageAlign} style={tdStyle}>{img}</td></tr>
      {showCaption &&
        (!RichText.isEmpty(caption) || isSelected) && (
          <tr>
            <td className="ng-block-caption" align={imageAlign} style={tdCaptionStyle}>
              <RichText
                identifier="caption"
                ref={captionRef}
                tagName="span"
                style={captionStyle}
                aria-label={__('Image caption text')}
                placeholder={__('Add caption')}
                value={caption}
                onChange={(value) =>
                  setAttributes({ caption: value })
                }
                inlineToolbar
                __unstableOnSplitAtEnd={() =>
                  insertBlocksAfter(
                    createBlock('newsletterglue/text')
                  )
                }
              />
            </td>
          </tr>
        )}
    </>
  );
}
