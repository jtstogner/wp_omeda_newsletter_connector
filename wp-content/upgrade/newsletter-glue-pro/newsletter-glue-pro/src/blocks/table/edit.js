import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  AlignmentControl,
  BlockControls,
  BlockIcon,
  InspectorControls,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  Button,
  PanelBody,
  Placeholder,
  TextControl,
  ToggleControl,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
  ToolbarDropdownMenu,
} from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
  alignCenter,
  alignLeft,
  alignRight,
  blockTable as icon,
  table,
  tableColumnAfter,
  tableColumnBefore,
  tableColumnDelete,
  tableRowAfter,
  tableRowBefore,
  tableRowDelete,
} from '@wordpress/icons';

import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import {
  createTable,
  deleteColumn,
  deleteRow,
  getCellAttribute,
  insertColumn,
  insertRow,
  isEmptyTableSection,
  toggleSection,
  updateSelectedCell,
} from './state';

import { theme } from '../../defaults/theme.js';
import { Controls } from './controls.js';
import { MobileControls } from './mobile-controls.js';

const ALIGNMENT_CONTROLS = [
  {
    icon: alignLeft,
    title: __('Align column left'),
    align: 'left',
  },
  {
    icon: alignCenter,
    title: __('Align column center'),
    align: 'center',
  },
  {
    icon: alignRight,
    title: __('Align column right'),
    align: 'right',
  },
];

const cellAriaLabel = {
  head: __('Header cell text'),
  body: __('Body cell text'),
  foot: __('Footer cell text'),
};

const placeholder = {
  head: __('Header label'),
  foot: __('Footer label'),
};

function TSection({ name, ...props }) {
  const TagName = `t${name}`;
  return <TagName {...props} />;
}

