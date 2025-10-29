import React from 'react';

import { createRoot } from 'react-dom/client';

import {
  Component
} from '@wordpress/element';

import GlobalStyles from './../global-styles/index.js';

import { Modal, Button } from '@wordpress/components';

const buttonIcon = <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 211.922 134.177">
  <g id="Group_63" data-name="Group 63" transform="translate(-1082.929 -302.541)">
    <g id="Group_47" data-name="Group 47" transform="translate(1082.929 302.541)">
      <path id="Path_29" data-name="Path 29" d="M77.521,85.8l2.314-1.114a85.37,85.37,0,0,1-9.487-39.991q0-16.93,8.392-28.752C84.335,8.057,88.94,3.013,100.775.672s23.1,1.866,25.3,5.9-8.225,4.43-14.819,13.69S104.438,30.342,100.775,39.8c-2.4,7.682-3.429,10.641-4.414,20.811-.463,10.07.888,16.012,3.18,26.4,2.524,10.139,4.62,14.546,7.734,19.12a45.629,45.629,0,0,1-11.822,5.838,50.072,50.072,0,0,1-13.938,1.605,32.83,32.83,0,0,1-26.855-13.354,55.822,55.822,0,0,1-7.516-14.96q-5.4-15.617-6.568-39.7H38.239q-6.714,30.358-6.714,42.472t4.379,19.12q-8.757,6.568-18.244,6.568T4.087,108.253Q0,102.78,0,91.906c0-7.249,8.542-56.349,9.195-61.664S1.46,14.625,1.46,14.625Q17.806,4.117,30.65,4.117T50.061,8.568a23.391,23.391,0,0,1,9.195,13.063A111.235,111.235,0,0,1,63.124,39.8q1.241,9.56,4.379,22.987C69.594,71.741,73.727,78.6,77.521,85.8Z" transform="translate(0 0)" />
    </g>
    <g id="Group_46" data-name="Group 46" transform="matrix(0.999, -0.035, 0.035, 0.999, 1192.293, 306.755)">
      <path id="Path_29-2" data-name="Path 29" d="M66.846,77.792,59.4,62.467Q75.019,52.1,87.571,52.1q10.509,0,10.508,11.822,0,3.357-4.014,20.506t-4.014,26.2q0,9.049,3.941,13.428a28.5,28.5,0,0,1-17.076,5.984q-8.9,0-12.625-5.838T60.57,107.128a57.236,57.236,0,0,1-16.347,2.335q-19.849,0-32.036-12.26T0,61.008a54.137,54.137,0,0,1,4.379-20.8,69.555,69.555,0,0,1,12.406-19.63A60.369,60.369,0,0,1,37.728,5.765,64.866,64.866,0,0,1,64.437,0q13.792,0,21.6,5.108a16.044,16.044,0,0,1,7.808,14.157q0,9.049-5.911,14.084a20.233,20.233,0,0,1-13.5,5.035A28.164,28.164,0,0,1,60.789,35.1a25.039,25.039,0,0,1-9.706-9.414Q43.2,29.482,37.509,40.574a50.179,50.179,0,0,0-5.692,23.206q0,12.114,4.743,18.171a14.532,14.532,0,0,0,11.9,6.057Q58.818,88.009,66.846,77.792Z" transform="translate(0 0)" />
    </g>
  </g>
</svg>;

export default class TemplateStyles extends Component {

  constructor(props) {

    super(props);

    this.openModal = this.openModal.bind(this);
    this.closeModal = this.closeModal.bind(this);

    this.state = {
      isOpen: false,
    };

  }

  openModal() {
    this.setState({ isOpen: true });
  }

  closeModal() {
    this.setState({ isOpen: false });
  }

  render() {

    return (
      <>
        {this.state.isOpen &&
          <Modal
            title={false}
            isFullScreen={true}
            shouldCloseOnEsc={true}
            shouldCloseOnClickOutside={true}
            isDismissible={true}
            onRequestClose={this.closeModal}
            __experimentalHideHeader={true}
            className="ngl-template-styles-block"
          >
            <GlobalStyles scope="single" toggleModal={this.closeModal} />
          </Modal>}
        {!this.state.isOpen &&
          <Button
            variant="none"
            onClick={this.openModal}
            icon={buttonIcon}
            iconSize={20}
            className="ngl-launch-template-styles"
          >Template style</Button>}
      </>
    );

  }

}

var rootElement = document.getElementById('ngl-template-styles');

if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<TemplateStyles />);
}