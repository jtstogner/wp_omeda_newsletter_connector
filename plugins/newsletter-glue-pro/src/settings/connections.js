import { __, sprintf } from "@wordpress/i18n";
import React from "react";

import apiFetch from "@wordpress/api-fetch";

import { Component } from "@wordpress/element";

import {
  BaseControl,
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
  __experimentalSpacer as Spacer,
} from "@wordpress/components";

import Select from "react-select";

import {
  iconAdd,
  iconCheck,
  iconChevDown,
  iconChevronDown,
  iconChevronUp,
  iconIssue,
} from "../common/icons";

import Loader from "../common/loader";

const iconUri = nglue_backend.images_uri + "icon-connections.svg";

export default class Connections extends Component {
  constructor(props) {
    super(props);

    this.changeConnectionName = this.changeConnectionName.bind(this);
    this.attemptConnect = this.attemptConnect.bind(this);
    this.removeConnection = this.removeConnection.bind(this);
    this.saveESP = this.saveESP.bind(this);
    this.updateItems = this.updateItems.bind(this);

    const data = {
      currentConnectionName: "",
      isAPILoaded: false,
      setupEsp: false,
      esps: "",
      connectUpgrade: false,
      newEsp: "",
      selectedEsp: "",
      getAPIURL: "",
      allowESPinputs: false,
      espAPIKey: "",
      espAPIURL: "",
      espAPISecret: "",
      isConnecting: false,
      connectionStatus: 0,
      enableTest: false,
      connectionName: "",
      connectionIcon: "",
      connectionState: "",
      fromEmail: "",
      fromName: "",
      utm_source: "",
      utm_campaign: "",
      utm_medium: "",
      utm_content: "",
      hasConnected: false,
      showURLfield: false,
      showSecretfield: false,
      isToggled: true,
      disableSelect: "",
      updatingSegments: false,
      options: "",
      isSaving: false,
      hasSaved: false,
      showConnected: false,
      isInvalidEmail: false,
      isInvalidName: false,
      unsavedChanges: false,
      showMoreSettings: false,
    };

    this.state = data;
  }

  changeConnectionName(e) {
    var name = e.currentTarget.textContent;

    const { newEsp, currentConnectionName } = this.state;

    const data = {
      esp: newEsp,
      name: name,
    };

    if (name == currentConnectionName) {
      return false;
    }

    this.setState({
      currentConnectionName: name,
    });

    apiFetch({
      path:
        "newsletterglue/" +
        nglue_backend.api_version +
        "/update_connection_name",
      method: "post",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
      data: data,
    }).then(() => {});
  }

  attemptConnect() {
    this.setState({
      isConnecting: true,
      connectionStatus: 0,
      enableTest: false,
      allowESPinputs: false,
    });

    apiFetch({
      path:
        "newsletterglue/" + nglue_backend.api_version + "/verify_connection",
      method: "post",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
      data: this.state,
    }).then((d) => {
      if (d.response === "successful") {
        this.setState({
          isConnecting: false,
          connectionStatus: 1,
          allowESPinputs: true,
          connectionName: d.connection_name,
          connectionIcon: d.connection_icon,
          connectionState: d.connection_state,
          fromEmail: d.from_email,
          fromName: d.from_name,
          utm_source: d.utm_source,
          utm_campaign: d.utm_campaign,
          utm_medium: d.utm_medium,
          utm_content: d.utm_content,
          schedule: d.schedule,
          options: d.options,
          showConnected: true,
        });

        setTimeout(() => {
          if (!this.state.enableTest) {
            this.setState({
              enableTest: true,
              showConnected: false,
            });
          }
        }, 1500);
      } else {
        this.setState({
          isConnecting: false,
          connectionStatus: 2,
          allowESPinputs: false,
        });
      }
    });
  }

  removeConnection() {
    this.setState({
      isConnecting: false,
      connectionStatus: 0,
      enableTest: false,
      espAPIKey: "",
      espAPISecret: "",
      espAPIURL: "",
      allowESPinputs: false,
      connectionName: "",
      selectedEsp: null,
      fromName: "",
      fromEmail: "",
      hasConnected: false,
      setupEsp: false,
      options: "",
    });

    apiFetch({
      path:
        "newsletterglue/" + nglue_backend.api_version + "/remove_connection",
      method: "get",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
    }).then(() => {});
  }