function TableEdit({
  attributes,
  setAttributes,
  className,
  isSelected,
  clientId
}) {

  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';

  const { hasFixedLayout, head, foot } = attributes;
  const [initialRowCount, setInitialRowCount] = useState(2);
  const [initialColumnCount, setInitialColumnCount] = useState(2);
  const [selectedCell, setSelectedCell] = useState();

  const tableRef = useRef();
  const [hasTableCreated, setHasTableCreated] = useState(false);

  /**
   * Updates the initial column count used for table creation.
   *
   * @param {number} count New initial column count.
   */
  function onChangeInitialColumnCount(count) {
    setInitialColumnCount(count);
  }

  /**
   * Updates the initial row count used for table creation.
   *
   * @param {number} count New initial row count.
   */
  function onChangeInitialRowCount(count) {
    setInitialRowCount(count);
  }

  /**
   * Creates a table based on dimensions in local state.
   *
   * @param {Object} event Form submit event.
   */
  function onCreateTable(event) {
    event.preventDefault();

    setAttributes(
      createTable({
        rowCount: parseInt(initialRowCount, 10) || 2,
        columnCount: parseInt(initialColumnCount, 10) || 2,
      })
    );
    setHasTableCreated(true);
  }

  /**
   * Toggles whether the table has a fixed layout or not.
   */
  function onChangeFixedLayout() {
    setAttributes({ hasFixedLayout: !hasFixedLayout });
  }

  /**
   * Changes the content of the currently selected cell.
   *
   * @param {Array} content A RichText content value.
   */
  function onChange(content) {
    if (!selectedCell) {
      return;
    }

    setAttributes(
      updateSelectedCell(
        attributes,
        selectedCell,
        (cellAttributes) => ({
          ...cellAttributes,
          content,
        })
      )
    );
  }

  /**
   * Align text within the a column.
   *
   * @param {string} align The new alignment to apply to the column.
   */
  function onChangeColumnAlignment(align) {
    if (!selectedCell) {
      return;
    }

    // Convert the cell selection to a column selection so that alignment
    // is applied to the entire column.
    const columnSelection = {
      type: 'column',
      columnIndex: selectedCell.columnIndex,
    };

    const newAttributes = updateSelectedCell(
      attributes,
      columnSelection,
      (cellAttributes) => ({
        ...cellAttributes,
        align,
      })
    );
    setAttributes(newAttributes);
  }

  /**
   * Get the alignment of the currently selected cell.
   *
   * @return {string | undefined} The new alignment to apply to the column.
   */
  function getCellAlignment() {
    if (!selectedCell) {
      return;
    }

    return getCellAttribute(attributes, selectedCell, 'align');
  }

  /**
   * Add or remove a `head` table section.
   */
  function onToggleHeaderSection() {
    setAttributes(toggleSection(attributes, 'head'));
  }

  /**
   * Add or remove a `foot` table section.
   */
  function onToggleFooterSection() {
    setAttributes(toggleSection(attributes, 'foot'));
  }

  /**
   * Inserts a row at the currently selected row index, plus `delta`.
   *
   * @param {number} delta Offset for selected row index at which to insert.
   */
  function onInsertRow(delta) {
    if (!selectedCell) {
      return;
    }

    const { sectionName, rowIndex } = selectedCell;
    const newRowIndex = rowIndex + delta;

    setAttributes(
      insertRow(attributes, {
        sectionName,
        rowIndex: newRowIndex,
      })
    );
    // Select the first cell of the new row.
    setSelectedCell({
      sectionName,
      rowIndex: newRowIndex,
      columnIndex: 0,
      type: 'cell',
    });
  }

  /**
   * Inserts a row before the currently selected row.
   */
  function onInsertRowBefore() {
    onInsertRow(0);
  }

  /**
   * Inserts a row after the currently selected row.
   */
  function onInsertRowAfter() {
    onInsertRow(1);
  }

  /**
   * Deletes the currently selected row.
   */
  function onDeleteRow() {
    if (!selectedCell) {
      return;
    }

    const { sectionName, rowIndex } = selectedCell;

    setSelectedCell();
    setAttributes(deleteRow(attributes, { sectionName, rowIndex }));
  }

  /**
   * Inserts a column at the currently selected column index, plus `delta`.
   *
   * @param {number} delta Offset for selected column index at which to insert.
   */
  function onInsertColumn(delta = 0) {
    if (!selectedCell) {
      return;
    }

    const { columnIndex } = selectedCell;
    const newColumnIndex = columnIndex + delta;

    setAttributes(
      insertColumn(attributes, {
        columnIndex: newColumnIndex,
      })
    );
    // Select the first cell of the new column.
    setSelectedCell({
      rowIndex: 0,
      columnIndex: newColumnIndex,
      type: 'cell',
    });
  }

  /**
   * Inserts a column before the currently selected column.
   */
  function onInsertColumnBefore() {
    onInsertColumn(0);
  }

  /**
   * Inserts a column after the currently selected column.
   */
  function onInsertColumnAfter() {
    onInsertColumn(1);
  }

  /**
   * Deletes the currently selected column.
   */
  function onDeleteColumn() {
    if (!selectedCell) {
      return;
    }

    const { sectionName, columnIndex } = selectedCell;

    setSelectedCell();
    setAttributes(
      deleteColumn(attributes, { sectionName, columnIndex })
    );
  }

  useEffect(() => {
    if (!isSelected) {
      setSelectedCell();
    }
  }, [isSelected]);

  useEffect(() => {
    if (hasTableCreated) {
      tableRef?.current
        ?.querySelector('td[contentEditable="true"]')
        ?.focus();
      setHasTableCreated(false);
    }
  }, [hasTableCreated]);

  const sections = ['head', 'body', 'foot'].filter(
    (name) => !isEmptyTableSection(attributes[name])
  );

  const tableControls = [
    {
      icon: tableRowBefore,
      title: __('Insert row before'),
      isDisabled: !selectedCell,
      onClick: onInsertRowBefore,
    },
    {
      icon: tableRowAfter,
      title: __('Insert row after'),
      isDisabled: !selectedCell,
      onClick: onInsertRowAfter,
    },
    {
      icon: tableRowDelete,
      title: __('Delete row'),
      isDisabled: !selectedCell,
      onClick: onDeleteRow,
    },
    {
      icon: tableColumnBefore,
      title: __('Insert column before'),
      isDisabled: !selectedCell,
      onClick: onInsertColumnBefore,
    },
    {
      icon: tableColumnAfter,
      title: __('Insert column after'),
      isDisabled: !selectedCell,
      onClick: onInsertColumnAfter,
    },
    {
      icon: tableColumnDelete,
      title: __('Delete column'),
      isDisabled: !selectedCell,
      onClick: onDeleteColumn,
    },
  ];

  var color = attributes.color ? attributes.color : theme.color;

  var i = 0;

  const padding = isMobile ? attributes.mobile_padding : attributes.padding;

  const renderedSections = sections.map((name) => (
    <TSection name={name} key={name}>
      {attributes[name].map(({ cells }, rowIndex) => {

        var isStriped = false;
        if (name === 'body' && (i++ % 2)) {
          isStriped = true;
        }

        return (
          <tr key={rowIndex}>
            {cells.map(
              (
                {
                  content,
                  tag: CellTag,
                  scope,
                  align,
                  colspan,
                  rowspan,
                },
                columnIndex
              ) => {

                var defaultAlign = 'left';

                var cellStyle = {
                  fontFamily: nglue_backend.font_names[attributes.font.key],
                  textAlign: align ? align : defaultAlign,
                  fontSize: isMobile ? attributes.mobile_size : attributes.fontsize,
                  lineHeight: isMobile ? attributes.mobile_lineheight : attributes.lineheight,
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

                if (name === 'head' && attributes.backgroundhead) {
                  cellStyle['backgroundColor'] = attributes.backgroundhead;
                }

                if (name === 'foot' && attributes.backgroundfoot) {
                  cellStyle['backgroundColor'] = attributes.backgroundfoot;
                }

                if (isStriped && attributes.style === 'stripes') {
                  cellStyle['backgroundColor'] = attributes.background2;
                }

                return (
                  <RichText
                    tagName={CellTag}
                    key={columnIndex}
                    align={align ? align : defaultAlign}
                    style={cellStyle}
                    className={classnames(
                      {
                      },
                      'ng-block-td',
                      'wp-block-table__cell-content'
                    )}
                    scope={CellTag === 'th' ? scope : undefined}
                    colSpan={colspan}
                    rowSpan={rowspan}
                    value={content}
                    onChange={onChange}
                    onFocus={() => {
                      setSelectedCell({
                        sectionName: name,
                        rowIndex,
                        columnIndex,
                        type: 'cell',
                      });
                    }}
                    aria-label={cellAriaLabel[name]}
                    placeholder={placeholder[name]}
                  />
                )
              }
            )}
          </tr>
        );
      }
      )}
    </TSection>
  ));

  const isEmpty = !sections.length;

  var tableStyles = { borderCollapse: 'collapse' };
  if (hasFixedLayout) {
    tableStyles['width'] = '100%';
    tableStyles['tableLayout'] = 'fixed';
  }

  const margin = isMobile ? attributes.mobile_margin : attributes.margin;

  return (
    <figure {...useBlockProps({ ref: tableRef, className: 'ng-block' })}>
      {!isEmpty && (
        <>
          <BlockControls group="block">
            <AlignmentControl
              label={__('Change column alignment')}
              alignmentControls={ALIGNMENT_CONTROLS}
              value={getCellAlignment()}
              onChange={(nextAlign) =>
                onChangeColumnAlignment(nextAlign)
              }
            />
          </BlockControls>
          <BlockControls group="other">
            <ToolbarDropdownMenu
              hasArrowIndicator
              icon={table}
              label={__('Edit table')}
              controls={tableControls}
            />
          </BlockControls>
        </>
      )}
      <InspectorControls>
        {deviceType !== 'Mobile' &&
          <>
            <PanelBody
              title={__('Styles')}
              className="blocks-table-settings"
            >
              <ToggleGroupControl
                value={attributes.style}
                onChange={(newStyle) => setAttributes({ style: newStyle })}
                isBlock
              >
                <ToggleGroupControlOption
                  value="default"
                  label={__('Default')}
                />
                <ToggleGroupControlOption
                  value="stripes"
                  label={__('Stripes')}
                />
              </ToggleGroupControl>
            </PanelBody>

            <PanelBody
              title={__('Settings')}
              className="blocks-table-settings"
            >
              <ToggleControl
                __nextHasNoMarginBottom
                label={__('Fixed width table cells')}
                checked={!!hasFixedLayout}
                onChange={onChangeFixedLayout}
              />
              {!isEmpty && (
                <>
                  <ToggleControl
                    __nextHasNoMarginBottom
                    label={__('Header section')}
                    checked={!!(head && head.length)}
                    onChange={onToggleHeaderSection}
                  />
                  <ToggleControl
                    __nextHasNoMarginBottom
                    label={__('Footer section')}
                    checked={!!(foot && foot.length)}
                    onChange={onToggleFooterSection}
                  />
                  <ToggleControl
                    __nextHasNoMarginBottom
                    label={__('Table borders')}
                    checked={attributes.hasBorder}
                    onChange={(val) => {
                      setAttributes({ hasBorder: val });
                    }}
                  />
                </>
              )}
            </PanelBody>
          </>
        }
        {deviceType !== 'Mobile' &&
          <Controls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
        {deviceType === 'Mobile' &&
          <MobileControls attributes={attributes} setAttributes={setAttributes} className={className} isSelected={isSelected} clientId={clientId} />
        }
      </InspectorControls>
      {!isEmpty && (
        <div className="ng-table-wrapper ng-block" style={{ paddingTop: margin.top, paddingBottom: margin.bottom, paddingLeft: margin.left, paddingRight: margin.right, border: 'none' }}>
          <table
            width="100%"
            cellPadding={0}
            cellSpacing={0}
            className={classnames(
              {
                'has-fixed-layout': hasFixedLayout,
              }
            )}
            style={tableStyles}
          >
            {renderedSections}
          </table>
        </div>
      )}

      {isEmpty && (
        <Placeholder
          label={__('Table')}
          icon={<BlockIcon icon={icon} showColors />}
          instructions={__('Insert a table for sharing data.')}
        >
          <form
            className="blocks-table__placeholder-form"
            onSubmit={onCreateTable}
          >
            <TextControl
              __nextHasNoMarginBottom
              type="number"
              label={__('Column count')}
              value={initialColumnCount}
              onChange={onChangeInitialColumnCount}
              min="1"
              className="blocks-table__placeholder-input"
            />
            <TextControl
              __nextHasNoMarginBottom
              type="number"
              label={__('Row count')}
              value={initialRowCount}
              onChange={onChangeInitialRowCount}
              min="1"
              className="blocks-table__placeholder-input"
            />
            <Button
              className="blocks-table__placeholder-button"
              variant="primary"
              type="submit"
            >
              {__('Create Table')}
            </Button>
          </form>
        </Placeholder>
      )}
    </figure>
  );
}

export default TableEdit;
