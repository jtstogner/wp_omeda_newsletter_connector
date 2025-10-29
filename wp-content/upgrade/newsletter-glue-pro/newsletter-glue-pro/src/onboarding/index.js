import React from 'react';

import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { createRoot } from 'react-dom/client';

import {
  Component,
  Fragment
} from '@wordpress/element';

import ESPConnection from './esp';
import OnboadingLicense from './license';
import ESPSetup from './setup';

import Loader from '../common/loader';

import TemplateWizard4 from '../template-wizard/finish';
import TemplateWizard2 from '../template-wizard/logo';
import TemplateWizard1 from '../template-wizard/personalise';
import { settings } from '../template-wizard/settings';
import TemplateWizard3 from '../template-wizard/social-links';

const stepTitles = {
  1: "License",
  2: "Email provider",
  3: "Newsletter defaults",
  4: "Personalisation",
  5: "Brand",
  6: "Social accounts",
};

export default class Onboarding extends Component {

  constructor(props) {

    super(props);

    this.changeState = this.changeState.bind(this);
    this.changeOptions = this.changeOptions.bind(this);

    const data = {
      isReady: false,
      step: 1,
      completedStep: 0,
      successRedirect: nglue_backend.demo_post,
      isSaving: false,
      showPatternWelcome: false,
      isOnboarding: true,
    };

    settings.map(function (item) {
      data[item] = '';
    });

    this.state = data;
  }

  componentDidMount() {

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/pattern_settings',
      method: 'get',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {

      const data = [];

      data.isReady = true;

      settings.map(function (item) {
        data[item] = response[item];
      });

      this.setState(data);

    });

  }

  changeState(newstate) {
    this.setState(newstate);
  }

  changeOptions() {

    const data = {};

    settings.map((item) => {
      data[item] = this.state[item];
    });

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/pattern_settings',
      method: 'post',
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      },
      data: data
    }).then(() => {

    });

  }

  navigatetoStep(newstep) {

    const { completedStep } = this.state;

    if (newstep > completedStep && (newstep - completedStep) > 1) {
      return;
    }

    this.setState({
      step: newstep
    });

  }

  render() {

    if (!this.state.isReady) {
      return <Loader />;
    }

    var Step = this.state.step;
    var completedStep = this.state.completedStep;

    const stepsCount = 6;

    var steps = [1, 2, 3, 4, 5, 6];
    let stepsList = steps.map((item, i) => {

      var id = i + 1;
      var stepclass = undefined;

      if (completedStep >= id) {
        stepclass = 'active';
      } else if (Step == id) {
        stepclass = 'progress';
      }

      if (completedStep >= 1) {
        if (i == completedStep) {
          stepclass = 'progress';
        }
      }

      return <li
        key={item}
        className={stepclass}
        data-step={item}
        onClick={this.navigatetoStep.bind(this, item)}
      >{stepTitles[item]}</li>;
    });

    return (
      <Fragment>
        <div className="ngl-wizard-steps">
          <ul className={`ngl-steps ngl-steps-${stepsCount}`}>
            {stepsList}
          </ul>
          <div style={{ clear: 'both' }}></div>
        </div>
        <div className="ngl-wizard">
          {Step == 1 && <OnboadingLicense changeState={this.changeState} />}
          {Step == 2 && <ESPConnection changeState={this.changeState} />}
          {Step == 3 && <ESPSetup changeState={this.changeState} />}
          {Step == 4 && <TemplateWizard1 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 5 && <TemplateWizard2 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 6 && <TemplateWizard3 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
          {Step == 7 && <TemplateWizard4 {...this} changeState={this.changeState} changeOptions={this.changeOptions} />}
        </div>
        <div className="ngl-wizard-links">
          {!this.state.isSaving && <a href={nglue_backend.skip_onboarding}>{__('Skip onboarding', 'newsletter-glue')}</a>}
        </div>
      </Fragment>
    );

  }

}

var rootElement = document.getElementById('nglue-welcome-wizard');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<Onboarding />);
}