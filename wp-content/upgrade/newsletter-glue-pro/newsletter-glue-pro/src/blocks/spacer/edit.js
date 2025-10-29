import React from 'react';

import classnames from 'classnames';

import { useBlockProps } from '@wordpress/block-editor';
import { ResizableBox } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

import { MIN_SPACER_SIZE } from './constants';
import SpacerControls from './controls';
import MobileControls from './mobile-controls';

const ResizableSpacer = ({
  orientation,
  onResizeStart,
  onResize,
  onResizeStop,
  isSelected,
  isResizing,
  setIsResizing,
  ...props
}) => {
  const getCurrentSize = (elt) => {
    return orientation === 'horizontal'
      ? elt.clientWidth
      : elt.clientHeight;
  };

  const getNextVal = (elt) => {
    return `${getCurrentSize(elt)}px`;
  };

  return (
    <ResizableBox
      className={classnames('block-library-spacer__resize-container', {
        'resize-horizontal': orientation === 'horizontal',
        'is-resizing': isResizing,
        'is-selected': isSelected,
      })}
      onResizeStart={(_event, _direction, elt) => {
        const nextVal = getNextVal(elt);
        onResizeStart(nextVal);
        onResize(nextVal);
      }}
      onResize={(_event, _direction, elt) => {
        onResize(getNextVal(elt));
        if (!isResizing) {
          setIsResizing(true);
        }
      }}
      onResizeStop={(_event, _direction, elt) => {
        const nextVal = getCurrentSize(elt);
        onResizeStop(`${nextVal}px`);
        setIsResizing(false);
      }}
      __experimentalShowTooltip={true}
      __experimentalTooltipProps={{
        axis: orientation === 'horizontal' ? 'x' : 'y',
        position: 'corner',
        isVisible: isResizing,
      }}
      showHandle={isSelected}
      {...props}
    />
  );
};

const SpacerEdit = ({
  attributes,
  isSelected,
  setAttributes,
  toggleSelection,
  context,
  __unstableParentLayout: parentLayout,
}) => {
  const { orientation } = context;
  const { orientation: parentOrientation, type } = parentLayout || {};
  // If the spacer is inside a flex container, it should either inherit the orientation
  // of the parent or use the flex default orientation.
  const inheritedOrientation =
    !parentOrientation && type === 'flex'
      ? 'horizontal'
      : parentOrientation || orientation;
  const { width } = attributes;

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  let height;
  height = attributes.height;

  if (isMobile && attributes.mobile_height) {
    height = attributes.mobile_height;
  } else {
    height = attributes.height;
  }

  if (deviceType !== 'Mobile') {
    height = attributes.height;
  }

  let editorHeight;
  if (!isMobile) {
    editorHeight = attributes.height;
  } else {
    editorHeight = attributes.mobile_height;
  }

  const [isResizing, setIsResizing] = useState(false);
  const [temporaryHeight, setTemporaryHeight] = useState(null);
  const [temporaryWidth, setTemporaryWidth] = useState(null);

  const onResizeStart = () => toggleSelection(false);
  const onResizeStop = () => toggleSelection(true);

  const handleOnVerticalResizeStop = (newHeight) => {
    onResizeStop();

    if (isMobile) {
      setAttributes({ mobile_height: newHeight });
    } else {
      setAttributes({ height: newHeight });
    }

    setTemporaryHeight(null);
  };

  const handleOnHorizontalResizeStop = (newWidth) => {
    onResizeStop();
    setAttributes({ width: newWidth });
    setTemporaryWidth(null);
  };

  const style = {
    backgroundColor: attributes.background,
    height:
      inheritedOrientation === 'horizontal'
        ? 24
        : temporaryHeight || editorHeight || undefined,
    width:
      inheritedOrientation === 'horizontal'
        ? temporaryWidth || width || undefined
        : undefined,
    // In vertical flex containers, the spacer shrinks to nothing without a minimum width.
    minWidth:
      inheritedOrientation === 'vertical' && type === 'flex'
        ? 48
        : undefined,
  };

  const resizableBoxWithOrientation = (blockOrientation) => {
    if (blockOrientation === 'horizontal') {
      return (
        <ResizableSpacer
          minWidth={MIN_SPACER_SIZE}
          enable={{
            top: false,
            right: true,
            bottom: false,
            left: false,
            topRight: false,
            bottomRight: false,
            bottomLeft: false,
            topLeft: false,
          }}
          orientation={blockOrientation}
          onResizeStart={onResizeStart}
          onResize={setTemporaryWidth}
          onResizeStop={handleOnHorizontalResizeStop}
          isSelected={isSelected}
          isResizing={isResizing}
          setIsResizing={setIsResizing}
        />
      );
    }

    return (
      <>
        <ResizableSpacer
          minHeight={MIN_SPACER_SIZE}
          enable={{
            top: false,
            right: false,
            bottom: true,
            left: false,
            topRight: false,
            bottomRight: false,
            bottomLeft: false,
            topLeft: false,
          }}
          orientation={blockOrientation}
          onResizeStart={onResizeStart}
          onResize={setTemporaryHeight}
          onResizeStop={handleOnVerticalResizeStop}
          isSelected={isSelected}
          isResizing={isResizing}
          setIsResizing={setIsResizing}
        />
      </>
    );
  };

  useEffect(() => {
    if (inheritedOrientation === 'horizontal' && !width) {
      setAttributes({
        height: '0px',
        width: '72px',
      });
    }
  }, []);

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: `ng-block`,
    bgcolor: attributes.background,
    style: {
      backgroundColor: attributes.background,
    }
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    height: style.height
  }

  return (
    <>
      <table {...blockProps}>
        <tbody>
          <tr>
            <td className="ng-block-td" height={parseInt(style.height)} style={tdStyle}>
              {resizableBoxWithOrientation(inheritedOrientation)}
            </td>
          </tr>
        </tbody>
      </table>
      {deviceType !== 'Mobile' &&
        <SpacerControls
          attributes={attributes}
          setAttributes={setAttributes}
          height={temporaryHeight || height}
          width={temporaryWidth || width}
          orientation={inheritedOrientation}
          isResizing={isResizing}
        />
      }
      {deviceType === 'Mobile' &&
        <MobileControls
          attributes={attributes}
          setAttributes={setAttributes}
          height={temporaryHeight || height}
          width={temporaryWidth || width}
          orientation={inheritedOrientation}
          isResizing={isResizing}
        />
      }
    </>
  );

};

export default SpacerEdit;