import React from 'react';

import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { useState } from 'react';

import {
  render
} from '@wordpress/element';

import {
  BaseControl,
  Button,
  ButtonGroup,
  CheckboxControl,
  Flex,
  FlexBlock,
  FlexItem,
  __experimentalHeading as Heading,
  Notice,
  PanelBody,
  __experimentalSpacer as Spacer,
  __experimentalText as Text,
  TextareaControl,
  TextControl,
} from '@wordpress/components';

import { tabdata } from './support-tabdata.js';
import { tabs } from './support-tabs.js';

import { IoClose } from "react-icons/io5";

export default function Support() {

  const { support } = nglue_backend;

  const [tab, setTab] = useState('');
  const [issueText, setIssueText] = useState('');
  const [issueURL, setIssueURL] = useState('');
  const [isPlugins, setPlugins] = useState(true);
  const [isESP, setESP] = useState(true);
  const [isDiagnose, setDiagnose] = useState(true);
  const [name, setName] = useState(support.admin_name);
  const [email, setEmail] = useState(support.admin_email);
  const [error, setError] = useState('');
  const [accerror, setAccerror] = useState('');
  const [isProcessing, setProcessing] = useState(false);
  const [isDisabled, setDisabled] = useState(false);
  const [isCreating, setCreating] = useState(false);
  const [createBtn, setCreateBtn] = useState('Create admin account');
  const [isDone, setDone] = useState(false);
  const [files, setFiles] = useState([]);

  function sendSupportRequest() {

    setProcessing(true);
    setError('');

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/support_request',
      method: 'post',
      data: {
        name: name,
        email: email,
        create_admin: true,
        manage_plugins: isPlugins,
        manage_esp: isESP,
        send_diagnose: isDiagnose,
        issue_url: issueURL,
        issue: issueText,
        type: tab,
        files: files,
      },
    }).then(response => {
      if (response.error) {
        setError(response.error);
      }
      if (response.success) {
        setDone(true);
        setFiles([]);
      }
      setProcessing(false);
    });

  }

  function createAdminAccount() {

    setCreating(true);
    setCreateBtn('Creating admin account...');
    setAccerror('');

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/create_support_admin',
      method: 'post',
      data: {
        manage_plugins: isPlugins,
        manage_esp: isESP,
        send_diagnose: isDiagnose,
      },
    }).then(response => {
      if (response.error) {
        setAccerror(response.error);
      }

      setCreating(false);
      setDisabled(true);
      setCreateBtn('Admin account created!');
      setTimeout(() => {
        setCreateBtn('Create admin account');
        setDisabled(false);
      }, 3000);
    });

  }

  function unattachFile(index) {
    setFiles(files.filter((x, i) => i !== index));
  }

  let frame
  const runUploader = (event) => {
    event.preventDefault()

    // If the media frame already exists, reopen it.
    if (frame) {
      frame.open()
      return
    }

    // Create a new media frame
    frame = wp.media({
      title: __('Add image/video', 'newsletter-glue'),
      button: {
        text: __('Add image/video', 'newsletter-glue'),
      },
      multiple: true,
    })

    frame.on('select', function () {
      var uploads = frame.state().get('selection').toJSON();
      setFiles(uploads);
    });

    // Finally, open the modal on click
    frame.open();
  }

  return (

    <>
      {isDone &&
        <>
          <Spacer paddingBottom={20} marginBottom={0} />
          <div className="nglue-main nglue-form">
            <Heading level={1}>{__('Thank you!', 'newsletter-glue')}</Heading>
            <Spacer paddingBottom={8} marginBottom={0} />
            <Text size={16} isBlock>{__('We received your support request. Please expect a reply in the next 24-48 hours.', 'newsletter-glue')}</Text>
            <Spacer paddingBottom={8} marginBottom={0} />
            <Flex gap={6}>
              <FlexItem>
                <Button
                  variant="primary"
                  className="ngl-big-button"
                  onClick={() => {
                    setDone(false);
                    setError('');
                    setIssueText('');
                    setIssueURL('');
                    setTab('');
                  }}
                >
                  {__('Submit a new ticket', 'newsletter-glue')}
                </Button>
              </FlexItem>
              <FlexBlock>
              </FlexBlock>
            </Flex>
          </div>
        </>
      }

      {!isDone &&
        <>
          <Spacer paddingBottom={20} marginBottom={0} />
          <div className="nglue-main nglue-form">
            <Heading level={1}>{__('Request support', 'newsletter-glue')}</Heading>

            <Spacer paddingBottom={8} marginBottom={0} />

            <Text size={16} isBlock>{__('I need help with:', 'newsletter-glue')}</Text>

            <Spacer paddingBottom={4} marginBottom={0} />

            <ButtonGroup className="ngl-btn-group">
              {
                tabs.map(function (item, i) {
                  return <Button
                    key={`button-${i}`}
                    variant={item.name === tab ? 'primary' : 'secondary'}
                    icon={item.icon ? item.icon : null}
                    onClick={(e) => {
                      e.preventDefault();
                      setTab(item.name);
                    }}
                  >
                    {item.label}
                  </Button>;
                })
              }
            </ButtonGroup>

            {tab && tabdata[tab] && tabdata[tab].questions &&
              <>
                <PanelBody className="ngl-content">
                  <Heading level={2}>Browse common questions on {tabdata[tab].smallcase}</Heading>
                  <Spacer paddingBottom={2} marginBottom={0} />
                  <ul>
                    {
                      tabdata[tab].questions.map(function (question, i) {
                        return <li key={`question-${i}`}><a href={question.url} target="_blank" rel="noreferrer">{question.name}</a></li>
                      })
                    }
                  </ul>
                </PanelBody>
              </>
            }

            {tab &&
              <>
                <PanelBody className="ngl-content">

                  <Heading level={3}>Submit support ticket</Heading>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <TextareaControl
                    label={__('Describe your issue*', 'newsletter-glue')}
                    value={issueText}
                    rows={6}
                    onChange={(value) => setIssueText(value)}
                    disabled={isProcessing}
                  />

                  {tab != 'sending' && <TextControl
                    label={__('Where is the problem occuring? Share a specific URL.', 'newsletter-glue')}
                    value={issueURL}
                    onChange={(value) => setIssueURL(value)}
                    disabled={isProcessing}
                  />}

                  <BaseControl label="Share screenshots or screen recordings of your problem." __nextHasNoMarginBottom>
                  </BaseControl>
                  <Button
                    variant="secondary"
                    disabled={false}
                    onClick={runUploader}
                  >Add image/video</Button>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  {files &&
                    Array.from(files).map(function (item, i) {
                      return <div key={`file-${i}`} className="ngl-file-uploaded">
                        <a href={item.url} target="_blank" rel="noreferrer">{item.title}</a>
                        <a href="" className="ngl-file-unattach" onClick={(e) => {
                          e.preventDefault();
                          unattachFile(i);
                        }}><IoClose /></a>
                      </div>;
                    })
                  }

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <Flex gap={2} align="flex-start">
                    <FlexBlock>
                      <TextControl
                        label={__('Your name', 'newsletter-glue')}
                        value={name}
                        onChange={(value) => setName(value)}
                        disabled={isProcessing}
                      />
                    </FlexBlock>
                    <FlexBlock>
                      <TextControl
                        label={__('Your email address', 'newsletter-glue')}
                        value={email}
                        onChange={(value) => setEmail(value)}
                        help={__('We will email this address to follow up on this support ticket.', 'newsletter-glue')}
                        disabled={isProcessing}
                      />
                    </FlexBlock>
                  </Flex>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <Flex gap={6}>
                    <FlexItem>
                      <Button
                        variant="primary"
                        className="ngl-big-button"
                        onClick={sendSupportRequest}
                        isBusy={isProcessing}
                        disabled={isProcessing || !email || !issueText}
                      >
                        {!isProcessing && __('Submit support ticket', 'newsletter-glue')}
                        {isProcessing && __('Processing your request...', 'newsletter-glue')}
                      </Button>
                    </FlexItem>
                    <FlexBlock>
                      {error && <Notice status="error" isDismissible={false} className="ngl-notice-err">{error}</Notice>}
                    </FlexBlock>
                  </Flex>

                </PanelBody>

                <PanelBody className="ngl-content">
                  <Heading level={3}>{__('Admin login', 'newsletter-glue')}</Heading>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <Text isBlock lineHeight={1.8} size={14}>Automatically create an admin account for Newsletter Glue when you click <strong>Create admin account</strong>.</Text>
                  <Text isBlock lineHeight={1.8} size={14}>This account will be deleted once the support ticket is closed.</Text>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <CheckboxControl
                    label={__('Allow Newsletter Glue to temporarily install, activate, and deactivate plugins to troubleshoot.', 'newsletter-glue')}
                    checked={isPlugins}
                    onChange={setPlugins}
                    disabled={isProcessing}
                  />

                  <CheckboxControl
                    label={__('Allow Newsletter Glue to temporarily replace my email service provider API key with their own to troubleshoot.', 'newsletter-glue')}
                    checked={isESP}
                    onChange={setESP}
                    disabled={isProcessing}
                  />

                  <CheckboxControl
                    label={__('Send basic diagnostic information to Newsletter Glue.', 'newsletter-glue')}
                    checked={isDiagnose}
                    onChange={setDiagnose}
                    disabled={isProcessing}
                  />

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <Flex gap={6}>
                    <FlexItem>
                      <Button
                        variant="primary"
                        className="ngl-big-button"
                        onClick={createAdminAccount}
                        isBusy={isCreating}
                        disabled={isDisabled || isCreating}
                      >
                        {createBtn}
                      </Button>
                    </FlexItem>
                    <FlexBlock>
                      {accerror && <Notice status="accerror" isDismissible={false} className="ngl-notice-err">{accerror}</Notice>}
                    </FlexBlock>
                  </Flex>

                  <Spacer marginBottom={0} paddingBottom={4} />

                  <Text isBlock lineHeight={1.8} size={14} variant="muted">Delete this admin account at anytime by heading to <a href={nglue_backend.users_url}>Users</a>.</Text>

                </PanelBody>
              </>
            }

          </div>
        </>
      }
    </>

  );

}

var rootElement = document.getElementById('nglue-support');

if (rootElement) {
  render(<Support />, rootElement);
}