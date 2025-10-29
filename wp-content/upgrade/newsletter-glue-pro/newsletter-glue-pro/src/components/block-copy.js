import { __ } from '@wordpress/i18n';
import React from 'react';

import { Button } from '@wordpress/components';

import { applyToAllBlocks } from '../hooks/apply-block-attributes';

export function getBlockTitleByName(name) {
  let string = name.replace('newsletterglue/', '');

  string = string.charAt(0).toUpperCase() + string.slice(1);
  string = string.replace('-', ' ');

  if (string === 'Showhide') {
    string = 'Show/hide';
  }

  if (string === 'Optin') {
    string = 'Subscriber form';
  }

  return string;
}

export const BlockCopy = props => {

  const { clientId, name } = props;

  let title = getBlockTitleByName(name);

  return (
    <div>
      <p>
        Do you want to apply these settings to all visible <strong>{title}</strong> blocks in this <strong>Campaign</strong>?
      </p>
      <Button
        className='ng-component-button'
        onClick={() => applyToAllBlocks(clientId)}
        variant="secondary"
        text={__('Apply')}
        size="small"
      />
    </div>
  );

}