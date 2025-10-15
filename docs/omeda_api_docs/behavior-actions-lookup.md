# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-
actions-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve Behavior âActionsâ defined for
a given brand.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/action/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/action/*
    

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

## Lookup All Behavior Actions

Retrieves all defined Behavior Actions for the brand.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Behavior Action Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Behavior Action Identifier  
Description| Yes| String| Description of the Behavior Action.  
StatusCode| Yes| Integer| 1 = Active, 0 = Inactive  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Behavior Actions are found, an HTTP 404 (not
found) response will be returned.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "BehaviorAction":[
        {
          "Id":3,
          "Description":"Attended",
          "StatusCode": 1
        },
        {
          "Id":4,
          "Description":"Exhibited",
          "StatusCode": 1
        },
        {
          "Id":8,
          "Description":"Onsite",
          "StatusCode": 1
        },
        {
          "Id":10,
          "Description":"Pre-Registered",
          "StatusCode": 1
        },
        {
          "Id":11,
          "Description":"No Show",
          "StatusCode": 1
        },
        {
          "Id":12,
          "Description":"Attended Dialup Modem Conference",
          "StatusCode": 0
        }
      ]
    }

**Table of Contents**

×

