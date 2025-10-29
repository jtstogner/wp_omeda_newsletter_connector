import classnames from 'classnames';
import React from 'react';

import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, ToggleControl } from '@wordpress/components';
import { dispatch, select, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

import { icon } from './icon.js';
import { layoutOptions } from './layout-options.js';

export default function Edit({ attributes, setAttributes, className, isSelected, clientId }) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  const classes = classnames({
    'ng-block': true,
    'is-stacked-on-mobile': isMobile && attributes.stacked_on_mobile
  });

  var attrs = {
    width: '100%',
    cellPadding: 0,
    cellSpacing: 0,
    className: classes,
    bgcolor: attributes.background,
    style: {
      backgroundColor: attributes.background,
    },
  };

  const blockProps = useBlockProps(attrs);

  const tdStyle = {
    paddingTop: isMobile ? attributes.mobile_padding.top : attributes.padding.top,
    paddingBottom: isMobile ? attributes.mobile_padding.bottom : attributes.padding.bottom,
    paddingLeft: isMobile ? attributes.mobile_padding.left : attributes.padding.left,
    paddingRight: isMobile ? attributes.mobile_padding.right : attributes.padding.right,
  }

  function changeLayout(val) {
    let threshold = 600;

    if (attributes.padding.left) {
      threshold = threshold - parseInt(attributes.padding.left);
    }

    if (attributes.padding.right) {
      threshold = threshold - parseInt(attributes.padding.right);
    }

    var layoutProps = val.split('_');
    let children = [];
    if (select('core/block-editor').getBlocksByClientId(clientId)[0] && select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks) {
      children = select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
    }

    if (children.length) {
      children.forEach(function (child, i) {
        if (layoutProps[i]) {
          let width = (layoutProps[i] / 100) * threshold;
          dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: parseInt(width), originalWidth: parseInt(width) });
        } else {
          dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: undefined, originalWidth: undefined });
        }
      });
    } else {
      setTimeout(function () {
        if (select('core/block-editor').getBlocksByClientId(clientId)[0] && select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks) {
          const children = select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
          children.forEach(function (child, i) {
            if (layoutProps[i]) {
              let width = (layoutProps[i] / 100) * threshold;
              dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: parseInt(width), originalWidth: parseInt(width) });
            } else {
              dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: undefined, originalWidth: undefined });
            }
          });
        }
      }, 500);
    }

    setAttributes({ layout: val });
  }

  useEffect(() => {
    if (attributes.layout) {

      let threshold = 600;

      if (attributes.padding.left) {
        threshold = threshold - parseInt(attributes.padding.left);
      }

      if (attributes.padding.right) {
        threshold = threshold - parseInt(attributes.padding.right);
      }

      var layoutProps = attributes.layout.split('_');
      let children = [];
      if (select('core/block-editor').getBlocksByClientId(clientId)[0] && select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks) {
        children = select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;
      }
      if (children.length) {
        children.forEach(function (child, i) {
          if (layoutProps[i]) {
            let width = (layoutProps[i] / 100) * threshold;
            dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: parseInt(width), originalWidth: parseInt(width) });
          } else {
            dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: undefined, originalWidth: undefined });
          }
        });
      }

    }

  }, [attributes.padding]);

  const displayLayoutOptions = layoutOptions.map((option, i) => {
    return <div key={`ng-option-group-${i}`}>
      <div className="ng-block-group-title">{option.group}</div>
      <div className="ng-block-grouped">
        {
          option.options.map((info, i) => {
            let optionLayout;
            if (info.first) {
              optionLayout = info.first;
            }
            if (info.second) {
              optionLayout = optionLayout + ' : ' + info.second;
            }
            if (info.third) {
              optionLayout = optionLayout + ' : ' + info.third;
            }
            return <div className={`ng__column_option${attributes.layout === info.layout ? ' ng__column_option_active' : ''}`} key={`ng-option-key-${i}`} data-layout={optionLayout} onClick={() => changeLayout(info.layout)}>
              {info.first && <span style={{ width: info.first + '%' }}></span>}
              {info.second && <span style={{ width: info.second + '%' }}></span>}
              {info.third && <span style={{ width: info.third + '%' }}></span>}
            </div>
          })
        }
      </div>
    </div>;
  });

  const allowedBlocks = ['newsletterglue/section'];

  let getBlockTemplate = [
    ['newsletterglue/section', {}],
    ['newsletterglue/section', {}],
    ['newsletterglue/section', {}]
  ];

  const { children } = useInnerBlocksProps([], {
    allowedBlocks: allowedBlocks,
    template: getBlockTemplate,
    templateLock: false,
    orientation: "horizontal"
  });

  return (
    <>
      <InspectorControls>
        {attributes.layout && deviceType !== 'Mobile' && (
          <PanelBody title={__('Layout')}>
            {displayLayoutOptions}
          </PanelBody>
        )}
        {attributes.layout && deviceType === 'Mobile' && (
          <PanelBody title={__('Layout')}>
            <PanelRow>
              <ToggleControl
                label={__('Stack columns on mobile', 'newsletter-glue')}
                checked={attributes.stacked_on_mobile}
                onChange={(value) => {
                  setAttributes({ stacked_on_mobile: value });
                }}
              />
            </PanelRow>
          </PanelBody>
        )}
        {attributes.layout && deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {attributes.layout && deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>

      <table {...blockProps}>
        <tbody>
          {!attributes.layout && (
            <tr>
              <td>
                <div className="components-placeholder wp-block-columns is-large">
                  <div className="components-placeholder__label">
                    <span className="block-editor-block-icon has-colors">{icon}</span> Columns
                  </div>
                  <fieldset className="components-placeholder__fieldset">
                    <legend className="components-placeholder__instructions">Select a layout to begin. You can always change this later.</legend>
                    <form>
                      {displayLayoutOptions}
                    </form>
                  </fieldset>
                </div>
              </td>
            </tr>
          )}
          {attributes.layout && (
            <tr>
              <td className="ng-columns-wrap" style={tdStyle}>
                <table width="100%" cellPadding="0" cellSpacing="0">
                  <tbody>
                    <tr>
                      {children}
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </>
  );

}