import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
  __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

import { theme } from '../../defaults/theme.js';

export default function save({ attributes }) {
  const { hasFixedLayout, head, body, foot } = attributes;
  const isEmpty = !head.length && !body.length && !foot.length;

  if (isEmpty) {
    return null;
  }

  const colorProps = getColorClassesAndStyles(attributes);
  const borderProps = getBorderClassesAndStyles(attributes);

  const classes = classnames(colorProps.className, borderProps.className, {
    'has-fixed-layout': hasFixedLayout,
  });

  var color = attributes.color ? attributes.color : theme.color;

  const padding = attributes.padding;

  const Section = ({ type, rows }) => {
    if (!rows.length) {
      return null;
    }

    const Tag = `t${type}`;

    var i = 0;

    return (
      <Tag>
        {rows.map(({ cells }, rowIndex) => {

          var isStriped = false;
          if (type === 'body' && (i++ % 2)) {
            isStriped = true;
          }

          return (
            <tr key={rowIndex}>
              {cells.map(
                (
                  {
                    content,
                    tag,
                    scope,
                    align,
                    colspan,
                    rowspan,
                  },
                  cellIndex
                ) => {
                  const cellClasses = classnames({
                    'ng-block-td': true,
                  });

                  var defaultAlign = 'left';

                  var cellStyle = {
                    fontFamily: nglue_backend.font_names[attributes.font.key],
                    textAlign: align ? align : defaultAlign,
                    fontSize: attributes.fontsize,
                    lineHeight: attributes.lineheight,
                    color: color,
                    backgroundColor: attributes.background,
                    borderWidth: attributes.hasBorder ? '1px' : '0px',
                    borderStyle: 'solid',
                    borderColor: attributes.border ? attributes.border : attributes.color,
                    paddingTop: padding.top,
                    paddingBottom: padding.bottom,
                    paddingLeft: padding.left,
                    paddingRight: padding.right
                  }

                  if (type === 'head' && attributes.backgroundhead) {
                    cellStyle['backgroundColor'] = attributes.backgroundhead;
                  }

                  if (type === 'foot' && attributes.backgroundfoot) {
                    cellStyle['backgroundColor'] = attributes.backgroundfoot;
                  }

                  if (isStriped && attributes.style === 'stripes') {
                    cellStyle['backgroundColor'] = attributes.background2;
                  }

                  return (
                    <RichText.Content
                      align={align ? align : defaultAlign}
                      style={cellStyle}
                      className={
                        cellClasses
                          ? cellClasses
                          : undefined
                      }
                      data-align={align}
                      tagName={tag}
                      value={content}
                      key={cellIndex}
                      scope={
                        tag === 'th' ? scope : undefined
                      }
                      colSpan={colspan}
                      rowSpan={rowspan}
                    />
                  );
                }
              )}
            </tr>
          );
        })}
      </Tag>
    );
  };

  var tableStyles = { borderCollapse: 'collapse' };
  if (hasFixedLayout) {
    tableStyles['width'] = '100%';
    tableStyles['tableLayout'] = 'fixed';
  }

  const margin = attributes.margin;

  return (
    <figure {...useBlockProps.save({ className: 'ng-block' })}>
      <div className="ng-table-wrapper ng-block" style={{ paddingTop: margin.top, paddingBottom: margin.bottom, paddingLeft: margin.left, paddingRight: margin.right, border: 'none' }}>
        <table
          width="100%"
          cellPadding={0}
          cellSpacing={0}
          className={classes === '' ? undefined : classes}
          style={tableStyles}
        >
          <Section type="head" rows={head} />
          <Section type="body" rows={body} />
          <Section type="foot" rows={foot} />
        </table>
      </div>
    </figure>
  );
}
