import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  PanelBody,
  Popover,
  TextControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  ToolbarButton,
} from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
  AlignmentControl,
  BlockControls,
  InspectorControls,
  __experimentalLinkControl as LinkControl,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { useMergeRefs } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { link, linkOff } from '@wordpress/icons';
import { displayShortcut, isKeyboardEvent } from '@wordpress/keycodes';
import { prependHTTP } from '@wordpress/url';

const NEW_TAB_REL = 'noopener noreferrer';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

import { theme } from '../../defaults/theme.js';

function StylePanel({ attributes, setAttributes }) {

  return (
    <PanelBody title={__('Style')}>
      <ToggleGroupControl
        value={attributes.buttonstyle}
        onChange={(newStyle) => setAttributes({ buttonstyle: newStyle })}
        isBlock
      >
        <ToggleGroupControlOption
          value="filled"
          label={__('Filled')}
        />
        <ToggleGroupControlOption
          value="outline"
          label={__('Outlined')}
        />
      </ToggleGroupControl>
    </PanelBody>
  );
}

function ButtonEdit(props) {
  const {
    attributes,
    setAttributes,
    className,
    isSelected,
    onReplace,
    mergeBlocks,
    clientId,
  } = props;
  const { textAlign, linkTarget, placeholder, rel, text, url } =
    attributes;

  function onToggleOpenInNewTab(value) {
    const newLinkTarget = value ? '_blank' : undefined;

    let updatedRel = rel;
    if (newLinkTarget && !rel) {
      updatedRel = NEW_TAB_REL;
    } else if (!newLinkTarget && rel === NEW_TAB_REL) {
      updatedRel = undefined;
    }

    setAttributes({
      linkTarget: newLinkTarget,
      rel: updatedRel,
    });
  }

  function setButtonText(newText) {
    // Remove anchor tags from button text content.
    setAttributes({ text: newText.replace(/<\/?a[^>]*>/g, '') });
  }

  function onKeyDown(event) {
    if (isKeyboardEvent.primary(event, 'k')) {
      startEditing(event);
    } else if (isKeyboardEvent.primaryShift(event, 'k')) {
      unlink();
      richTextRef.current?.focus();
    }
  }

  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [popoverAnchor, setPopoverAnchor] = useState(null);

  const ref = useRef();
  const richTextRef = useRef();
  const blockProps = useBlockProps({
    ref: useMergeRefs([setPopoverAnchor, ref]),
    onKeyDown,
  });

  const [isEditingURL, setIsEditingURL] = useState(false);
  const isURLSet = !!url;
  const opensInNewTab = linkTarget === '_blank';

  function startEditing(event) {
    event.preventDefault();
    setIsEditingURL(true);
  }

  function unlink() {
    setAttributes({
      url: undefined,
      linkTarget: undefined,
      rel: undefined,
    });
    setIsEditingURL(false);
  }

  useEffect(() => {
    if (!isSelected) {
      setIsEditingURL(false);
    }
  }, [isSelected]);

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  let buttonWidth;
  if (attributes.width === 'custom') {
    buttonWidth = attributes.custom_width ? attributes.custom_width : theme.button.width + 'px';
  } else {
    buttonWidth = 'auto';
  }
  if (attributes.width === 'full') {
    buttonWidth = '100%';
  }

  let mbuttonWidth;
  if (isMobile) {
    if (attributes.mobile_custom_width) {
      mbuttonWidth = attributes.mobile_custom_width ? attributes.mobile_custom_width : buttonWidth;
    }
  }
  if (!mbuttonWidth) {
    mbuttonWidth = '100%';
  }

  const defaultButtonBg = attributes.buttonstyle === 'filled' ? theme.button.bg : '#ffffff';
  const defaultButtonColor = attributes.buttonstyle === 'filled' ? theme.button.color : theme.button.bg;
  const defaultBorder = attributes.buttonstyle === 'filled' ? (attributes.background ? attributes.background : defaultButtonBg) : (attributes.color ? attributes.color : defaultButtonColor);
  const defaultBorderSize = '2px';

  let radius = attributes.radius ? attributes.radius : '0px';
  radius = parseInt(radius) + 'px';

  const buttonStyle = {
    fontFamily: nglue_backend.font_names[attributes.font.key],
    fontSize: isMobile ? attributes.mobile_fontsize : attributes.fontsize,
    lineHeight: isMobile ? attributes.mobile_lineheight : attributes.lineheight,
    fontWeight: attributes.fontweight.key,
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
    textAlign: attributes.textAlign ? attributes.textAlign : 'center',
    width: isMobile && mbuttonWidth ? mbuttonWidth : buttonWidth,
    backgroundColor: attributes.background ? attributes.background : defaultButtonBg,
    color: attributes.color ? attributes.color : defaultButtonColor,
    borderWidth: attributes.borderSize ? attributes.borderSize : defaultBorderSize,
    borderStyle: 'solid',
    borderColor: attributes.border ? attributes.border : defaultBorder,
    borderRadius: radius,
    boxSizing: 'border-box',
  }

  return (
    <>
      <div
        {...blockProps}
        className={classnames(blockProps.className, {

        })}
        style={{
          flexBasis: buttonWidth,
        }}
      >
        <RichText
          ref={richTextRef}
          aria-label={__('Button text')}
          placeholder={placeholder || __('Add text…')}
          value={text}
          onChange={(value) => setButtonText(value)}
          withoutInteractiveFormatting
          className={classnames(
            className,
            'ng-block-button__link',
          )}
          style={buttonStyle}
          onSplit={(value) =>
            createBlock('newsletterglue/button', {
              ...attributes,
              text: value,
            })
          }
          onReplace={onReplace}
          onMerge={mergeBlocks}
          identifier="text"
        />
      </div>
      <BlockControls group="block">
        <AlignmentControl
          value={textAlign}
          onChange={(nextAlign) => {
            setAttributes({ textAlign: nextAlign });
          }}
        />
        {!isURLSet && (
          <ToolbarButton
            name="link"
            icon={link}
            title={__('Link')}
            shortcut={displayShortcut.primary('k')}
            onClick={startEditing}
          />
        )}
        {isURLSet && (
          <ToolbarButton
            name="link"
            icon={linkOff}
            title={__('Unlink')}
            shortcut={displayShortcut.primaryShift('k')}
            onClick={unlink}
            isActive={true}
          />
        )}
      </BlockControls>
      {isSelected && (isEditingURL || isURLSet) && (
        <Popover
          placement="bottom"
          onClose={() => {
            setIsEditingURL(false);
            richTextRef.current?.focus();
          }}
          anchor={popoverAnchor}
          focusOnMount={isEditingURL ? 'firstElement' : false}
          __unstableSlotName={'__unstable-block-tools-after'}
          shift
        >
          <LinkControl
            className="wp-block-navigation-link__inline-link-input"
            value={{ url, opensInNewTab }}
            onChange={({
              url: newURL = '',
              opensInNewTab: newOpensInNewTab,
            }) => {
              setAttributes({ url: prependHTTP(newURL) });

              if (opensInNewTab !== newOpensInNewTab) {
                onToggleOpenInNewTab(newOpensInNewTab);
              }
            }}
            onRemove={() => {
              unlink();
              richTextRef.current?.focus();
            }}
            forceIsEditingLink={isEditingURL}
          />
        </Popover>
      )}
      <InspectorControls>
        {deviceType !== 'Mobile' &&
          <StylePanel
            attributes={attributes}
            setAttributes={setAttributes}
          />
        }
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
          label={__('Link rel')}
          value={rel || ''}
          onChange={(newRel) => setAttributes({ rel: newRel })}
        />
      </InspectorControls>
    </>
  );
}

export default ButtonEdit;
