import { __ } from '@wordpress/i18n';
import React from 'react';

import apiFetch from '@wordpress/api-fetch';

import {
  Component
} from '@wordpress/element';

import {
  Button,
  FocusableIframe,
  FormToggle,
  Modal,
  PanelBody
} from '@wordpress/components';

const iconUri = nglue_backend.images_uri + 'icon-blocks.svg';

export default class Blocks extends Component {

  constructor(props) {

    super(props);

    this.changeOptions = this.changeOptions.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.showModal = this.showModal.bind(this);

    const data = {
      blocks: nglue_backend.blocks,
      OpenModal: false,
      demoTitle: '',
      demoURL: '',
    };

    this.state = data;

  }

  showModal(title, url) {
    this.setState({
      OpenModal: true,
      demoTitle: title,
      demoURL: url,
    });
  }

  closeModal() {
    this.setState({
      OpenModal: false,
      demoTitle: '',
      demoURL: '',
    });
  }

  changeOptions(option, value) {

    this.setState({
      isSaving: true,
    });

    const data = {
      block_id: option,
      is_active: value
    };

    var _newoptions = this.state.blocks;
    _newoptions[option]['in_use'] = value;
    this.setState({ blocks: _newoptions });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_block_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

    });

  }

  render() {

    const { blocks, OpenModal, demoTitle, demoURL } = this.state;

    const { notLicensed } = this.props.getState;

    const panelClass = notLicensed ? 'nglue-panel-off' : '';

    var viewBlocks = [];

    for (var block in blocks) {
      var item = blocks[block];

      let theclass = '';
      if (blocks[item.id]['in_use']) {
        theclass = 'nglue-is-active';
      } else {
        theclass = 'nglue-is-inactive';
      }

      viewBlocks.push(
        <PanelBody
          key={`nglue-block-${item.id}`}
          className={theclass}
        >
          <div className="nglue-info-head">
            <div className="nglue-info-icon">
              <div dangerouslySetInnerHTML={{ __html: item.icon }}></div>
            </div>
            <div className="nglue-info-label">
              <div className="nglue-info-main">{item.label}</div>
              <div className="nglue-info-desc">{item.description}</div>
            </div>
          </div>
          <div className="nglue-info-foot">
            <div className="nglue-foot-left">
              <div className="nglue-foot-label">{__('Enable block', 'newsletter-glue')}</div>
              <div className="nglue-foot-control">
                <FormToggle
                  id={item.id}
                  checked={blocks[item.id]['in_use']}
                  onChange={(e) => {
                    this.changeOptions(e.target.id, !this.state.blocks[e.target.id]['in_use']);
                  }}
                />
              </div>
            </div>
            <div className="nglue-foot-right">
              <Button
                variant="tertiary"
                className="is-tertiary"
                data-title={item.label}
                data-url={item.url}
                onClick={() => {
                  var title = event.target.getAttribute('data-title');
                  var url = event.target.getAttribute('data-url');
                  this.showModal(title, url);
                }}
              >{__('Watch demo', 'newsletter-glue')}</Button>
            </div>
          </div>
        </PanelBody>
      );
    }

    return (
      <>
        <div className="nglue-main">
          {notLicensed && <div className="nglue-panel-overlay"></div>}
          <PanelBody className={`nglue-title-only ${panelClass}`}>
            <div className={`nglue-title-bar ${panelClass}`}>
              <div className="nglue-title">
                <span className="nglue-title-main">{__('Newsletter blocks', 'newsletter-glue')}</span>
                <span className="nglue-title-sub">{__('Explore and manage all newsletter blocks. Enabled blocks can be used in the block editor. Disabled blocks won’t appear in the block editor.', 'newsletter-glue')}</span>
              </div>
              <div className="nglue-title-icon"><img src={iconUri} /></div>
            </div>
          </PanelBody>
        </div>

        <div className={`nglue-panel-body ${panelClass}`} style={{ backgroundColor: 'transparent' }}>
          {notLicensed && <div className="nglue-panel-overlay"></div>}
          <div className="nglue-main nglue-main-2">
            <div className="nglue-equal-boxes">
              {viewBlocks}
            </div>
          </div>

        </div>

        {OpenModal &&
          <Modal title={demoTitle} onRequestClose={this.closeModal} shouldCloseOnEsc={false}>
            <FocusableIframe width="800" height="450" src={demoURL} frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />
          </Modal>
        }

      </>
    );

  }

}