  saveESP() {
    this.setState({
      isSaving: true,
    });

    const data = {
      esp: this.state.newEsp,
      from_name: this.state.fromName,
      from_email: this.state.fromEmail,
      schedule: this.state.schedule,
      utm_source: this.state.utm_source,
      utm_campaign: this.state.utm_campaign,
      utm_medium: this.state.utm_medium,
      utm_content: this.state.utm_content,
      options: this.state.options,
    };

    console.log(data);

    apiFetch({
      path: "newsletterglue/" + nglue_backend.api_version + "/save_esp",
      method: "post",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
      data: data,
    }).then((d) => {
      setTimeout(() => {
        this.setState({
          isSaving: false,
          hasSaved: true,
          isInvalidEmail: d.is_invalid_email ? true : false,
          isInvalidName: d.is_invalid_name ? true : false,
        });
      }, 1500);

      setTimeout(() => {
        this.setState({
          hasSaved: false,
          isInvalidEmail: d.is_invalid_email ? true : false,
          isInvalidName: d.is_invalid_name ? true : false,
          unsavedChanges: false,
        });
      }, 2500);
    });
  }

  updateItems(child, selected) {
    this.setState({
      updatingSegments: true,
      disableSelect: child,
      isValidated: false,
    });

    let callback = this.state.options[child]["callback"];

    apiFetch({
      path:
        "newsletterglue/" +
        nglue_backend.api_version +
        "/get_esp_items?esp=" +
        this.state.newEsp +
        "&parameter=" +
        selected.value +
        "&callback=" +
        callback,
      method: "get",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
    }).then((response) => {
      let new_state = this.state;

      new_state.options[child]["items"] = response;
      new_state.options[child]["value"] = "";
      new_state.options[child]["default"] = "";
      new_state.updatingSegments = false;
      new_state.disableSelect = "";
      new_state.isValidated = true;

      this.setState(new_state);
    });
  }

  handleChange(name, event) {
    var _newoptions = this.state.options;
    _newoptions[name]["value"] = event.target.value;
    this.setState({ unsavedChanges: true, options: _newoptions });
  }

  componentDidMount() {
    apiFetch({
      path: "newsletterglue/" + nglue_backend.api_version + "/get_settings",
      method: "get",
      headers: {
        "NEWSLETTERGLUE-API-KEY": nglue_backend.api_key,
      },
    }).then((response) => {
      const data = [];

      data.isAPILoaded = true;

      for (var key in response) {
        data[key] = response[key];
      }

      if (!response.hasConnected) {
        data.isToggled = false;
      }

      console.log(data);
      this.setState(data);
    });
  }

