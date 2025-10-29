import { dispatch, select } from '@wordpress/data';

export function applyToAllBlocks(clientId) {

  const currentBlock = select('core/block-editor').getBlock(clientId);

  const strictAttributes = [
    'content',
    'citation',
    'text',
    'posts',
    'hash',
    'custom_data',
    'filter',
    'contentstyle',
    'posts_num',
    'words_num',
    'postlength',
    'columns_num',
    'sortby',
    'dates',
    'hidden_posts',
    'filter_authors',
    'filter_cpts',
    'filter_categories',
    'filter_tags',
    'week_starts',
    'starts_time',
    'two_weeks_starts',
    'month_starts',
    'update_posts',
    'taxonomies',
    'containerWidth',
    'itemBase',
    'div1',
    'div2',
    'embeds',
    'embeds_order',
  ];

  if (currentBlock) {
    const name = currentBlock.name;
    const attributes = currentBlock.attributes;
    const blocks = select('core/block-editor').getBlocks();
    const allBlocks = [];

    let forcelevel = 0;
    if (name === 'newsletterglue/heading') {
      forcelevel = attributes.level;
    }

    blocks.forEach((block) => {
      if (block.name && block.name === name && block.clientId !== clientId) {
        if (!forcelevel || (block.attributes.level && (forcelevel === block.attributes.level))) {
          allBlocks.push(block);
        }
      }
      if (block.innerBlocks.length) {
        block.innerBlocks.forEach((innerblock) => {
          if (innerblock.name && innerblock.name === name && innerblock.clientId !== clientId) {
            if (!forcelevel || (block.attributes.level && (forcelevel === block.attributes.level))) {
              allBlocks.push(innerblock);
            }
          }
        });
      }
    });

    if (allBlocks) {
      let updatedAttributes = [];
      Object.keys(attributes).map(function (key) {
        if (!strictAttributes.includes(key)) {
          updatedAttributes[key] = attributes[key];
        }
      });

      allBlocks.forEach((block) => {
        dispatch('core/block-editor').updateBlockAttributes(block.clientId, updatedAttributes);
      });
    }
  }

}