import { __ } from '@wordpress/i18n';
import React from 'react';
import { createRoot } from 'react-dom/client';

import {
  Component
} from '@wordpress/element';

import {
  BaseControl,
  __experimentalBoxControl as BoxControl,
  Button,
  Card,
  CardBody,
  ColorIndicator,
  Flex,
  FlexItem,
  __experimentalGrid as Grid,
  __experimentalHStack as HStack,
  __experimentalItem as Item,
  __experimentalItemGroup as ItemGroup,
  __experimentalNavigatorButton as NavigatorButton,
  __experimentalNavigatorProvider as NavigatorProvider,
  __experimentalNavigatorScreen as NavigatorScreen,
  Panel,
  PanelBody,
  PanelRow,
  Popover,
  SlotFillProvider,
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import NGEmailPreview from './email-preview.js';
import NGScreens from './screens.js';
import NGSwitchView from './switch-view.js';

export default class GlobalStyles extends Component {

  constructor(props) {

    super(props);

    this.updateTheme = this.updateTheme.bind(this);
    this.handleChange = this.handleChange.bind(this);
    this.updateDimensions = this.updateDimensions.bind(this);
    this.ajaxSave = this.ajaxSave.bind(this);
    this.InlineSave = this.InlineSave.bind(this);
    this.resetTheme = this.resetTheme.bind(this);
    this.quickStyle = this.quickStyle.bind(this);
    this.hoverStyle = this.hoverStyle.bind(this);

    this.state = {
      unsavedChanges: false,
      theme_r: nglue_backend.theme_r,
      theme_m: nglue_backend.theme_m,
      isMobile: false,
      customCSS: nglue_backend.custom_css,
      changes: [],
      isSaving: false,
      currentStyle: 0,
      hoveredStyle: 0,
      quickstyle: nglue_backend.quickstyle,
      ngColors: nglue_backend.ngColors,
    };

  }

  quickStyle(num, Identifier) {
    var pushChanges = [];

    let theme = this.state.isMobile ? this.state.theme_m : this.state.theme_r;

    let theme_r = this.state.theme_r;
    let theme_m = this.state.theme_m;

    var selectedStyle = [];
    Object.keys(theme.styles).forEach(function (key) {
      var id = key + 1;
      if (id === num) {
        selectedStyle = theme.styles[key];
      }
    });

    theme_r['email_bg'] = selectedStyle.bg;
    theme_m['email_bg'] = selectedStyle.bg;
    pushChanges.push({ key: 'email_bg', value: selectedStyle.bg });

    theme_r['container_bg'] = selectedStyle.content;
    theme_m['container_bg'] = selectedStyle.content;
    pushChanges.push({ key: 'container_bg', value: selectedStyle.content });

    theme_r['btn_border'] = selectedStyle.button;
    theme_m['btn_border'] = selectedStyle.button;
    pushChanges.push({ key: 'btn_border', value: selectedStyle.button });

    theme_r['btn_bg'] = selectedStyle.button;
    theme_m['btn_bg'] = selectedStyle.button;
    pushChanges.push({ key: 'btn_bg', value: selectedStyle.button });

    theme_r['a_colour'] = selectedStyle.button;
    theme_m['a_colour'] = selectedStyle.button;
    pushChanges.push({ key: 'a_colour', value: selectedStyle.button });

    if (selectedStyle.font_h_s) {
      theme_r['h1_font'] = selectedStyle.font_h_s;
      theme_m['h1_font'] = selectedStyle.font_h_s;
      theme_r['h2_font'] = selectedStyle.font_h_s;
      theme_m['h2_font'] = selectedStyle.font_h_s;
      theme_r['h3_font'] = selectedStyle.font_h_s;
      theme_m['h3_font'] = selectedStyle.font_h_s;
      theme_r['h4_font'] = selectedStyle.font_h_s;
      theme_m['h4_font'] = selectedStyle.font_h_s;
      theme_r['h5_font'] = selectedStyle.font_h_s;
      theme_m['h5_font'] = selectedStyle.font_h_s;
      theme_r['h6_font'] = selectedStyle.font_h_s;
      theme_m['h6_font'] = selectedStyle.font_h_s;
      pushChanges.push(
        { key: 'h1_font', value: selectedStyle.font_h_s },
        { key: 'h2_font', value: selectedStyle.font_h_s },
        { key: 'h3_font', value: selectedStyle.font_h_s },
        { key: 'h4_font', value: selectedStyle.font_h_s },
        { key: 'h5_font', value: selectedStyle.font_h_s },
        { key: 'h6_font', value: selectedStyle.font_h_s }
      );
    }

    if (selectedStyle.font_p_s) {
      theme_r['p_font'] = selectedStyle.font_p_s;
      theme_m['p_font'] = selectedStyle.font_p_s;
      pushChanges.push({ key: 'p_font', value: selectedStyle.font_p_s });
    }

    theme_r['h1_colour'] = selectedStyle.heading;
    theme_m['h1_colour'] = selectedStyle.heading;
    theme_r['h2_colour'] = selectedStyle.heading;
    theme_m['h2_colour'] = selectedStyle.heading;
    theme_r['h3_colour'] = selectedStyle.heading;
    theme_m['h3_colour'] = selectedStyle.heading;
    theme_r['h4_colour'] = selectedStyle.heading;
    theme_m['h4_colour'] = selectedStyle.heading;
    theme_r['h5_colour'] = selectedStyle.heading;
    theme_m['h5_colour'] = selectedStyle.heading;
    theme_r['h6_colour'] = selectedStyle.heading;
    theme_m['h6_colour'] = selectedStyle.heading;
    pushChanges.push(
      { key: 'h1_colour', value: selectedStyle.heading },
      { key: 'h2_colour', value: selectedStyle.heading },
      { key: 'h3_colour', value: selectedStyle.heading },
      { key: 'h4_colour', value: selectedStyle.heading },
      { key: 'h5_colour', value: selectedStyle.heading },
      { key: 'h6_colour', value: selectedStyle.heading }
    );

    theme_r['p_colour'] = selectedStyle.p;
    theme_m['p_colour'] = selectedStyle.p;
    pushChanges.push({ key: 'p_colour', value: selectedStyle.p });

    theme_r['quickstyle'] = Identifier;
    theme_m['quickstyle'] = Identifier;
    pushChanges.push({ key: 'quickstyle', value: Identifier });

    this.setState({
      theme_r: theme_r,
      theme_m: theme_m,
      unsavedChanges: true,
      currentStyle: num,
      quickstyle: Identifier,
      changes: [...this.state.changes, pushChanges]
    });
  }

  hoverStyle(num) {
    this.setState({ hoveredStyle: num });
  }

  handleChange(op, key = null, newValue = null) {
    const { isMobile } = this.state;
    let theme_r = this.state.theme_r;
    let theme_m = this.state.theme_m;

    if (op === 'switchView') {
      if (this.state.isMobile) {
        this.setState({ isMobile: false });
      } else {
        this.setState({ isMobile: true });
      }
    }

    if (op === 'fontsize') {
      var mobile_key = this.state.isMobile ? 'mobile_' + key : key;

      const newChange = { key: mobile_key, value: newValue }

      if (!this.state.isMobile) {
        let theme_r = this.state.theme_r;
        theme_r[key] = newValue;
        this.setState({ theme_r: theme_r, unsavedChanges: true, changes: [...this.state.changes, newChange] });
      } else {
        let theme_m = this.state.theme_m;
        theme_m[key] = newValue;
        this.setState({ theme_m: theme_m, unsavedChanges: true, changes: [...this.state.changes, newChange] });
      }
    }

    if (op === 'fontFamily') {
      this.updateTheme(key, newValue);
    }

    if (op === 'fontColor') {
      this.updateTheme(key, newValue);
    }

    if (op === 'emailColor') {
      this.updateTheme(key, newValue);
    }

    if (op === 'globalAttr') {
      this.updateTheme(key, newValue);
    }

    if (op === 'setAttr') {
      if (isMobile) {
        theme_m[key] = newValue;
        const newChange = { key: 'mobile_' + key, value: newValue }
        this.setState({ theme_m: theme_m, unsavedChanges: true, changes: [...this.state.changes, newChange] });
      } else {
        theme_r[key] = newValue;
        const newChange = { key: key, value: newValue }
        this.setState({ theme_r: theme_r, unsavedChanges: true, changes: [...this.state.changes, newChange] });
      }
    }
  }

  updateTheme(key, newValue) {
    let theme_r = this.state.theme_r;
    let theme_m = this.state.theme_m;
    theme_r[key] = newValue;
    theme_m[key] = newValue;
    const newChange = { key: key, value: newValue }
    this.setState({ theme_r: theme_r, theme_m: theme_m, unsavedChanges: true, changes: [...this.state.changes, newChange] });
  }

  updateDimensions(key, nextValues) {
    let stateKey;
    let top;
    let bottom;

    const { isMobile } = this.state;
    if (key === 'container_padding') {
      top = nextValues.top;
      bottom = nextValues.bottom;
      stateKey = isMobile ? 'theme_m' : 'theme_r';
      let stateVal = this.state[stateKey];
      stateVal['container_padding1'] = top;
      stateVal['container_padding2'] = bottom;
      const newChange = isMobile ? { key: 'mobile_container_padding1', value: top } : { key: 'container_padding1', value: top };
      const newChange2 = isMobile ? { key: 'mobile_container_padding2', value: bottom } : { key: 'container_padding2', value: bottom };
      this.setState({ [stateKey]: stateVal, unsavedChanges: true, changes: [...this.state.changes, newChange, newChange2] });
    }
    if (key === 'container_margin') {
      top = nextValues.top;
      bottom = nextValues.bottom;
      stateKey = isMobile ? 'theme_m' : 'theme_r';
      let stateVal = this.state[stateKey];
      stateVal['container_margin1'] = top;
      stateVal['container_margin2'] = bottom;
      const newChange = isMobile ? { key: 'mobile_container_margin1', value: top } : { key: 'container_margin1', value: top };
      const newChange2 = isMobile ? { key: 'mobile_container_margin2', value: bottom } : { key: 'container_margin2', value: bottom };
      this.setState({ [stateKey]: stateVal, unsavedChanges: true, changes: [...this.state.changes, newChange, newChange2] });
    }
  }

  ajaxSave() {

    const { scope } = this.props;

    this.setState({
      isSaving: true,
      unsavedChanges: false,
    });

    var data = {
      changes: this.state.changes,
      customCSS: this.state.customCSS,
      post_id: wp.data.select("core/editor") ? wp.data.select("core/editor").getCurrentPostId() : 0
    };

    if (scope === 'single') {
      this.InlineSave(data);
    }

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/save_theme_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data,
    }).then(() => {

      this.setState({ isSaving: false, unsavedChanges: false, changes: [] });

    });

  }

  InlineSave(data) {
    let changes;
    if (data.changes) {
      if (data.changes[0] && data.changes[0].length) {
        changes = data.changes[0];
      } else {
        changes = data.changes;
      }
    }

    if (data && changes) {
      changes.forEach(function (change) {
        var thekey = change.key;
        var theval = change.value;
        if (thekey === 'email_bg') {
          jQuery('.editor-styles-wrapper.block-editor-writing-flow, div.editor-visual-editor div.editor-visual-editor__content-area').attr('style', function (i, s) { return (s || '') + 'background-color: ' + theval + ' !important;' });
        }
        if (thekey === 'container_bg') {
          jQuery('.is-root-container').attr('style', function (i, s) { return (s || '') + 'background-color: ' + theval + ' !important;' });
        }
        if (thekey === 'h1_font') {
          jQuery('.editor-styles-wrapper h1').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h2_font') {
          jQuery('.editor-styles-wrapper h2').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h3_font') {
          jQuery('.editor-styles-wrapper h3').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h4_font') {
          jQuery('.editor-styles-wrapper h4').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h5_font') {
          jQuery('.editor-styles-wrapper h5').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h6_font') {
          jQuery('.editor-styles-wrapper h6').attr('style', function (i, s) { return (s || '') + 'font-family: ' + theval + ' !important;' });
        }
        if (thekey === 'h1_colour') {
          jQuery('.editor-styles-wrapper h1').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'h2_colour') {
          jQuery('.editor-styles-wrapper h2').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'h3_colour') {
          jQuery('.editor-styles-wrapper h3').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'h4_colour') {
          jQuery('.editor-styles-wrapper h4').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'h5_colour') {
          jQuery('.editor-styles-wrapper h5').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'h6_colour') {
          jQuery('.editor-styles-wrapper h6').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
        if (thekey === 'p_colour') {
          jQuery('.ngl-article, .ngl-article-excerpt').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ';' });
          jQuery('.editor-styles-wrapper p').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ';' });
          jQuery('.editor-styles-wrapper li').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ';' });
        }
        if (thekey == 'container_padding1') {
          jQuery('.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout').attr('style', function (i, s) { return (s || '') + 'padding-top: ' + theval + ' !important;' });
        }
        if (thekey == 'container_padding2') {
          jQuery('.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout').attr('style', function (i, s) { return (s || '') + 'padding-bottom: ' + theval + ' !important;' });
        }
        if (thekey == 'container_margin1') {
          jQuery('.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout').attr('style', function (i, s) { return (s || '') + 'margin-top: ' + theval + ' !important;' });
        }
        if (thekey == 'container_margin2') {
          jQuery('.editor-styles-wrapper.block-editor-writing-flow div.is-root-container.block-editor-block-list__layout').attr('style', function (i, s) { return (s || '') + 'margin-bottom: ' + theval + ' !important;' });
        }
        if (thekey === 'a_colour') {
          jQuery('.ngl-article-title a').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
          jQuery('.editor-styles-wrapper .wp-block a').attr('style', function (i, s) { return (s || '') + 'color: ' + theval + ' !important;' });
        }
      });
    }
  }

  resetTheme() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/reset_theme_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: {

      },
    }).then(() => {

    });

  }

  componentDidUpdate() {
    if (this.state.unsavedChanges) {
      this.ajaxSave();
    }
  }

  render() {

    const { isMobile, customCSS, currentStyle, hoveredStyle } = this.state;

    let theme = isMobile ? this.state.theme_m : this.state.theme_r;

    var scope = this.props.scope && this.props.scope === 'single' ? 'single' : 'global';

    const closeIcon = <svg stroke="currentColor" fill="currentColor" strokeWidth="0" viewBox="0 0 16 16" height="24" width="24" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" clipRule="evenodd" d="M8 8.707l3.646 3.647.708-.707L8.707 8l3.647-3.646-.707-.708L8 7.293 4.354 3.646l-.707.708L7.293 8l-3.646 3.646.707.708L8 8.707z"></path></svg>;

    return (
      <>
        <div className="interface-interface-skeleton" style={{ top: scope == 'global' ? '210px' : '1px' }}>
          <SlotFillProvider>
            <Popover.Slot />
            <div className={`interface-interface-skeleton__editor interface-ngl__${scope}`}>

              <div className="interface-interface-skeleton__header">
                <div className="editor-header">
                  <div className="editor-header__toolbar">
                    {scope === 'single' && <img src={nglue_backend.logo_url} alt="" width={150} height={22} style={{ marginLeft: '22px' }} />}
                  </div>
                  <div className="editor-header__settings">
                    <NGSwitchView getState={this.state} handleChange={this.handleChange} />

                    {scope === 'single' &&
                      <Button
                        variant="link"
                        onClick={() => {
                          this.props.toggleModal();
                        }}
                        icon={closeIcon}
                        text={false}
                      >
                      </Button>
                    }

                  </div>
                </div>
              </div>

              <div className="interface-interface-skeleton__body">

                <div className="interface-interface-skeleton__content">
                  <NGEmailPreview getState={this.state} handleChange={this.handleChange} />
                </div>

                <div className="interface-interface-skeleton__sidebar">

                  <div className="interface-complementary-area editor-sidebar">

                    {scope === 'global' &&
                      <div className="block-editor-block-inspector" style={{ borderBottom: '1px solid #e0e0e0' }}>
                        <div className="block-editor-block-card">
                          <div className="block-editor-block-card__content">
                            <div className="block-editor-block-card__title">Global styles</div>
                            <div className="block-editor-block-card__description">
                              <p style={{ marginBottom: '8px' }}>Customize the global styles for all your newsletters.</p>
                              <p style={{ marginBottom: '8px' }}>To remove all email styling from web view, head to <a href={nglue_backend.additional_settings}>Settings {">"} Additional</a>.</p>
                              <p style={{ marginBottom: '0px' }}>You can also create individual <a href={nglue_backend.template_styles_link}>template styles</a> which will override the global styles you have chosen here.</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    }

                    {scope === 'single' &&
                      <div className="block-editor-block-inspector" style={{ borderBottom: '1px solid #e0e0e0' }}>
                        <div className="block-editor-block-card">
                          <div className="block-editor-block-card__content">
                            <div className="block-editor-block-card__title">Template styles</div>
                            <div className="block-editor-block-card__description">
                              <p style={{ marginBottom: '8px' }}>Customize the style of this template.</p>
                              <p style={{ marginBottom: '0px' }}>Head <a href={nglue_backend.global_styles_link}>here</a> to customize global styles instead.</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    }

                    <NavigatorProvider initialPath="/">
                      <NavigatorScreen path="/">
                        <Panel>

                          <PanelBody title={__('Quick styles', 'newsletter-glue')} initialOpen={true}>
                            <PanelRow>
                              <p className="edit-site-global-styles-header__description" style={{ padding: 0 }}>
                                Choose a different style combination for the newsletter styles
                              </p>
                            </PanelRow>

                            <Card elevation={0} isBorderless className="ngl-card-grid">
                              <CardBody style={{ padding: '20px 0' }}>
                                <Grid columns={2}>

                                  {Object.keys(theme.styles).map(function (childStyle, k) {
                                    var id = childStyle + 1;
                                    let style = theme.styles[childStyle];
                                    return (
                                      <div key={`ngl-quickstyle-${id}`} className={`edit-site-global-styles-variations_item ${currentStyle === id || this.state.quickstyle == k ? 'is-active' : ''}`} role="button" tabIndex={0} onClick={() => this.quickStyle(id, k)} onMouseEnter={() => this.hoverStyle(id)} onMouseLeave={() => this.hoverStyle(0)}>
                                        {hoveredStyle !== id &&
                                          <div className="edit-site-global-styles-variations_item-preview" style={{ backgroundColor: style.bg }}>
                                            <div className="edit-site-global-styles-inner" style={{ backgroundColor: style.content }}>
                                              <div className="ngl-style-typo" style={{ color: style.heading, fontFamily: style.font_h ? style.font_h : 'inherit' }}>Aa</div>
                                              <div className="ngl-style-round">
                                                <div className="ngl-style-round-1" style={{ backgroundColor: style.heading }}></div>
                                                <div className="ngl-style-round-2" style={{ backgroundColor: style.button }}></div>
                                              </div>
                                            </div>
                                          </div>}
                                        {hoveredStyle === id &&
                                          <div className="edit-site-global-styles-variations_item-preview" style={{ backgroundColor: style.bg }}>
                                            <div className="edit-site-global-styles-inner is-vertical" style={{ backgroundColor: style.content }}>
                                              <div className="ngl-style-name" style={{ color: style.heading, fontFamily: style.font_h ? style.font_h : 'inherit' }}>{style.name}</div>
                                              <div className="ngl-style-tagline" style={{ color: style.p, fontFamily: style.font_p ? style.font_p : 'inherit' }}>Aa Aa</div>
                                              <div className="ngl-style-colors">
                                                <div className="ngl-style-color" style={{ backgroundColor: style.button }}></div>
                                                <div className="ngl-style-color" style={{ backgroundColor: style.button }}></div>
                                                <div className="ngl-style-color" style={{ backgroundColor: style.button }}></div>
                                                <div className="ngl-style-color" style={{ backgroundColor: style.button }}></div>
                                              </div>
                                            </div>
                                          </div>}
                                      </div>
                                    )
                                  }.bind(this))}

                                </Grid>
                              </CardBody>
                            </Card>
                          </PanelBody>

                          <PanelBody title={__('Layout', 'newsletter-glue')} initialOpen={true}>
                            <ItemGroup className="nglue-group">
                              <Item>
                                <NavigatorButton
                                  path="/colors/background"
                                >
                                  <HStack justify="flex-start">
                                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                                      <ColorIndicator colorValue={theme.email_bg} />
                                    </Flex>
                                    <FlexItem>Background</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/colors/content"
                                >
                                  <HStack justify="flex-start">
                                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                                      <ColorIndicator colorValue={theme.container_bg} />
                                    </Flex>
                                    <FlexItem>Content area</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                            </ItemGroup>
                            <BaseControl className="nglue-box-control">
                              <BoxControl
                                label={__('Padding', 'newsletter-glue')}
                                values={{
                                  top: theme.container_padding1,
                                  bottom: theme.container_padding2,
                                }}
                                allowReset={true}
                                resetValues={{ top: '5px', bottom: '15px' }}
                                sides={['top', 'bottom']}
                                units={[{ value: 'px', label: 'px', default: 0 }]}
                                onChange={(nextValues) => {
                                  this.updateDimensions('container_padding', nextValues);
                                }}
                              />
                            </BaseControl>
                            <BaseControl className="nglue-box-control">
                              <BoxControl
                                label={__('Margin', 'newsletter-glue')}
                                values={{
                                  top: theme.container_margin1,
                                  bottom: theme.container_margin2,
                                }}
                                allowReset={true}
                                resetValues={{ top: '0px', bottom: '0px' }}
                                sides={['top', 'bottom']}
                                units={[{ value: 'px', label: 'px', default: 0 }]}
                                onChange={(nextValues) => {
                                  this.updateDimensions('container_margin', nextValues);
                                }}
                              />
                            </BaseControl>
                          </PanelBody>

                          <PanelBody title={__('Typography', 'newsletter-glue')} initialOpen={true}>
                            <ItemGroup className="nglue-group">
                              <Item>
                                <NavigatorButton
                                  path="/typography/h1"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator" style={{ fontWeight: 'bold' }}>Aa</FlexItem>
                                    <FlexItem>Heading 1</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/typography/h2"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator" style={{ fontWeight: 'bold' }}>Aa</FlexItem>
                                    <FlexItem>Heading 2</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/typography/h3"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator" style={{ fontWeight: 'bold' }}>Aa</FlexItem>
                                    <FlexItem>Heading 3</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/typography/h4"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator" style={{ fontWeight: 'bold' }}>Aa</FlexItem>
                                    <FlexItem>Heading 4, 5, 6</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/typography/paragraph"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator">Aa</FlexItem>
                                    <FlexItem>Paragraph</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                              <Item>
                                <NavigatorButton
                                  path="/typography/links"
                                >
                                  <HStack justify="flex-start">
                                    <FlexItem className="edit-site-global-styles-screen-typography__indicator" style={{ textDecoration: 'underline' }}>Aa</FlexItem>
                                    <FlexItem>Links</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                            </ItemGroup>
                          </PanelBody>

                          <PanelBody title={__('Additional', 'newsletter-glue')} initialOpen={true}>
                            <ItemGroup className="nglue-group">
                              <Item>
                                <NavigatorButton
                                  path="/button"
                                >
                                  <HStack justify="flex-start">
                                    <Flex className="edit-site-global-styles__color-indicator-wrapper">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="19" height="11" viewBox="0 0 19 11">
                                        <g id="Group_61" data-name="Group 61" transform="translate(-1572 -745)">
                                          <rect id="Rectangle_27" data-name="Rectangle 27" width="19" height="11" rx="2" transform="translate(1572 745)" fill="#1e1e1e" />
                                          <line id="Line_12" data-name="Line 12" x2="7.5" transform="translate(1577.75 750.5)" fill="none" stroke="#fff" strokeWidth="1" />
                                        </g>
                                      </svg>
                                    </Flex>
                                    <FlexItem>Button</FlexItem>
                                  </HStack>
                                </NavigatorButton>
                              </Item>
                            </ItemGroup>
                          </PanelBody>

                          <PanelBody title={__('Custom CSS', 'newsletter-glue')} initialOpen={true} className="ngl-setting-textarea">
                            <PanelRow>
                              <BaseControl>
                                <textarea
                                  className="components-textarea-control__input"
                                  id="nglue-custom-css"
                                  rows={12}
                                  onChange={e => this.setState({ unsavedChanges: true, customCSS: e.target.value })}
                                  value={customCSS}
                                  placeholder={__('Enter custom CSS here...', 'newsletter-glue')}
                                />
                              </BaseControl>
                            </PanelRow>
                          </PanelBody>
                        </Panel>
                      </NavigatorScreen>

                      <NGScreens getState={this.state} handleChange={this.handleChange} />

                    </NavigatorProvider>

                  </div>

                </div>

              </div>
            </div>
          </SlotFillProvider>
        </div>
      </>
    );

  }

}

var rootElement = document.getElementById('ngl-global-styles');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<GlobalStyles scope="global" />);
}