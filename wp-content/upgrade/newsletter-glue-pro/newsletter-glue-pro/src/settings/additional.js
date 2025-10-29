import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  Component
} from '@wordpress/element';

import {
  BaseControl,
  Button,
  CheckboxControl,
  ExternalLink,
  PanelBody,
  PanelRow,
} from '@wordpress/components';

import { TextControl } from '@wordpress/components';


import Select from 'react-select';

import { iconCheck, iconChevronDown, iconChevronUp } from '../common/icons';

const iconUri = nglue_backend.images_uri + 'icon-additional.svg';
const wizardIcon = nglue_backend.images_uri + 'icon-wizard.svg';
const postTypesIcon = nglue_backend.images_uri + 'icon-types.svg';
const slugsIcon = nglue_backend.images_uri + 'icon-slugs.svg';
const frontcssIcon = nglue_backend.images_uri + 'icon-frontcss.svg';
const rootIcon = nglue_backend.images_uri + 'icon-url.svg';
const rolesIcon = nglue_backend.images_uri + 'icon-roles.svg';
const adInserterIcon = nglue_backend.images_uri + 'icon-ad-inserter.svg';

import UpgradeNotice from '../common/upgrade-notice';

export default class Additional extends Component {

  constructor(props) {

    super(props);

    this.savePostTypes = this.savePostTypes.bind(this);
    this.saveSlug = this.saveSlug.bind(this);
    this.saveCSS = this.saveCSS.bind(this);
    this.saveDomain = this.saveDomain.bind(this);
    this.savePerms = this.savePerms.bind(this);
    this.resetRole = this.resetRole.bind(this);
    this.resetRoles = this.resetRoles.bind(this);
    this.saveAdInserter = this.saveAdInserter.bind(this);
    this.testBroadstreetConnection = this.testBroadstreetConnection.bind(this);

    const data = {
      isSaving1: false,
      isSaving2: false,
      isSaving3: false,
      isSaving4: false,
      isSaving5: false,
      isSaving6: false,
      hasSaved1: false,
      hasSaved2: false,
      hasSaved3: false,
      hasSaved4: false,
      hasSaved5: false,
      hasSaved6: false,
      activeAdIntegration: nglue_backend.ad_inserter_active_integration ? 
        nglue_backend.ad_inserter_integration.find(option => 
          option.value === nglue_backend.ad_inserter_active_integration.id
        ) : null,
      unsavedChanges1: false,
      unsavedChanges2: false,
      unsavedChanges3: false,
      unsavedChanges4: false,
      unsavedChanges5: false,
      unsavedChanges6: false,
      isInvalidSlug: false,
      isInvalidDomain: false,
      resetAlltoggle: false,
      resetRoletoggle: false,
      isTesting: false,
      testSuccess: false,
      testFailed: false,
      broadstreetAccessToken: '',
      hasConnection: false,
      networkName: '',
    };

    this.state = data;

  }

  componentDidMount() {
    // Check if there's an existing Broadstreet access token and connection
    if (nglue_backend.broadstreet_access_token && nglue_backend.broadstreet_has_connection) {
      this.setState({
        broadstreetAccessToken: nglue_backend.broadstreet_access_token,
        hasConnection: true
      }, () => {
        // Fetch network name after setting the state
        this.fetchNetworkName();
      });
    } else if (nglue_backend.broadstreet_access_token) {
      // We have a token but need to verify the connection
      this.setState({
        broadstreetAccessToken: nglue_backend.broadstreet_access_token
      }, () => {
        // Verify the connection status
        wp.apiRequest({
          path: '/newsletter-glue/v1/broadstreet/verify-connection',
          method: 'GET'
        }).then(response => {
          if (response && response.success) {
            this.setState({
              hasConnection: true
            }, () => {
              this.fetchNetworkName();
            });
          }
        }).catch(error => {
          console.error('Error verifying Broadstreet connection', error);
        });
      });
    }
  }
  
  fetchNetworkName() {
    wp.apiRequest({
      path: '/newsletter-glue/v1/broadstreet/get-network-name',
      method: 'GET'
    }).then(response => {
      if (response) {
        this.setState({
          networkName: response
        });
      }
    }).catch(error => {
      console.error('Error fetching Broadstreet network name', error);
    });
  }

