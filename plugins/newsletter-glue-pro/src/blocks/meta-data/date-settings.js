import React from 'react';

import { __ } from '@wordpress/i18n';

import * as wpDate from "@wordpress/date";

import {
  SelectControl,
} from '@wordpress/components';

export function DateSettings(props) {

  const { attrs, setAttributes } = props;

  let attributes = attrs;

  const post = wp.data.select("core/editor").getCurrentPost();

  var dateFormats = [
    { value: 'l, j M Y', label: wpDate.format('l, j M Y', post.date) },
    { value: 'F j, Y', label: wpDate.format('F j, Y', post.date) },
    { value: 'j M Y', label: wpDate.format('j M Y', post.date) },
    { value: 'Y-m-d', label: wpDate.format('Y-m-d', post.date) },
    { value: 'm/d/Y', label: wpDate.format('m/d/Y', post.date) },
    { value: 'd/m/Y', label: wpDate.format('d/m/Y', post.date) }
  ];

  return (
    <SelectControl
      label={__('Date format')}
      value={attributes.date_format}
      onChange={(value) => setAttributes({ date_format: value })}
      options={dateFormats}
    />
  );
}