  render() {
    const {
      isAPILoaded,
      connectionStatus,
      showConnected,
      enableTest,
      hasConnected,
      isConnecting,
      hasSaved,
      options,
      disableSelect,
      updatingSegments,
      connectionName,
      isToggled,
      connectionIcon,
      connectionState,
      selectedEsp,
      connectUpgrade,
      setupEsp,
      espAPISecret,
      espAPIKey,
      getAPIURL,
      showURLfield,
      showSecretfield,
      espAPIURL,
      allowESPinputs,
      fromName,
      fromEmail,
      utm_source,
      utm_campaign,
      utm_medium,
      utm_content,
      esps,
      isSaving,
      isInvalidEmail,
      isInvalidName,
      unsavedChanges,
      showMoreSettings,
    } = this.state;

    const { notLicensed } = this.props.getState;

    const panelClass = notLicensed ? "" : "";

    var connectBtnClass = "is-primary";
    if (connectionStatus === 1) {
      if (showConnected) {
        connectBtnClass = "nglue-btn-valid";
      } else {
        if (enableTest || hasConnected) {
          connectBtnClass = "is-tertiary";
        } else {
          connectBtnClass = "nglue-btn-valid";
        }
      }
    } else if (isConnecting) {
      connectBtnClass = "nglue-btn-wait";
    }

    var connectBtnText = "";
    if (connectionStatus === 1) {
      if (showConnected) {
        connectBtnText = __("Connected", "newsletter-glue");
      } else {
        if (enableTest || hasConnected) {
          connectBtnText = __("Test", "newsletter-glue");
        } else {
          connectBtnText = __("Connected", "newsletter-glue");
        }
      }
    } else if (!isConnecting) {
      connectBtnText = __("Connect", "newsletter-glue");
    } else {
      connectBtnText = __("Connecting...", "newsletter-glue");
    }

    var saveBtnClass = "is-primary";
    if (hasSaved) {
      saveBtnClass = "nglue-btn-valid";
    } else if (isSaving) {
      saveBtnClass = "nglue-btn-wait";
    }

    var saveBtnText = "";
    if (hasSaved == 1) {
      saveBtnText = __("Saved", "newsletter-glue");
    } else if (!isSaving) {
      saveBtnText = __("Save", "newsletter-glue");
    } else {
      saveBtnText = __("Saving...", "newsletter-glue");
    }

    var viewOptions = [];

    for (var option in options) {
      var item = options[option];

      var espValue = selectedEsp && selectedEsp.value ? selectedEsp.value : "";

      viewOptions.push(
        <BaseControl
          key={`nglue-option-${option}`}
          label={item.title}
          id={option}
          className={`nglue-base nglue-esp-select nglue-field-${option} nglue-esp-select-${espValue}`}
        >
          {item.type == "select" && (
            <Select
              name={option}
              inputId={option}
              isMulti={item.is_multi}
              classNamePrefix="nglue"
              options={item.items}
              value={item.value || item.is_multi ? item.value : item.default}
              isDisabled={disableSelect === option && updatingSegments}
              isLoading={disableSelect === option && updatingSegments}
              placeholder={item.placeholder}
              isClearable={false}
              isOptionDisabled={(option) => option.disabled}
              onChange={(selected, action) => {
                var _newoptions = options;

                if (selected === _newoptions[action.name]["value"]) {
                  return;
                }

                _newoptions[action.name]["value"] = selected;
                this.setState({ unsavedChanges: true, options: _newoptions });
                if (_newoptions[action.name]["onchange"]) {
                  this.updateItems(
                    _newoptions[action.name]["onchange"],
                    selected
                  );
                }
              }}
            />
          )}
          {item.type == "text" && (
            <div className="nglue-esp-input">
              <input
                type="text"
                id={option}
                name={option}
                className={`nglue-base nglue-esp-input-in`}
                value={item.value ? item.value : item.default}
                onChange={this.handleChange.bind(this, option)}
              />
            </div>
          )}
          {item.help && (
            <div
              className="nglue-input-help b"
              dangerouslySetInnerHTML={{ __html: item.help }}
            ></div>
          )}
        </BaseControl>
      );
    }

    var thelabel = "";
    if (selectedEsp && selectedEsp.label) {
      thelabel = selectedEsp.label;
    }

    const scheduleOptions = [
      { value: "immediately", label: __("Send now", "newsletter-glue") },
      {
        value: "draft",
        label: sprintf(
          __("Save as a draft in %s", "newsletter-glue"),
          thelabel
        ),
      },
      {
        value: "schedule_draft",
        label: sprintf(
          __(
            "Immediately save draft in %s when post scheduled",
            "newsletter-glue"
          ),
          thelabel
        ),
      },
    ];

    var newsletterTitleTag = "{{newsletter_title}}";

    var apiSecretLabel =
      selectedEsp &&
      selectedEsp.value &&
      selectedEsp.value === "campaignmonitor"
        ? "Client ID"
        : "API secret";

    return (
      <>
        <div className="nglue-main">
          <PanelBody className={panelClass}>
            <div className={`nglue-title-bar ${panelClass}`}>
              <div className="nglue-title">
                <span className="nglue-title-main">
                  {__("Connect", "newsletter-glue")}
                </span>
                <span className="nglue-title-sub">
                  {__(
                    "Connect email service provider to send newsletters.",
                    "newsletter-glue"
                  )}
                </span>
              </div>
              <div className="nglue-title-icon">
                <img src={iconUri} />
              </div>
            </div>

            {!isAPILoaded && <Loader />}

            {isAPILoaded && (
              <div className={`nglue-panel-body ${panelClass}`}>
                <PanelRow>
                  {connectionName && (
                    <div
                      className={
                        "nglue-placeholder " +
                        (isToggled
                          ? "nglue-placeholder-collapsed"
                          : "nglue-placeholder-open")
                      }
                      onClick={(e) => {
                        if (e.target.className === "nglue-editable-name") {
                          return false;
                        }
                        if (isToggled) {
                          this.setState({ isToggled: false });
                        } else {
                          this.setState({ isToggled: true });
                        }
                      }}
                    >
                      <div className="nglue-hc-icon">
                        <img src={connectionIcon} />
                      </div>
                      <div
                        className="nglue-editable-name"
                        contentEditable
                        spellCheck={false}
                        dangerouslySetInnerHTML={{ __html: connectionName }}
                        onBlur={this.changeConnectionName}
                      ></div>
                      {isConnecting && (
                        <div className="nglue-hc-state nglue-hc-state-wait">
                          {__("Connecting...", "newsletter-glue")}
                        </div>
                      )}
                      {!isConnecting && connectionStatus == 1 && (
                        <div className="nglue-hc-state">
                          {iconCheck} {connectionState}
                        </div>
                      )}
                      {!isConnecting && connectionStatus == 2 && (
                        <div className="nglue-hc-state nglue-hc-state-failed">
                          {iconIssue} {__("Failed", "newsletter-glue")}
                        </div>
                      )}
                      <div className="nglue-hc-collapse">{iconChevDown}</div>
                    </div>
                  )}
                  {!connectionName && (
                    <div className="nglue-placeholder">
                      {__("Make your first connection", "newsletter-glue")}
                    </div>
                  )}
                </PanelRow>
                <div
                  className={
                    "nglue-section " +
                    `nglue-collapse nglue-collapse-${isToggled}`
                  }
                >
                  <form autoComplete="off">
                    <PanelRow
                      key={`nglue-esp-new ` + (!esps && "nglue-disabled-row")}
                    >
                      <BaseControl
                        label={__("Integration", "newsletter-glue")}
                        id="nglue-esp-select"
                        className={`nglue-base nglue-esp-select`}
                      >
                        <Select
                          name="nglue-esp-select"
                          inputId="nglue-esp-select"
                          classNamePrefix="nglue"
                          options={esps}
                          placeholder={__(
                            "Select from list",
                            "newsletter-glue"
                          )}
                          isClearable={false}
                          value={selectedEsp}
                          isDisabled={
                            isConnecting ||
                            hasConnected ||
                            connectionStatus == 1
                          }
                          inputProps={{ autoComplete: "new-password" }}
                          onChange={(selected) => {
                            if (selected) {
                              if (selected.upgrade == false) {
                                this.setState({
                                  setupEsp: true,
                                  connectUpgrade: false,
                                  newEsp: selected.value,
                                  getAPIURL: selected.api_src,
                                  selectedEsp: selected,
                                  espAPIKey: "",
                                  espAPIURL: "",
                                  espAPISecret: "",
                                  showURLfield: selected.url_field,
                                  showSecretfield: selected.secret_field,
                                });
                              } else {
                                this.setState({
                                  setupEsp: false,
                                  connectUpgrade: true,
                                  newEsp: selected.value,
                                  selectedEsp: selected,
                                  espAPIKey: "",
                                  espAPIURL: "",
                                  espAPISecret: "",
                                  showURLfield: selected.url_field,
                                  showSecretfield: selected.secret_field,
                                });
                              }
                            } else {
                              this.setState({
                                setupEsp: false,
                                connectUpgrade: false,
                                selectedEsp: "",
                                espAPIKey: "",
                              });
                            }
                          }}
                          getOptionLabel={(e) => (
                            <div className="nglue-option">
                              <span className="nglue-option-icon">
                                <img src={e.icon} alt="" title="" />
                              </span>
                              <span className="nglue-option-label">
                                {e.label}
                              </span>
                              {e.upgrade != false && (
                                <span className="nglue-option-note">
                                  {__("Upgrade", "newsletter-glue")}
                                </span>
                              )}
                            </div>
                          )}
                        />
                      </BaseControl>
                    </PanelRow>

                    {connectUpgrade && (
                      <>
                        <PanelRow className="nglue-notice-box">
                          <div className="nglue-notice">
                            <div>
                              <div className="nglue-tier-notice-title">
                                Upgrade to unlock {thelabel} integration
                              </div>
                              <div className="nglue-tier-notice-desc">
                                Log into your account to see pro-rated pricing
                                and upgrade.
                              </div>
                              <div className="nglue-tier-notice-btn">
                                <Button
                                  isPrimary
                                  href={nglue_backend.upgrade_link}
                                  target="_blank"
                                >
                                  {__("Upgrade my account", "newsletter-glue")}
                                </Button>
                              </div>
                            </div>
                            <div>
                              <div className="nglue-tier-notice-desc">
                                Here are the new features you will get when you
                                upgrade:
                              </div>
                              <div className="nglue-tier-features">
                                <div>
                                  {iconCheck} Access all our email integrations
                                </div>
                                <div>{iconCheck} Static site compatibility</div>
                                <div>
                                  {iconCheck} User roles & permissions
                                  management for editorial teams
                                </div>
                                <div>{iconCheck} Pro-rated pricing</div>
                              </div>
                            </div>
                          </div>
                        </PanelRow>
                      </>
                    )}

                    {!connectUpgrade && (
                      <>
                        <PanelRow
                          className={
                            !setupEsp && !hasConnected && "nglue-disabled-row"
                          }
                        >
                          <BaseControl
                            label={
                              (selectedEsp && selectedEsp.key_name) ||
                              sprintf(
                                __("%s API key", "newsletter-glue"),
                                thelabel
                              )
                            }
                            id="nglue-api-key"
                            className={`nglue-base nglue-esp-input`}
                          >
                            <input
                              type="password"
                              id="nglue-api-key"
                              value={espAPIKey}
                              onChange={(e) =>
                                this.setState({ espAPIKey: e.target.value })
                              }
                              disabled={isConnecting}
                              autoComplete="new-password"
                            />
                            {getAPIURL != "none" && (
                              <div className="nglue-input-help">
                                <ExternalLink href={getAPIURL}>
                                  {__("Get API key", "newsletter-glue")}
                                </ExternalLink>
                              </div>
                            )}
                          </BaseControl>
                        </PanelRow>

                        {showSecretfield && (
                          <PanelRow
                            className={
                              !setupEsp && !hasConnected && "nglue-disabled-row"
                            }
                          >
                            <BaseControl
                              label={
                                (selectedEsp && selectedEsp.secret_name) ||
                                sprintf(
                                  __(`%s ${apiSecretLabel}`, "newsletter-glue"),
                                  thelabel
                                )
                              }
                              id="nglue-api-secret"
                              className={`nglue-base nglue-esp-input`}
                            >
                              <input
                                type="password"
                                id="nglue-api-secret"
                                value={espAPISecret}
                                onChange={(e) =>
                                  this.setState({
                                    espAPISecret: e.target.value,
                                  })
                                }
                                disabled={isConnecting}
                                autoComplete="new-password"
                              />
                            </BaseControl>
                          </PanelRow>
                        )}

                        {showURLfield && (
                          <PanelRow
                            className={
                              !setupEsp && !hasConnected && "nglue-disabled-row"
                            }
                          >
                            <BaseControl
                              label={
                                (selectedEsp && selectedEsp.url_name) ||
                                sprintf(
                                  __("%s API URL", "newsletter-glue"),
                                  thelabel
                                )
                              }
                              id="nglue-api-url"
                              className={`nglue-base nglue-esp-input`}
                            >
                              <input
                                type="text"
                                id="nglue-api-url"
                                value={espAPIURL}
                                onChange={(e) =>
                                  this.setState({ espAPIURL: e.target.value })
                                }
                                disabled={isConnecting}
                              />
                              {selectedEsp && selectedEsp.url_help && (
                                <div className="nglue-input-help">
                                  {selectedEsp.url_help}
                                </div>
                              )}
                            </BaseControl>
                          </PanelRow>
                        )}

                        <PanelRow className="nglue-buttons">
                          <Button
                            disabled={
                              (!setupEsp && !hasConnected) || !espAPIKey
                            }
                            onClick={this.attemptConnect}
                            className={connectBtnClass}
                            style={{ margin: "0 10px 0 0" }}
                            icon={
                              (connectionStatus === 1 &&
                                !enableTest &&
                                !hasConnected &&
                                iconCheck) ||
                              (showConnected && iconCheck)
                            }
                          >
                            {connectBtnText}
                          </Button>

                          {isConnecting && (
                            <Button
                              isLink
                              onClick={() =>
                                this.setState({ isConnecting: false })
                              }
                            >
                              {__("Cancel connecting", "newsletter-glue")}
                            </Button>
                          )}

                          {connectionStatus > 0 && (
                            <Button
                              isLink
                              onClick={() => {
                                this.removeConnection();
                              }}
                            >
                              {__("Remove connection", "newsletter-glue")}
                            </Button>
                          )}

                          {connectionStatus == 2 && (
                            <div className="nglue-form-err">
                              {__(
                                "API connection failed. Please make sure your API key is valid.",
                                "newsletter-glue"
                              )}
                            </div>
                          )}
                        </PanelRow>

                        <div style={{ height: "20px" }} />

                        <PanelRow
                          className={
                            (!allowESPinputs || isSaving) &&
                            "nglue-disabled-row"
                          }
                        >
                          <div className="nglue-head-part">
                            <div className="nglue-head">
                              {__("Email defaults", "newsletter-glue")}
                            </div>
                            <div className="nglue-subheading">
                              <span>
                                {__(
                                  "New newsletters will default to the details you’ve chosen here.",
                                  "newsletter-glue"
                                )}
                              </span>
                              <span>
                                {__(
                                  "Change details for individual newsletters at the bottom of each new post.",
                                  "newsletter-glue"
                                )}
                              </span>
                            </div>
                          </div>
                        </PanelRow>

                        {options && (
                          <PanelRow
                            className={
                              (!allowESPinputs || isSaving) &&
                              "nglue-disabled-row"
                            }
                          >
                            {viewOptions}
                          </PanelRow>
                        )}

                        {!options && (
                          <PanelRow
                            className={
                              (!allowESPinputs || isSaving) &&
                              "nglue-disabled-row"
                            }
                          >
                            <BaseControl
                              label={__("Lists", "newsletter-glue")}
                              id="nglue-api-list"
                              className={`nglue-base nglue-esp-input`}
                            >
                              <input type="text" id="nglue-api-list" />
                              <div className="nglue-input-help">
                                {__(
                                  "Who receives your email.",
                                  "newsletter-glue"
                                )}
                              </div>
                            </BaseControl>
                            <BaseControl
                              label={__("Segment/tag", "newsletter-glue")}
                              id="nglue-api-segment"
                              className={`nglue-base nglue-esp-input`}
                            >
                              <input type="text" id="nglue-api-segment" />
                              <div className="nglue-input-help">
                                {__(
                                  "A subset of subscribers.",
                                  "newsletter-glue"
                                )}
                              </div>
                            </BaseControl>
                          </PanelRow>
                        )}

                        <PanelRow
                          className={
                            (!allowESPinputs || isSaving) &&
                            "nglue-disabled-row"
                          }
                        >
                          <BaseControl
                            label={__("From name", "newsletter-glue")}
                            id="nglue-api-from-name"
                            className={
                              `nglue-base nglue-esp-input ` +
                              (isInvalidName ? "nglue-esp-invalid" : "")
                            }
                          >
                            <input
                              type="text"
                              id="nglue-api-from-name"
                              value={fromName}
                              onChange={(e) => {
                                this.setState({
                                  unsavedChanges: true,
                                  fromName: e.target.value,
                                });
                              }}
                            />
                            <div className="nglue-input-help">
                              {__(
                                "Your subscribers will see this name in their inboxes.",
                                "newsletter-glue"
                              )}
                            </div>
                          </BaseControl>
                          <BaseControl
                            label={__("From email", "newsletter-glue")}
                            id="nglue-api-from-email"
                            className={
                              `nglue-base nglue-esp-input ` +
                              (isInvalidEmail ? "nglue-esp-invalid" : "")
                            }
                          >
                            <input
                              type="text"
                              id="nglue-api-from-email"
                              value={fromEmail}
                              onChange={(e) =>
                                this.setState({
                                  unsavedChanges: true,
                                  fromEmail: e.target.value,
                                })
                              }
                            />
                            <div className="nglue-input-help">
                              {__(
                                "Subscribers will see and reply to this email address.",
                                "newsletter-glue"
                              )}
                              <br />
                              {__(
                                "Only use verified email addresses.",
                                "newsletter-glue"
                              )}{" "}
                              <ExternalLink href="https://newsletterglue.com/docs/from-email-use-verified-email-address/">
                                {__("Learn more", "newsletter-glue")}
                              </ExternalLink>
                            </div>
                          </BaseControl>
                        </PanelRow>

                        <PanelRow
                          className={
                            (!allowESPinputs || isSaving) &&
                            "nglue-disabled-row"
                          }
                        >
                          <a
                            href="#"
                            className="ngl-edit-link"
                            onClick={(e) => {
                              e.preventDefault();
                              if (!showMoreSettings) {
                                this.setState({ showMoreSettings: true });
                              } else {
                                this.setState({ showMoreSettings: false });
                              }
                            }}
                          >
                            Edit more settings{" "}
                            {showMoreSettings ? iconChevronUp : iconChevronDown}
                          </a>
                        </PanelRow>

                        {showMoreSettings && (
                          <>
                            <PanelRow className="nglue-simple-row">
                              <div className="nglue-head-part">
                                <div className="nglue-head">
                                  {__("Send or save", "newsletter-glue")}
                                </div>
                                <div className="nglue-subheading">
                                  <span>
                                    {sprintf(
                                      __(
                                        "When you click publish, decide if your emails should send right away, or get saved as a draft inside %s.",
                                        "newsletter-glue"
                                      ),
                                      thelabel
                                    )}
                                  </span>
                                </div>
                              </div>
                            </PanelRow>

                            <PanelRow
                              className={
                                (!allowESPinputs || isSaving) &&
                                "nglue-disabled-row"
                              }
                            >
                              <BaseControl
                                label={__(
                                  "Send now or save for later",
                                  "newsletter-glue"
                                )}
                                id="nglue-schedule-select"
                                className={`nglue-base nglue-esp-select`}
                              >
                                <Select
                                  name="nglue-schedule-select"
                                  inputId="nglue-schedule-select"
                                  classNamePrefix="nglue"
                                  options={scheduleOptions}
                                  isClearable={false}
                                  value={scheduleOptions.find(
                                    (obj) => obj.value === this.state.schedule
                                  )}
                                  onChange={(selected) => {
                                    this.setState({
                                      unsavedChanges: true,
                                      schedule: selected.value,
                                    });
                                  }}
                                />
                              </BaseControl>
                            </PanelRow>

                            <Spacer marginBottom={4} />

                            <PanelRow className="nglue-simple-row">
                              <div className="nglue-head-part">
                                <div className="nglue-head">
                                  {__("UTM builder", "newsletter-glue")}
                                </div>
                                <div className="nglue-subheading">
                                  <span>
                                    {__(
                                      "Generate UTM codes to track subscribers when they click on links in your emails.",
                                      "newsletter-glue"
                                    )}
                                  </span>
                                  <span>
                                    {__(
                                      "We will automatically add these codes to the end of each URL inside your emails. You can change UTM codes for individual newsletters or templates.",
                                      "newsletter-glue"
                                    )}
                                  </span>
                                  <span>
                                    <a href="#">
                                      {__("Learn more.", "newsletter-glue")}
                                    </a>
                                  </span>
                                </div>
                              </div>
                            </PanelRow>

                            <PanelRow
                              className={
                                (!allowESPinputs || isSaving) &&
                                "nglue-disabled-row"
                              }
                            >
                              <BaseControl
                                label={__("UTM Source", "newsletter-glue")}
                                id="nglue-utm-source"
                                className={`nglue-base nglue-esp-input`}
                              >
                                <input
                                  type="text"
                                  id="nglue-utm-source"
                                  value={utm_source}
                                  onChange={(e) => {
                                    this.setState({
                                      unsavedChanges: true,
                                      utm_source: e.target.value,
                                    });
                                  }}
                                />
                                <div className="nglue-input-help">
                                  {__("e.g. newsletter, ng", "newsletter-glue")}
                                </div>
                              </BaseControl>
                              <BaseControl
                                label={__("UTM Campaign", "newsletter-glue")}
                                id="nglue-utm-campaign"
                                className={`nglue-base nglue-esp-input`}
                              >
                                <input
                                  type="text"
                                  id="nglue-utm-campaign"
                                  value={utm_campaign}
                                  onChange={(e) => {
                                    this.setState({
                                      unsavedChanges: true,
                                      utm_campaign: e.target.value,
                                    });
                                  }}
                                />
                                <div className="nglue-input-help">
                                  {__(
                                    "e.g. weekly_update, ",
                                    "newsletter-glue"
                                  )}
                                  <a
                                    href="#"
                                    className="nglue-input-var"
                                    onClick={(e) => {
                                      e.preventDefault();
                                      this.setState({
                                        unsavedChanges: true,
                                        utm_campaign: newsletterTitleTag,
                                      });
                                    }}
                                  >
                                    {newsletterTitleTag}
                                  </a>
                                </div>
                              </BaseControl>
                            </PanelRow>

                            <PanelRow
                              className={
                                (!allowESPinputs || isSaving) &&
                                "nglue-disabled-row"
                              }
                            >
                              <BaseControl
                                label={__("UTM Medium", "newsletter-glue")}
                                id="nglue-utm-medium"
                                className={`nglue-base nglue-esp-input`}
                              >
                                <input
                                  type="text"
                                  id="nglue-utm-medium"
                                  value={utm_medium}
                                  onChange={(e) => {
                                    this.setState({
                                      unsavedChanges: true,
                                      utm_medium: e.target.value,
                                    });
                                  }}
                                />
                                <div className="nglue-input-help">
                                  {__("e.g. email", "newsletter-glue")}
                                </div>
                              </BaseControl>
                              <BaseControl
                                label={__("UTM Content", "newsletter-glue")}
                                id="nglue-utm-content"
                                className={`nglue-base nglue-esp-input`}
                              >
                                <input
                                  type="text"
                                  id="nglue-utm-content"
                                  value={utm_content}
                                  onChange={(e) => {
                                    this.setState({
                                      unsavedChanges: true,
                                      utm_content: e.target.value,
                                    });
                                  }}
                                />
                                <div className="nglue-input-help"></div>
                              </BaseControl>
                            </PanelRow>

                            <PanelRow className="nglue-simple-row">
                              <p>
                                {sprintf(
                                  __(
                                    "Important: If you already have Google Analytics connected to your %s account, you will need to turn it off first to use this feature.",
                                    "newsletter-glue"
                                  ),
                                  thelabel
                                )}
                              </p>
                            </PanelRow>
                          </>
                        )}

                        <PanelRow
                          className={!allowESPinputs && "nglue-disabled-row"}
                        >
                          <Button
                            disabled={!allowESPinputs || !unsavedChanges}
                            onClick={this.saveESP}
                            className={saveBtnClass}
                            icon={hasSaved && iconCheck}
                          >
                            {saveBtnText}
                          </Button>
                        </PanelRow>
                      </>
                    )}
                  </form>
                </div>

                <div className="nglue-soon" style={{ display: "none" }}>
                  {iconAdd}
                  <div
                    dangerouslySetInnerHTML={{
                      __html: __(
                        "Add another email service provider &mdash; <strong>Coming soon</strong>",
                        "newsletter-glue"
                      ),
                    }}
                  ></div>
                </div>
              </div>
            )}
          </PanelBody>
        </div>
      </>
    );
  }
}
