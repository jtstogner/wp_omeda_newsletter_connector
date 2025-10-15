# Content from: https://knowledgebase.omeda.com/omedaclientkb/olytics-
customer-top-values

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

An api available to our [olytics](../omedaclientkb/cdp-olytics-overview)
clients.

For a given customer, this service returns the top 3 olytics tag values for
each field you are using as part of your olytics setup.

This api can be used to deliver more targeted content or advertisements to the
visitors on your site.

By default all available values will be considered by the API, but we offer
the ability for you to flag olytics values that you do not want to include in
this API. This can be the case for generic pages you have tagged, such as a
home page or landing page that does not provide added value to your
customerâs olytics profile. Please contact your omeda representative if you
wish to exclude certain tags from calculation.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerIdOrCustomerEncrypted}/topolytics/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerIdOrCustomerEncrypted}/topolytics/*
    

Specifying a specific behavior id to narrow the results resturned:

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerIdOrCustomerEncrypted}/topolytics/behavior/{behaviorIdOrEncryptedBehaviorId}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerIdOrCustomerEncrypted}/topolytics/behavior/{behaviorIdOrEncryptedBehaviorId}/*
    

brandAbbreviation is the abbreviation for the brand
customerIdOrCustomerEncrypted is the internal omeda customer id or the
encrypted omeda customer id (in olytics this is the ajs_uid)
behaviorIdOrEncryptedBehaviorId is the internal omeda behavior id or the
encrypted omeda behavior id (the encrypted omeda behavior id is used in your
olytics javascript tags)

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/olytics-customer-top-values) for
more details. If omitted, the default content type is application/json.

### Content Type

The content type is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported: GET See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Lookup Top Olytics Tag Values By Customer Id

Retrieves the top 3 olytics tag values for each field used on your olytics
installation for a given customer.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### TopOlyticsAttributes Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
OlyticsField| no| string| the name of the field, example: âolytics
Categoryâ or âolytics Tagâ.  
TopValues| no| array| an array containing the top 3 values for the given
field. Contact omeda if there are certain values (tags or categories) you
would like to exclude from calculation, such as home pages or landing pages.  
  
##### TopValues Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
OlyticsValue| no| string| The open text value supplied in your javascript
tags, stored on our system. Examples: âHealthCare Productsâ, âWidgets
Recalled Article â Januaryâ, âSubscription Pageâ.  
LastVisitDate| no| date| The most recent visit the customer made for this
olytics value.  
VisitCount| no| integer| The number of visits the customer made for this
olytics value.  
  
#### Success Example

CODE

    
    
    {
    	"TopOlyticsAttributes": [{
    		"OlyticsField": "olytics category",
    		"TopValues": [{
    			"OlyticsValue": "Whitepapers",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 30
    		}, {
    			"OlyticsValue": "Magazine",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 21
    		}, {
    			"OlyticsValue": "News",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 12
    		}]
    	}, {
    		"OlyticsField": "olytics tag",
    		"TopValues": [{
    			"OlyticsValue": "2014-11 Article Home",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 8
    		}, {
    			"OlyticsValue": "Wormly Widgets Announces Recall",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 7
    		}, {
    			"OlyticsValue": "California Attorney General Announces Lawsuit Against Consolidated Widgets Inc.",
    			"LastVisitDate": "2015-02-23 12:15:29.857",
    			"VisitCount": 6
    		}]
    	}],
    	"SubmissionId": "79AC54FA-5D1D-4FA4-815A-08074C7F5EC8"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No customer found for customer 12345."
          }
       ]
    }

**Table of Contents**

×

