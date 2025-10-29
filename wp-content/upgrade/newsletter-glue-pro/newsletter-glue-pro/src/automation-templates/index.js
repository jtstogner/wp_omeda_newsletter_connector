import React from 'react';

import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { createRoot } from 'react-dom/client';

import {
  Component
} from '@wordpress/element';

import {
  MenuGroup,
  MenuItem,
  Modal,
  SearchControl,
} from '@wordpress/components';

export default class AutomationTemplates extends Component {

  constructor(props) {

    super(props);

    this.openModal = this.openModal.bind(this);
    this.closeModal = this.closeModal.bind(this);

    this.state = {
      isOpen: false,
      searchQuery: '',
      filters: nglue_backend.template_categories,
      catID: 0,
      catName: '',
      searchCount: 0,
      apiFetched: false,
      isSearching: true,
      results: '',
      previews: '',
    };

  }

  openModal() {
    this.setState({ isOpen: true });
  }

  closeModal() {
    this.setState({ isOpen: false });
  }

  componentDidUpdate() {
    const { isOpen, catID, searchQuery, isSearching } = this.state;

    if (isOpen && isSearching) {

      apiFetch({
        path: 'newsletterglue/' + nglue_backend.api_version + '/get_templates?automations_only=1&category_id=' + catID + '&query=' + searchQuery,
        method: 'get',
        headers: {
          'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
        },
      }).then(res => {
        this.setState({ isSearching: false, apiFetched: true, results: res.results, searchCount: res.count, previews: res.previews });
      });
    }
  }

  render() {

    const { isOpen, searchQuery, filters, catID, searchCount, apiFetched, isSearching, results, catName } = this.state;

    var viewFilters = [];

    for (var filter in filters) {
      var item = filters[filter];

      viewFilters.push(
        <MenuItem
          key={`nglue-filter-${item.id}`}
          isPressed={catID == item.id ? true : false}
          data-category={item.id}
          data-name={item.name}
          onClick={(e) => {
            var el = e.target;
            if (!el.getAttribute('data-category')) {
              el = e.target.parentNode;
              if (!el.getAttribute('data-category')) {
                el = e.target.parentNode.parentNode;
              }
            }
            if (el.getAttribute('aria-pressed') == 'false') {
              var cat = el.getAttribute('data-category');
              var catname = el.getAttribute('data-name');
              this.setState({ catID: cat, catName: catname, isSearching: true });
            }
          }}
        >
          {item.name}
          <span>{item.count}</span>
        </MenuItem>
      );
    }

    var outputResults = [];

    if (!apiFetched || isSearching) {
      outputResults.push(
        <div key="ngl-placeholder-1">
          <div className="ngl-template-placeholder-2" style={{ width: "50%" }}></div>
        </div>
      );
      outputResults.push(
        <div key="ngl-placeholder-2">
          <div className="ngl-template-placeholder-2" style={{ width: "40%" }}></div>
        </div>
      );
      outputResults.push(
        <div key="ngl-placeholder-3">
          <div className="ngl-template-placeholder-2" style={{ width: "30%" }}></div>
        </div>
      );
    } else {
      if (results) {
        results.map((item, i) => {
          outputResults.push(
            <div
              key={`ngl-placeholder-${i}`}
              className="ngl-template-result"
              data-url={nglue_backend.newsletter_url + '&template_id=' + item.ID}
              onClick={(e) => {
                window.location.href = e.target.closest('.ngl-template-result').getAttribute('data-url');
              }}
            >
              <div className="ngl-template-placeholder-2">
                {item.post_title}
                {nglue_backend.default_template == item.ID && <span>{__('Default', 'newsletter-glue')}</span>}
              </div>
            </div>
          );
        })
      }
    }

    return (
      <>
        <div
          className="page-title-action"
          onClick={this.openModal}
        >
          {__('Add Automation', 'newsletter-glue')}
        </div>
        {isOpen && (
          <Modal
            title={__('Automations Template Library', 'newsletter-glue')}
            onRequestClose={this.closeModal}
            isFullScreen={false}
            shouldCloseOnEsc={false}
            className="ngl-template-inserter-block"
            size="large"
          >
            <div className={`ngl-template-inserter${searchQuery ? ' is-searching' : ''}`}>
              <div className="ngl-template__sidebar">
                <div className="ngl-template__sidebar__search">
                  <SearchControl
                    value={searchQuery}
                    onChange={(e) => {
                      this.setState({ searchQuery: e, isSearching: true });
                    }}
                  />
                </div>
                <MenuGroup label={__('Browse by category', 'newsletter-glue')} className="ngl-template__sidebar__category">
                  {viewFilters}
                </MenuGroup>
              </div>
              <div className="ngl-template__content">
                {searchQuery && !isSearching && (
                  <div className="ngl-template__content-header">
                    <div className="search-results">
                      {`${searchCount} search result${searchCount == 1 ? '' : 's'} for "${searchQuery}"${catID ? ' in ' + catName : ''}`}
                    </div>
                  </div>
                )}
                <div className="ngl-template__content-grid">
                  <div className="ngl-template__list">
                    {outputResults}
                  </div>
                </div>
              </div>
            </div>
          </Modal>
        )}
      </>
    );

  }

}

var rootElement = document.getElementById('ngl_automation_select');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<AutomationTemplates />);
}