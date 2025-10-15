# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-
categories-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the Behavior Categories defined for
a given brand.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    

brandAbbreviation is the abbreviation for the brand

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup All Behavior Categories

Retrieves all defined Behavior Categories for the brand.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Behavior Category Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Behavior Category Identifier  
Description| Yes| String| Description of the Behavior Category.  
AlternateId| No| String| An id that can be used to uniquely identify this
Behavior Category (perhaps in your content management system).  
BehaviorId| No| Integer Array| List of BehaviorIds that are attached to this
Behavior Category.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Behavior Categories are found, an HTTP 404 (not
found) response will be returned.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "BehaviorCategory":[
        {
          "Id":22,
          "Description":"Emerging Tech",
          "AlternateId":"EMERG_TECH",
          "BehaviorId": [1848792,332839,328723]
        },
        {
          "Id":23,
          "Description":"Policy and Industry",
          "AlternateId":"POLICY_AND_INDUSTRY",
          "BehaviorId": [8763467,7383749,6174038,7263930,1749393,2848202,28347502]
        },
        {
          "Id":24,
          "Description":"Subscription",
          "AlternateId":"SUBSCRIPTION"
        },
        {
          "Id":26,
          "Description":"Reviews",
          "AlternateId":"REVIEWS",
          "BehaviorId": [8763467,7634798]
        }
      ]
    } 

**Table of Contents**

×

