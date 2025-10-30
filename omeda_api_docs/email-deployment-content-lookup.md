# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-content-lookup

[**Knowledge Base Home**](../omedaclientkb/)

### Summary

An api available to our Email Builder clients.

For a given set of url parameters â the API will return the text or html
content for a given Email Builder deployment and specified split.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/lookup/{textOrHtml}/{trackingNumber}/{splitSequence}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/lookup/{textOrHtml}/{trackingNumber}/{splitSequence}/*
    
    

brandAbbreviation is the abbreviation for the brand textOrHtml valid values
are âtextâ or âhtmlâ and determine whether the API returns the text
version of the deployment content or the html version of the deployment
content. trackingNumber is the tracking number for the omail deployment.
splitSequence is the split sequence of the omail deployment. If the deployment
has multiple splits, you would pass â1â to see split 1âs content, etc.

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **text/html**.

### Supported HTTP Methods

There is one HTTP method supported: GET See [W3Câs GET
specs](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

##### Example URL to retrieve the html content from split 1 of omail
deployment ACME190211008:

<https://ows.omedastaging.com/webservices/rest/brand/ACME/omail/deployment/content/lookup/html/ACME190211008/1/*>

##### Example URL to retrieve the text content from split 2 of omail
deployment ACME141001003:

<https://ows.omedastaging.com/webservices/rest/brand/ACME/omail/deployment/content/lookup/text/ACME141001003/2/*>

#### Success Response will return the content corresponding to the search
values in the lookup URL.

**Table of Contents**

×

