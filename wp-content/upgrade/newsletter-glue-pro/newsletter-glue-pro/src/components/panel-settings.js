import { PanelRow, RadioControl, TextControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import React from 'react';

export function getMeta(metakey) {
  let value;
  if (['title'].includes(metakey)) {
    value = useSelect((select) => select('core/editor').getEditedPostAttribute(metakey));
  } else {
    value = useSelect((select) => select('core/editor').getEditedPostAttribute('meta')[metakey]);
  }
  return value;
}

export const PanelSettings = (props) => {

  const { editPost } = useDispatch('core/editor');
  const { settings } = props;

  return (
    <>
      {settings.map(function (item, i) {

        return (
          <PanelRow key={`item-${i}`}>
            {item.type === 'radio' && <RadioControl
              label={item.label ? item.label : undefined}
              help={item.help ? item.help : undefined}
              selected={getMeta(item.metakey) || item.fallback}
              options={item.options}
              onChange={(value) => {
                item.is_meta ? editPost({ meta: { [item.metakey]: value } }) : editPost({ [item.metakey]: value });
              }}
            />
            }
            {item.type === 'text' && <TextControl
              id={`newsletterglue_${item.metakey}`}
              label={item.label ? item.label : undefined}
              help={item.help ? item.help : undefined}
              value={getMeta(item.metakey)}
              onChange={(value) => {
                item.is_meta ? editPost({ meta: { [item.metakey]: value } }) : editPost({ [item.metakey]: value });
              }}
              placeholder={item.placeholder_from_post ? getMeta(item.placeholder_from_post) : item.placeholder}
            />
            }
          </PanelRow>
        );
      })}
    </>
  );

};