import {
  Component
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { createRoot } from 'react-dom/client';

import {
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
} from '@wordpress/components';

import { HashRouter, NavLink, Route, Switch } from 'react-router-dom';

import { iconAlert } from '../common/icons';

import Additional from './additional';
import Blocks from './blocks';
import Tools from './tools';
import Connections from './connections';
import CustomCSS from './css';
import License from './license';

export default class Settings extends Component {

  constructor(props) {

    super(props);

    this.changeState = this.changeState.bind(this);

    this.state = {
      notLicensed: nglue_backend.no_license,
      licenseRenew: nglue_backend.license_expires,
      licenseStatus: parseInt(nglue_backend.license_status),
      licenseKey: nglue_backend.license_code,
      licenseConnected: nglue_backend.license_on,
      licenseConnecting: false,
      licenseShowConnect: false,
      licenseTest: nglue_backend.license_test,
      licenseName: nglue_backend.license_name,
      selectedPostTypes: nglue_backend.selectedPostTypes,
      selectedRole: nglue_backend.selectedRole,
      ngSlug: nglue_backend.ngSlug,
      ngDomain: nglue_backend.ngDomain,
      removeCSSFront: nglue_backend.removeCSSFront,
      tier: nglue_backend.license_tier,
      user_perms: nglue_backend.permissions,
      activeRole: 'editor',
    };

  }

  changeState(newstate) {
    this.setState(newstate);
  }

  render() {

    const { notLicensed } = this.state;

    return (
      <>
        <HashRouter>
          <div className="nglue-main">
            <PanelBody className="nglue-tabs">
              <PanelRow>
                <ul>
                  <li><NavLink className="components-button is-link" to="/" exact activeClassName="nglue-active">{__('Connections', 'newsletter-glue')}</NavLink></li>
                  <li><NavLink className="components-button is-link" to="/additional" exact activeClassName="nglue-active">{__('Additional', 'newsletter-glue')}</NavLink></li>
                  <li><NavLink className="components-button is-link" to="/pro" exact activeClassName="nglue-active">{__('Pro license', 'newsletter-glue')}</NavLink></li>
                  { nglue_backend.is_super_admin && (
                    <li>
                      <NavLink className="components-button is-link" to="/tools" exact activeClassName="nglue-active">
                        { __( 'Tools', 'newsletter-glue' ) }
                      </NavLink>
                    </li>
                  ) }
                </ul>
              </PanelRow>
            </PanelBody>
          </div>
          {notLicensed &&
            <div className="nglue-main">
              <PanelBody className="nglue-alert">
                <div className="nglue-alert-icon">{iconAlert}</div>
                <div className="nglue-alert-info">
                  <div className="nglue-alert-title">{__('Add/update your license key', 'newsletter-glue')}</div>
                  <div className="nglue-alert-body">{__('Your license key is invalid, expired or missing. Head to Pro license tab to fix.', 'newsletter-glue')}</div>
                  <div className="nglue-alert-body">{__('Something not right?', 'newsletter-glue')} <a href="https://newsletterglue.com/contact/" target="_blank" rel="noreferrer">{__('Get help', 'newsletter-glue')}</a>.</div>
                </div>
                <div className="nglue-alert-actions">
                  <Button isPrimary href="#/pro">{__('Go to Pro license tab', 'newsletter-glue')}</Button>
                  <div className="nglue-alert-link">
                    <ExternalLink href="https://newsletterglue.com/account">{__('Get license key from My Account', 'newsletter-glue')}</ExternalLink>
                  </div>
                </div>
              </PanelBody>
            </div>
          }
          <Switch>
            <Route exact path="/" render={() => <Connections {...this} getState={this.state} changeState={this.changeState} />} />
            <Route exact path="/pro" render={() => <License {...this} getState={this.state} changeState={this.changeState} />} />
            <Route exact path="/css" render={() => <CustomCSS {...this} getState={this.state} changeState={this.changeState} />} />
            <Route exact path="/blocks" render={() => <Blocks {...this} getState={this.state} changeState={this.changeState} />} />
            <Route exact path="/tools" render={() => <Tools {...this} getState={this.state} changeState={this.changeState} />} />
            <Route exact path="/additional" render={() => <Additional {...this} getState={this.state} changeState={this.changeState} />} />
          </Switch>
        </HashRouter>
      </>
    );

  }

}

var rootElement = document.getElementById('nglue-settings');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<Settings />);
}
