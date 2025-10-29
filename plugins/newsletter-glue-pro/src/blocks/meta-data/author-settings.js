import React from 'react';

import { MediaUpload } from '@wordpress/block-editor';
import { image as imageIcon } from "@wordpress/icons";

import {
  BaseControl,
  Button
} from '@wordpress/components';

export function AuthorSettings(props) {

  const { attrs, setAttributes } = props;

  let attributes = attrs;

  var onSelectImage = function (media) {
    return setAttributes({ profile_pic: media.url });
  };

  var removeImage = function () {
    setAttributes({ profile_pic: newsletterglue_meta.profile_pic });
  };

  return (
    <BaseControl className="ngl-base--flex">
      <MediaUpload
        onSelect={onSelectImage}
        type="image"
        render={function (obj) {
          return <>
            <Button
              onClick={obj.open}
              icon={imageIcon}
              variant="link"
            >Change profile picture</Button>
            {attributes.profile_pic && (attributes.profile_pic != newsletterglue_meta.profile_pic) && (
              <Button
                onClick={removeImage}
                variant="link"
                isDestructive
              >Reset</Button>)}
          </>
        }}
      />
    </BaseControl>
  );
}