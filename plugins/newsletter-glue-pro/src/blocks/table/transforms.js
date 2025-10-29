/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { normalizeRowColSpan } from './utils';

let blockName = 'core/table';

if (nglue_backend.is_allowed_post_type) {
  if (nglue_backend.core_post_type === 'no') {
    blockName = 'newsletterglue/table';
  }
}

const tableContentPasteSchema = ({ phrasingContentSchema }) => ({
  tr: {
    allowEmpty: true,
    children: {
      th: {
        allowEmpty: true,
        children: phrasingContentSchema,
        attributes: ['scope', 'colspan', 'rowspan'],
      },
      td: {
        allowEmpty: true,
        children: phrasingContentSchema,
        attributes: ['colspan', 'rowspan'],
      },
    },
  },
});

const tablePasteSchema = (args) => ({
  table: {
    children: {
      thead: {
        allowEmpty: true,
        children: tableContentPasteSchema(args),
      },
      tfoot: {
        allowEmpty: true,
        children: tableContentPasteSchema(args),
      },
      tbody: {
        allowEmpty: true,
        children: tableContentPasteSchema(args),
      },
    },
  },
});

const transforms = {
  from: [
    {
      type: 'raw',
      selector: 'table',
      schema: tablePasteSchema,
      transform: (node) => {
        const attributes = Array.from(node.children).reduce(
          (sectionAcc, section) => {
            if (!section.children.length) {
              return sectionAcc;
            }

            const sectionName = section.nodeName
              .toLowerCase()
              .slice(1);

            const sectionAttributes = Array.from(
              section.children
            ).reduce((rowAcc, row) => {
              if (!row.children.length) {
                return rowAcc;
              }

              const rowAttributes = Array.from(
                row.children
              ).reduce((colAcc, col) => {
                const rowspan = normalizeRowColSpan(
                  col.getAttribute('rowspan')
                );
                const colspan = normalizeRowColSpan(
                  col.getAttribute('colspan')
                );

                colAcc.push({
                  tag: col.nodeName.toLowerCase(),
                  content: col.innerHTML,
                  rowspan,
                  colspan,
                });

                return colAcc;
              }, []);

              rowAcc.push({
                cells: rowAttributes,
              });

              return rowAcc;
            }, []);

            sectionAcc[sectionName] = sectionAttributes;
            return sectionAcc;
          },
          {}
        );

        return createBlock(blockName, attributes);
      },
    },
  ],
};

export default transforms;
