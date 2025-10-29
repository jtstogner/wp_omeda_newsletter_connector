import { __, sprintf } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  Component
} from '@wordpress/element';

import {
  BaseControl,
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
  __experimentalSpacer as Spacer,
} from '@wordpress/components';

import { Icon, close } from '@wordpress/icons';

import Select from 'react-select';

import { iconAdd, iconCheck, iconChevDown, iconChevronDown, iconChevronUp, iconIssue } from '../common/icons';

import Loader from '../common/loader';

const iconUri = nglue_backend.images_uri + 'icon-connections.svg';

const tools = [
  {
    id: "reset_templates",
    header: "Newsletter templates",
    label: "Reset templates",
    desc: "This tool will reset all default newsletter templates."
  },
  {
    id: "reset_patterns",
    header: "Newsletter patterns",
    label: "Reset patterns",
    desc: "This tool will reset all default newsletter patterns."
  },
  {
    id: "reset_theme",
    header: "Newsletter theme",
    label: "Reset theme",
    desc: "This tool will reset all theme changes and restore the default theme."
  },
  {
    id: "reset_css",
    header: "Newsletter custom CSS",
    label: "Reset custom CSS",
    desc: "This tool will reset newsletter custom CSS."
  },
  {
    id: "reinstall_roles",
    header: "User Roles",
    label: "Reset user roles",
    desc: "This tool will reset all newsletter roles and permissions."
  },
];

export default class Tools extends Component {

  constructor(props) {

    super(props);

    this.initTool = this.initTool.bind(this);

    const data = {
      loading: {

      },
      messages: {
      },
    };

    this.state = data;

  }

  initTool( tool ) {

    const shouldInit = confirm( `${tool.desc} Are you sure you want to continue?` );

    if ( shouldInit ) {

      this.setState( prevState => {
         return{
            ...prevState,
            loading: {...prevState.loading, [tool.id]: true },
         }
      });

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + `/run_action_tools`,
        method: 'post',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
        data: {
          action: tool.id,
        }
      }).then( res => {
        this.setState( prevState => {
           return{
              ...prevState,
              loading: {...prevState.loading, [tool.id]: false },
              messages: {...prevState.messages, [tool.id]: res.message },
           }
        });
      });
    }

  }

  componentDidMount() {

  }

  render() {

    return (
      <>
        <div className="nglue-main">
          {Object.keys(this.state.messages).map((message, i) => {
            if ( ! this.state.messages[message] ) {
              return null;
            }
            return (
              <div className="updated inline nglue-updated-inline is-dismissible" key={`ng-tool-${i}`}>
                {this.state.messages[message]}
                <Button 
                  variant="link"
                  className="ngl-dismiss"
                  onClick={ () => {
                    this.setState( prevState => {
                       return{
                          ...prevState,
                          messages: {...prevState.messages, [message]: null },
                       }
                    });
                  }}
                ><Icon icon={close} size={16} /></Button>
              </div>
            );
          })}
          <PanelBody className={`nglue-settings-tools`}>
            <div className={`nglue-title-bar`}>
              <div className="nglue-title">
                <span className="nglue-title-main">{__('Tools', 'newsletter-glue')}</span>
                <span className="nglue-title-sub">{__('Use tools to restore your newsletter settings or reset plugin defaults.', 'newsletter-glue')}</span>
              </div>
              <div className="nglue-title-icon"></div>
            </div>

            {tools.map((tool, i) => {
              const processing = this.state.loading[tool.id];
              return (
                <div className={`nglue-panel-body`} key={ `tool-${i}` }>
                  <PanelRow>
                    <div>
                      <h4>{ tool.header }</h4>
                      <p>{ tool.desc }</p>
                    </div>
                    <Button
                      variant="tertiary"
                      size="small"
                      disabled={ processing }
                      isBusy={ processing }
                      onClick={ () => this.initTool( tool ) }
                    >{ tool.label }</Button>
                  </PanelRow>
                </div>
              );
            })}

          </PanelBody>
        </div>
      </>
    );

  }

}