  savePostTypes() {

    const { selectedPostTypes } = this.props.getState;

    this.setState({
      isSaving1: true,
    });

    const data = {
      post_types: selectedPostTypes
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_post_types',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving1: false,
          hasSaved1: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved1: false,
          unsavedChanges1: false,
        });
      }, 2500);

    });

  }

  saveSlug() {

    const { ngSlug } = this.props.getState;

    this.setState({
      isSaving2: true,
    });

    const data = {
      ngSlug: ngSlug
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_custom_slug',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving2: false,
          hasSaved2: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved2: false,
          unsavedChanges2: false,
        });
      }, 2500);

    });

  }

  saveCSS() {

    const { removeCSSFront } = this.props.getState;

    this.setState({
      isSaving5: true,
    });

    const data = {
      removeCSSFront: removeCSSFront
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_css_options',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving5: false,
          hasSaved5: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved5: false,
          unsavedChanges5: false,
        });
      }, 2500);

    });

  }

  saveDomain() {

    const { ngDomain } = this.props.getState;

    this.setState({
      isSaving3: true,
    });

    const data = {
      ngDomain: ngDomain
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_custom_domain',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving3: false,
          hasSaved3: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved3: false,
          unsavedChanges3: false,
        });
      }, 2500);

    });

  }

  savePerms() {

    const { user_perms, activeRole } = this.props.getState;

    this.setState({
      isSaving4: true,
    });

    const data = {
      user_perms: user_perms,
      role: activeRole,
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_permissions',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

      setTimeout(() => {
        this.setState({
          isSaving4: false,
          hasSaved4: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved4: false,
          unsavedChanges4: false,
        });
      }, 2500);

    });

  }

  testBroadstreetConnection() {
    this.setState({
      isTesting: true,
      testSuccess: false,
      testFailed: false
    });

    wp.apiRequest({
      path: '/newsletter-glue/v1/broadstreet/test-connection',
      method: 'POST',
      data: { access_token: this.state.broadstreetAccessToken }
    }).then(response => {
      if (response.success) {
        this.setState({
          isTesting: false,
          testSuccess: true,
          testFailed: false,
          hasConnection: true,
          unsavedChanges6: true // Set unsaved changes to true so user knows to save
        }, () => {
          // Fetch network name after successful connection
          this.fetchNetworkName();
        });
      } else {
        this.setState({
          isTesting: false,
          testSuccess: false,
          testFailed: true,
          hasConnection: false
        });
      }
    }).catch(error => {
      console.error('Error testing Broadstreet connection', error);
      this.setState({
        isTesting: false,
        testSuccess: false,
        testFailed: true,
        hasConnection: false
      });
    });
  }

  saveAdInserter() {

    this.setState({
      isSaving6: true,
    });

    // If we have an active ad integration selected, update it on the server
    if (this.state.activeAdIntegration) {
      // If the active integration is Broadstreet and we have a tested access token, save it first
      const saveBroadstreetToken = this.state.activeAdIntegration.value === 'broadstreet' && 
                                  this.state.testSuccess && 
                                  this.state.broadstreetAccessToken;
      
      const saveIntegration = () => {
        wp.apiRequest({
          path: '/newsletter-glue/v1/ad-inserter/set-integration',
          method: 'POST',
          data: { integration_id: this.state.activeAdIntegration.value }
        }).then(response => {
          // Update nglue_backend to reflect the new active integration
          nglue_backend.ad_inserter_active_integration = {
            id: this.state.activeAdIntegration.value,
            name: this.state.activeAdIntegration.label
          };
          
          setTimeout(() => {
            this.setState({
              isSaving6: false,
              hasSaved6: true,
            });
          }, 1500);

          setTimeout(() => {
            this.setState({
              hasSaved6: false,
              unsavedChanges6: false,
              testSuccess: false,
              testFailed: false
            });
          }, 2500);
        }).catch(error => {
          // Handle error
          console.error('Error updating ad integration', error);
          this.setState({ 
            isSaving6: false,
            // Revert to previous state on error
            activeAdIntegration: nglue_backend.ad_inserter_active_integration ? 
              nglue_backend.ad_inserter_integration.find(option => 
                option.value === nglue_backend.ad_inserter_active_integration.id
              ) : null 
          });
        });
      };
      
      if (saveBroadstreetToken) {
        // First save the access token, then save the integration
        wp.apiRequest({
          path: '/newsletter-glue/v1/broadstreet/save-access-token',
          method: 'POST',
          data: { access_token: this.state.broadstreetAccessToken }
        }).then(() => {
          // Update the global backend object to reflect the saved token
          nglue_backend.broadstreet_access_token = this.state.broadstreetAccessToken;
          nglue_backend.broadstreet_has_connection = true;
          saveIntegration();
        }).catch(error => {
          console.error('Error saving Broadstreet access token', error);
          this.setState({ isSaving6: false });
        });
      } else {
        // Just save the integration
        saveIntegration();
      }
    } else {
      // Handle other ad inserter settings if needed
      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/save_ad_inserter',
        method: 'post',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
        data: {}
      }).then(() => {
        setTimeout(() => {
          this.setState({
            isSaving6: false,
            hasSaved6: true,
          });
        }, 1500);

        setTimeout(() => {
          this.setState({
            hasSaved6: false,
            unsavedChanges6: false,
          });
        }, 2500);
      });
    }
  }

  resetRole() {

    var props = this.props;

    const { activeRole } = this.props.getState;

    this.setState({
      isSaving4: true,
      unsavedChanges4: true,
    });

    const data = {
      role: activeRole,
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/reset_role',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(d => {

      props.changeState({ user_perms: d.permissions });

      this.setState({
        resetRoletoggle: false,
      });

      setTimeout(() => {
        this.setState({
          isSaving4: false,
          hasSaved4: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved4: false,
          unsavedChanges4: false,
        });
      }, 2500);

    });

  }

  resetRoles() {

    var props = this.props;

    this.setState({
      isSaving4: true,
      unsavedChanges4: true,
    });

    const data = {

    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/reset_roles',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(d => {

      props.changeState({ user_perms: d.permissions });

      this.setState({
        resetAlltoggle: false,
      });

      setTimeout(() => {
        this.setState({
          isSaving4: false,
          hasSaved4: true,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved4: false,
          unsavedChanges4: false,
        });
      }, 2500);

    });

  }

  render() {

    var props = this.props;

    const { hasSaved1, hasSaved2, hasSaved3, hasSaved4, hasSaved5, hasSaved6, isSaving1, isSaving2, isSaving3, isSaving4, isSaving5, isSaving6, unsavedChanges1, unsavedChanges2, unsavedChanges3, unsavedChanges4, unsavedChanges5, unsavedChanges6, isInvalidSlug, isInvalidDomain, resetAlltoggle, resetRoletoggle } = this.state;

    const { notLicensed, selectedPostTypes, ngSlug, ngDomain, tier, selectedRole, user_perms, activeRole, removeCSSFront, activeAdIntegration } = this.props.getState;

    const panelClass = notLicensed ? 'nglue-panel-off' : '';

    var saveBtnClass1 = 'is-primary';
    if (hasSaved1) {
      saveBtnClass1 = 'nglue-btn-valid';
    } else if (isSaving1) {
      saveBtnClass1 = 'nglue-btn-wait';
    }

    var saveBtnClass2 = 'is-primary';
    if (hasSaved2) {
      saveBtnClass2 = 'nglue-btn-valid';
    } else if (isSaving2) {
      saveBtnClass2 = 'nglue-btn-wait';
    }

    var saveBtnClass3 = 'is-primary';
    if (hasSaved3) {
      saveBtnClass3 = 'nglue-btn-valid';
    } else if (isSaving3) {
      saveBtnClass3 = 'nglue-btn-wait';
    }

    var saveBtnClass4 = 'is-primary';
    if (hasSaved4) {
      saveBtnClass4 = 'nglue-btn-valid';
    } else if (isSaving4) {
      saveBtnClass4 = 'nglue-btn-wait';
    }

    var saveBtnClass5 = 'is-primary';
    if (hasSaved5) {
      saveBtnClass5 = 'nglue-btn-valid';
    } else if (isSaving5) {
      saveBtnClass5 = 'nglue-btn-wait';
    }

    var saveBtnClass6 = 'is-primary';
    if (hasSaved6) {
      saveBtnClass6 = 'nglue-btn-valid';
    } else if (isSaving6) {
      saveBtnClass6 = 'nglue-btn-wait';
    }

    var saveBtnText1 = '';
    if (hasSaved1 == 1) {
      saveBtnText1 = __('Saved', 'newsletter-glue');
    } else if (!isSaving1) {
      saveBtnText1 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText1 = __('Saving...', 'newsletter-glue');
    }

    var saveBtnText2 = '';
    if (hasSaved2 == 1) {
      saveBtnText2 = __('Saved', 'newsletter-glue');
    } else if (!isSaving2) {
      saveBtnText2 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText2 = __('Saving...', 'newsletter-glue');
    }

    var saveBtnText3 = '';
    if (hasSaved3 == 1) {
      saveBtnText3 = __('Saved', 'newsletter-glue');
    } else if (!isSaving3) {
      saveBtnText3 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText3 = __('Saving...', 'newsletter-glue');
    }

    var saveBtnText4 = '';
    if (hasSaved4 == 1) {
      saveBtnText4 = __('Saved', 'newsletter-glue');
    } else if (!isSaving4) {
      saveBtnText4 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText4 = __('Saving...', 'newsletter-glue');
    }

    var saveBtnText5 = '';
    if (hasSaved5 == 1) {
      saveBtnText5 = __('Saved', 'newsletter-glue');
    } else if (!isSaving5) {
      saveBtnText5 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText5 = __('Saving...', 'newsletter-glue');
    }

    var saveBtnText6 = '';
    if (hasSaved6 == 1) {
      saveBtnText6 = __('Saved', 'newsletter-glue');
    } else if (!isSaving6) {
      saveBtnText6 = __('Save', 'newsletter-glue');
    } else {
      saveBtnText6 = __('Saving...', 'newsletter-glue');
    }

    let slugHelp;
    if (ngSlug) {
      slugHelp = ngSlug;
    } else {
      slugHelp = 'newsletter';
    }

    var allowPublisherAccess = false;
    if (tier === 'newsroom' || tier === 'founding' || tier === 'publisher_new') {
      allowPublisherAccess = true;
    }

    return (
      <>
        <div className="nglue-main">
          {notLicensed && <div className="nglue-panel-overlay"></div>}
          <PanelBody className={`nglue-title-only ${panelClass}`}>
            <div className={`nglue-title-bar ${panelClass}`}>
              <div className="nglue-title">
                <span className="nglue-title-main">{__('Additional', 'newsletter-glue')}</span>
                <span className="nglue-title-sub">{__('Manage other settings and defaults here.', 'newsletter-glue')}</span>
              </div>
              <div className="nglue-title-icon"><img src={iconUri} /></div>
            </div>
          </PanelBody>
        </div>

        <div className={`nglue-panel-body ${panelClass}`} style={{ backgroundColor: 'transparent' }}>

          {notLicensed && <div className="nglue-panel-overlay"></div>}

          <div className="nglue-main nglue-main-2">
            <div className="nglue-equal-boxes">

              <PanelBody
              >
                <div className="nglue-info-wrap">
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Onboarding wizard', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Take 5 minutes to set up Newsletter Glue. Connect email service, customize email defaults and personalize default patterns.', 'newsletter-glue') }}></div>
                    </div>
                    <div className="nglue-info-buttons">
                      <Button isPrimary href={nglue_backend.wizard_uri}>{__('Launch onboarding wizard', 'newsletter-glue')}</Button>
                    </div>
                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={wizardIcon} />
                  </div>
                </div>
              </PanelBody>

              <PanelBody
              >
                <div className="nglue-info-wrap">
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Enable Send as newsletter in custom post types', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Select which custom post types you would like to <strong>Send as newsletter</strong>.', 'newsletter-glue') }}></div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        label={__('Custom post types', 'newsletter-glue')}
                        id="nglue-types-select"
                        className={`nglue-base nglue-esp-select`}
                      >
                        <Select
                          name="nglue-types-select"
                          inputId="nglue-types-select"
                          classNamePrefix="nglue"
                          placeholder={__('Select post types...', 'newsletter-glue')}
                          isClearable={false}
                          isDisabled={isSaving1}
                          isMulti={true}
                          inputProps={{ autoComplete: "new-password" }}
                          options={nglue_backend.post_types}
                          value={selectedPostTypes}
                          onChange={
                            (selected) => {
                              this.setState({
                                unsavedChanges1: true,
                              });
                              props.changeState({ selectedPostTypes: selected })
                            }
                          }
                        />
                      </BaseControl>
                    </PanelRow>

                    <div className="nglue-info-buttons">
                      <Button
                        className={saveBtnClass1}
                        icon={hasSaved1 && iconCheck}
                        onClick={this.savePostTypes}
                        disabled={!unsavedChanges1}
                      >
                        {saveBtnText1}
                      </Button>
                    </div>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={postTypesIcon} />
                  </div>
                </div>
              </PanelBody>

              <PanelBody
              >
                <div className="nglue-info-wrap">
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Custom URL slugs', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Change the slug of your newsletter custom post type. Useful for non-English sites or If you want your newsletter name in the URL.', 'newsletter-glue') }}></div>
                      <div className="nglue-info-desc" style={{ paddingTop: '20px' }}>
                        {__('Warning: Changing your slug might result in 404 page not found errors. To avoid this, consider using a redirection plugin.', 'newsletter-glue')} <ExternalLink href="https://redirection.me/support/what-is-a-redirect/">{__('Learn more', 'newsletter-glue')}</ExternalLink>
                      </div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        label={__('Newsletter custom post type slug', 'newsletter-glue')}
                        id="nglue-post-type-slug"
                        className={`nglue-base nglue-esp-input ` + (isInvalidSlug ? 'nglue-esp-invalid' : '')}
                      >
                        <input
                          type="text"
                          id="nglue-post-type-slug"
                          value={ngSlug}
                          disabled={isSaving2}
                          onChange={e => {
                            this.setState({
                              unsavedChanges2: true,
                            });
                            props.changeState({ ngSlug: e.target.value });
                          }}
                        />
                        <div className="nglue-input-help">
                          {nglue_backend.home_url}/<strong>{slugHelp}</strong>/archive
                        </div>
                      </BaseControl>
                    </PanelRow>

                    <div className="nglue-info-buttons">
                      <Button
                        className={saveBtnClass2}
                        icon={hasSaved2 && iconCheck}
                        onClick={this.saveSlug}
                        disabled={!unsavedChanges2 || !ngSlug}
                      >
                        {saveBtnText2}
                      </Button>
                    </div>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={slugsIcon} />
                  </div>
                </div>
              </PanelBody>

              <PanelBody
                className={!allowPublisherAccess && 'nglue-tier-locked'}
              >
                {!allowPublisherAccess &&
                  <UpgradeNotice title={__('Static sites: Replace URL', 'newsletter-glue')} />
                }
                <div className="nglue-info-wrap">
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Static sites: Replace URL', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Replace all links with a custom root domain. Ignore this feature If you don’t have a static site, or aren’t sure what that is.', 'newsletter-glue') }}></div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        label={__('Enter root domain', 'newsletter-glue')}
                        id="nglue-custom-domain"
                        className={`nglue-base nglue-esp-input ` + (isInvalidDomain ? 'nglue-esp-invalid' : '')}
                      >
                        <input
                          type="text"
                          id="nglue-custom-domain"
                          value={ngDomain}
                          disabled={isSaving3}
                          placeholder="https://"
                          onChange={e => {
                            this.setState({
                              unsavedChanges3: true,
                            });
                            props.changeState({ ngDomain: e.target.value });
                          }}
                        />
                      </BaseControl>
                    </PanelRow>

                    <div className="nglue-info-buttons">
                      <Button
                        className={saveBtnClass3}
                        icon={hasSaved3 && iconCheck}
                        onClick={this.saveDomain}
                        disabled={!unsavedChanges3}
                      >
                        {saveBtnText3}
                      </Button>
                    </div>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={rootIcon} />
                  </div>
                </div>
              </PanelBody>

              <PanelBody
                className="nglue-checkbox-panel"
              >
                <div className="nglue-info-wrap">
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Remove email styling from web view', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Remove all of Newsletter Glue’s CSS styling from the web version of your email newsletters. The web version will automatically revert to your site’s theme styling.', 'newsletter-glue') }}></div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        id="nglue-remove-front-css"
                        className={`nglue-base nglue-esp-input`}
                      >
                          <CheckboxControl
                            label={__('Remove all CSS styling from web version of your email newsletters.', 'newsletter-glue')}
                            checked={removeCSSFront}
                            disabled={isSaving5}
                            onChange={(value) => {
                              props.changeState({ removeCSSFront: value });
                              this.setState({ unsavedChanges5: true });
                            }}
                          />
                      </BaseControl>
                    </PanelRow>

                    <div className="nglue-info-buttons">
                      <Button
                        className={saveBtnClass5}
                        icon={hasSaved5 && iconCheck}
                        onClick={this.saveCSS}
                        disabled={!unsavedChanges5}
                      >
                        {saveBtnText5}
                      </Button>
                    </div>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={frontcssIcon} />
                  </div>
                </div>
              </PanelBody>

              <PanelBody className="nglue-flex-hide">
                {" "}
              </PanelBody>



              <PanelBody className={`nglue-title-only ${panelClass} nglue-title-only-full-width`}>
                <div className={`nglue-title-bar ${panelClass} nglue-title-bar-replace-margin`}>
                  <div className="nglue-title">
                    <span className="nglue-title-main">{__('Global Block Settings', 'newsletter-glue')}</span>
                    <span className="nglue-title-sub">{__('Manage global block settings here.', 'newsletter-glue')}</span>
                  </div>
                  <div className="nglue-title-icon"><img src={iconUri} /></div>
                    </div>
                    {" "}
                  </PanelBody>



              <PanelBody
                className={`nglue-flex-grow`}
              >
                <div className="nglue-info-wrap" style={{ minHeight: '200px' }}>
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('Ad Inserter', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Customize the global Ad Inserter block behavior.', 'newsletter-glue') }}></div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        className={`nglue-base nglue-ad-integration-select`}
                      >
                        <BaseControl
                        label={__('Ad Manager Integration', 'newsletter-glue')}
                        id="nglue-ad-manager-integration-select"
                        className={`nglue-base nglue-ad-manager-integration-select`}
                      >
                        <Select
                          name="nglue-ad-manager-integration-select"
                          inputId="nglue-ad-manager-integration-select"
                          classNamePrefix="nglue"
                          placeholder={__('Select ad manager integration...', 'newsletter-glue')}
                          isClearable={false}
                          isDisabled={isSaving6}
                          isMulti={false}
                          inputProps={{ autoComplete: "new-password" }}
                          options={nglue_backend.ad_inserter_integration || []}
                          value={
                            this.state.activeAdIntegration || (
                              nglue_backend.ad_inserter_active_integration ? 
                              nglue_backend.ad_inserter_integration.find(option => 
                                option.value === nglue_backend.ad_inserter_active_integration.id
                              ) : null
                            )
                          }
                          onChange={
                            (selected) => {
                              // Update local state immediately and mark as having unsaved changes
                              this.setState({ 
                                activeAdIntegration: selected,
                                unsavedChanges6: true
                              });
                            }
                          }
                        />
                        {this.state.activeAdIntegration.value === 'advanced-ads' &&
                          <div className="nglue-input-help">
                            {__('Ads will be searched from the Advanced Ads plugin.', 'newsletter-glue')}
                          </div>}
                        {this.state.activeAdIntegration.value === 'prototype' &&
                          <div className="nglue-input-help">
                            {__('[DEBUG] This is a placeholder integration for debugging purposes.', 'newsletter-glue')}
                            {__('Ads will be searched from the Prototype integration.', 'newsletter-glue')}
                          </div>}
                        {this.state.activeAdIntegration.value === 'broadstreet' &&
                          <div>
                            <div className="nglue-info-desc">
                              {__('Ads will be searched from the Broadstreet ad manager.', 'newsletter-glue')}
                            </div>
                            <div className="nglue-info-field">
                              {!this.state.hasConnection && (
                                <BaseControl
                                  label={__('Broadstreet Access Token', 'newsletter-glue')}
                                  id="nglue-broadstreet-access-token"
                                  className={`nglue-base nglue-esp-input`}
                                >
                                  <input
                                    type="text"
                                    id="nglue-broadstreet-access-token"
                                    value={this.state.broadstreetAccessToken}
                                    onChange={(e) => {
                                      this.setState({ 
                                        broadstreetAccessToken: e.target.value,
                                        testSuccess: false,
                                        testFailed: false
                                      });
                                    }}
                                    placeholder={__('Enter your Broadstreet access token', 'newsletter-glue')}
                                  />
                                </BaseControl>
                              )}
                              {this.state.hasConnection && (
                                <div className="nglue-info-network-name" style={{ padding: '10px 0', fontSize: '18px'}}>
                                  {__('You are connected to: ', 'newsletter-glue')}
                                  <span className="nglue-network-name">
                                    {this.state.networkName ? this.state.networkName : __('Loading...', 'newsletter-glue')}
                                  </span>
                                </div>
                              )}
                              <div className={`nglue-info-actions ${this.state.testSuccess ? 'nglue-btn-valid has-text has-icon' : ''}`} style={{ padding: '10px 0' }}>
                                {!this.state.hasConnection && (
                                  <Button 
                                    {...this.state.isTesting ? {isBusy: true} : {isTertiary: true}}
                                    onClick={this.testBroadstreetConnection}
                                    disabled={this.state.isTesting || !this.state.broadstreetAccessToken}
                                  >
                                    {this.state.isTesting ? __('Testing...', 'newsletter-glue') : __('Test Connection', 'newsletter-glue')}
                                  </Button>
                                )}
                                {this.state.hasConnection && (
                                  <Button 
                                    isDestructive={true}
                                    onClick={() => {
                                      // Update the connection status in the database
                                      wp.apiRequest({
                                        path: '/newsletter-glue/v1/broadstreet/remove-connection',
                                        method: 'POST'
                                      }).then(() => {
                                        // Update the global backend object
                                        nglue_backend.broadstreet_has_connection = false;
                                        nglue_backend.broadstreet_access_token = '';
                                        
                                        this.setState({ 
                                          hasConnection: false,
                                          broadstreetAccessToken: '',
                                          testSuccess: false,
                                          testFailed: false,
                                          unsavedChanges6: true // Set unsaved changes to true so user knows to save
                                        });
                                      }).catch(error => {
                                        console.error('Error removing Broadstreet connection', error);
                                      });
                                    }}
                                  >
                                    {__('Remove Connection', 'newsletter-glue')}
                                  </Button>
                                )}
                              </div>
                              {this.state.testSuccess && (
                                <div className="nglue-info-success">
                                  {__('Connection successful! Click Save to apply changes.', 'newsletter-glue')}
                                </div>
                              )}
                              {this.state.testFailed && (
                                <div className="nglue-info-error">
                                  {__('Connection failed. Please check your access token and try again.', 'newsletter-glue')}
                                </div>
                              )}
                            </div>
                          </div>
                        }  

                      </BaseControl>
                      </BaseControl>

                      
                    </PanelRow>

                    <div className="nglue-info-buttons">
                  <Button
                      className={saveBtnClass6}
                      icon={hasSaved6 && iconCheck}
                      onClick={this.saveAdInserter}
                      disabled={!unsavedChanges6}
                    >
                      {saveBtnText6}
                    </Button>
                </div>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={adInserterIcon} />
                  </div>
                </div>
                
              </PanelBody>

              <PanelBody className="nglue-flex-hide">
                {" "}
              </PanelBody>

              <PanelBody
                className={`nglue-flex-grow ` + (!allowPublisherAccess && 'nglue-tier-locked')}
              >
                {!allowPublisherAccess &&
                  <UpgradeNotice title={__('User permissions', 'newsletter-glue')} isExtended={true} />
                }
                <div className="nglue-info-wrap nglue-info-wrap-roles" style={{ minHeight: '200px' }}>
                  <div className="nglue-info-head">
                    <div className="nglue-info-label">
                      <div className="nglue-info-main">{__('User permissions', 'newsletter-glue')}</div>
                      <div className="nglue-info-desc" dangerouslySetInnerHTML={{ __html: __('Select what Newsletter Glue features each user role has access to.', 'newsletter-glue') }}></div>
                    </div>

                    <PanelRow>
                      <BaseControl
                        label={__('User role', 'newsletter-glue')}
                        id="nglue-role-select"
                        className={`nglue-base nglue-esp-select`}
                      >
                        <Select
                          name="nglue-role-select"
                          inputId="nglue-role-select"
                          classNamePrefix="nglue"
                          placeholder={__('Select user role...', 'newsletter-glue')}
                          isClearable={false}
                          isDisabled={isSaving4}
                          isMulti={false}
                          inputProps={{ autoComplete: "new-password" }}
                          options={nglue_backend.js_roles}
                          value={selectedRole}
                          onChange={
                            (selected) => {
                              props.changeState({ activeRole: selected.value, selectedRole: selected })
                            }
                          }
                        />
                        {activeRole === 'administrator' &&
                          <div className="nglue-input-help">
                            {__('By default, Administrators have full access and permissions to Newsletter Glue. This cannot be changed.', 'newsletter-glue')}
                          </div>}
                      </BaseControl>

                      <BaseControl
                        className={`nglue-base nglue-esp-input`}
                        label={__('Grant this user access to:', 'newsletter-glue')}
                      >
                        <div style={{ height: '14px' }}></div>
                        <div className="nglue-cbox">
                          <CheckboxControl
                            label={__('View and edit newsletters', 'newsletter-glue')}
                            checked={user_perms[activeRole]['edit_newsletterglue'] && user_perms[activeRole]['edit_newsletterglue'] == 1}
                            disabled={activeRole == 'administrator' || isSaving4}
                            onChange={(e) => {
                              var new_perms = user_perms;
                              if (!e) {
                                new_perms[activeRole]['edit_newsletterglue'] = 0;
                                new_perms[activeRole]['add_newsletterglue'] = 0;
                              } else {
                                new_perms[activeRole]['edit_newsletterglue'] = 1;
                              }
                              props.changeState({ user_perms: new_perms });
                              this.setState({ unsavedChanges4: true });
                            }}
                          />
                        </div>
                        <div className="nglue-cbox nglue-cbox-child">
                          <CheckboxControl
                            label={__('Add new newsletter', 'newsletter-glue')}
                            checked={user_perms[activeRole]['add_newsletterglue'] && user_perms[activeRole]['add_newsletterglue'] == 1}
                            disabled={activeRole == 'administrator' || isSaving4}
                            onChange={(e) => {
                              var new_perms = user_perms;
                              if (!e) {
                                new_perms[activeRole]['add_newsletterglue'] = 0;
                              } else {
                                new_perms[activeRole]['add_newsletterglue'] = 1;
                                new_perms[activeRole]['edit_newsletterglue'] = 1;
                              }
                              props.changeState({ user_perms: new_perms });
                              this.setState({ unsavedChanges4: true });
                            }}
                          />
                        </div>
                        <div className="nglue-cbox">
                          <CheckboxControl
                            label={__('Send as newsletter', 'newsletter-glue')}
                            checked={user_perms[activeRole]['publish_newsletterglue'] && user_perms[activeRole]['publish_newsletterglue'] == 1}
                            disabled={activeRole == 'administrator' || isSaving4}
                            onChange={(e) => {
                              var new_perms = user_perms;
                              if (!e) {
                                new_perms[activeRole]['publish_newsletterglue'] = 0;
                              } else {
                                new_perms[activeRole]['publish_newsletterglue'] = 1;
                              }
                              props.changeState({ user_perms: new_perms });
                              this.setState({ unsavedChanges4: true });
                            }}
                          />
                        </div>
                        <div className="nglue-cbox">
                          <CheckboxControl
                            label={__('Templates & styles', 'newsletter-glue')}
                            checked={user_perms[activeRole]['manage_newsletterglue_patterns'] && user_perms[activeRole]['manage_newsletterglue_patterns'] == 1}
                            disabled={activeRole == 'administrator' || isSaving4}
                            onChange={(e) => {
                              var new_perms = user_perms;
                              if (!e) {
                                new_perms[activeRole]['manage_newsletterglue_patterns'] = 0;
                              } else {
                                new_perms[activeRole]['manage_newsletterglue_patterns'] = 1;
                              }
                              props.changeState({ user_perms: new_perms });
                              this.setState({ unsavedChanges4: true });
                            }}
                          />
                        </div>
                        <div className="nglue-cbox">
                          <CheckboxControl
                            label={__('Newsletter Glue settings', 'newsletter-glue')}
                            checked={user_perms[activeRole]['manage_newsletterglue'] && user_perms[activeRole]['manage_newsletterglue'] == 1}
                            disabled={activeRole == 'administrator' || isSaving4}
                            onChange={(e) => {
                              var new_perms = user_perms;
                              if (!e) {
                                new_perms[activeRole]['manage_newsletterglue'] = 0;
                              } else {
                                new_perms[activeRole]['manage_newsletterglue'] = 1;
                              }
                              props.changeState({ user_perms: new_perms });
                              this.setState({ unsavedChanges4: true });
                            }}
                          />
                        </div>
                      </BaseControl>
                    </PanelRow>

                  </div>
                  <div className="nglue-info-wrap-icon">
                    <img src={rolesIcon} />
                  </div>
                </div>
                <div className="nglue-info-foot">
                  <div className="nglue-foot-left">
                    <div className="nglue-foot-act">
                      <a
                        href="#"
                        className={isSaving4 ? 'is-disabled' : ''}
                        onClick={(e) => {
                          e.preventDefault();
                          if (!resetAlltoggle) {
                            this.setState({ resetAlltoggle: true });
                          } else {
                            this.setState({ resetAlltoggle: false });
                          }
                        }}
                      >{__('Reset all user permissions', 'newsletter-glue')} {!resetAlltoggle && iconChevronDown} {resetAlltoggle && iconChevronUp}</a>
                      {resetAlltoggle && <div className="nglue-foot-act-confirm">
                        <a
                          href="#"
                          className={isSaving4 ? 'is-disabled' : ''}
                          onClick={(e) => {
                            e.preventDefault();
                            this.resetRoles();
                          }}
                        >{__('Confirm reset', 'newsletter-glue')}</a>
                        <span>{__('(you can’t undo after this)', 'newsletter-glue')}</span>
                      </div>}
                    </div>
                  </div>
                  <div className="nglue-foot-right">

                    <div className="nglue-foot-act">
                      <a
                        href="#"
                        className={isSaving4 ? 'is-disabled' : ''}
                        onClick={(e) => {
                          e.preventDefault();
                          if (!resetRoletoggle) {
                            this.setState({ resetRoletoggle: true });
                          } else {
                            this.setState({ resetRoletoggle: false });
                          }
                        }}
                      >{__('Reset this role’s permissions', 'newsletter-glue')} {!resetRoletoggle && iconChevronDown} {resetRoletoggle && iconChevronUp}</a>
                      {resetRoletoggle && <div className="nglue-foot-act-confirm">
                        <a
                          href="#"
                          className={isSaving4 ? 'is-disabled' : ''}
                          onClick={(e) => {
                            e.preventDefault();
                            this.resetRole();
                          }}
                        >{__('Confirm reset', 'newsletter-glue')}</a>
                        <span>{__('(you can’t undo after this)', 'newsletter-glue')}</span>
                      </div>}
                    </div>

                    <Button
                      className={saveBtnClass4}
                      icon={hasSaved4 && iconCheck}
                      onClick={this.savePerms}
                      disabled={!unsavedChanges4 || (activeRole == 'administrator')}
                    >
                      {saveBtnText4}
                    </Button>
                  </div>
                </div>
              </PanelBody>

            </div>
          </div>

        </div>

      </>
    );

  }

}