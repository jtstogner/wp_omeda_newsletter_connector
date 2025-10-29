import React from 'react';

import apiFetch from '@wordpress/api-fetch';
import { ToggleControl } from '@wordpress/components';
import { useState } from 'react';
import { createRoot } from 'react-dom/client';

export default function AutomationStatus(props) {

  const [status, setStatus] = useState(props.status);

  function changeAutomationStatus(value) {

    if (!value) {
      setStatus('off');
    } else {
      setStatus('on');
    }

    const data = {
      id: props.id,
      status: !value ? 'off' : 'on'
    };

    apiFetch({
      path: 'newsletterglue/' + nglue_backend.api_version + '/update_automation_status',
      method: 'post',
      data: data,
      headers: {
        'NEWSLETTERGLUE-API-KEY': nglue_backend.api_key,
      }
    }).then(response => {
      if (response.status == 'off') {
        jQuery('.ngl-next-run[data-id=' + props.id + ']').hide();
      } else {
        jQuery('.ngl-next-run[data-id=' + props.id + ']').show();
      }
    });

  }

  return (

    <>
      <ToggleControl
        label={false}
        checked={status === 'on'}
        onChange={(value) => {
          changeAutomationStatus(value);
        }}
      />
    </>

  );

}

document.querySelectorAll('.ngl-automation-status').forEach((item) => {
  let root = createRoot(item);
  root.render(<AutomationStatus id={item.getAttribute('data-id')} status={item.getAttribute('data-status')} />);
});