import React from 'react';

import {
  Component
} from '@wordpress/element';

import { __ } from '@wordpress/i18n';

import {
  Button
} from '@wordpress/components';

import { iconCheck } from '../common/icons';

export default class UpgradeNotice extends Component {


  render() {

    var tierClass = this.props.isExtended ? 'nglue-tier-notice nglue-tier-notice-extended' : 'nglue-tier-notice';

    return <>
      <div className="nglue-tier-lock"></div>
      <div className={tierClass}>
        <div className="nglue-tier-notice-info">
          <div className="nglue-tier-notice-title">{__('Upgrade to unlock', 'newsletter-glue')} {this.props.title}</div>
          <div className="nglue-tier-notice-desc">{__('Log into your account to see pro-rated pricing and upgrade.', 'newsletter-glue')}</div>
          <div className="nglue-tier-notice-btn">
            <Button
              isPrimary
              href={nglue_backend.upgrade_link}
              target="_blank"
            >
              {__('Upgrade my account', 'newsletter-glue')}
            </Button>
          </div>
        </div>
        {this.props.isExtended &&
          <div className="nglue-tier-notice-extras">
            <div className="nglue-tier-notice-desc">Here are the new features you will get when you upgrade:</div>
            <div className="nglue-tier-features">
              <div>{iconCheck} Access all our email integrations</div>
              <div>{iconCheck} Static site compatibility</div>
              <div>{iconCheck} User roles & permissions management for editorial teams</div>
              <div>{iconCheck} Pro-rated pricing</div>
            </div>
          </div>
        }
      </div>
    </>;

  }

}