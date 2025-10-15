# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the Behaviors defined for a given
brand. For example, you might use it to keep your marketing database at Omeda
in sync with your content taxonomy category.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    

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

## Lookup All Behaviors

Retrieves all defined Behaviors for the brand.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Behavior Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Behavior Identifier  
ActionId| Yes| Integer| Behavior Action Identifier â all behaviors must
belong to a behavior action, which is predefined in the database. Some
examples of behavior actions are âViewâ, âDownloadâ, âRegisteredâ,
âAttendâ â each of which contains has numeric identifier.  
Description| Yes| String| Description of the Behavior.  
Category| No| Integer Array| List of CategoryIds that are attached to this
Behavior.  
AlternateId| No| String| An id that can be used to uniquely identify this
behavior (perhaps in your content management system).  
ProductId| No| Integer| Links the Behavior to a specific Product defined in
the database.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Behaviors are found, an HTTP 404 (not found)
response will be returned.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Behavior":[
        {
          "Id":41234, 
          "ActionId":3, 
          "Description":"Trade Show 2010 - Attended",
          "AlternateId":"TRADE_SHOW_2010_ATTENDED",
          "ProductId":76
        },       
        { 
          "Id":41235, 
          "ActionId":8, 
          "Description":"Trade Show 2010 - Onsite",
          "AlternateId":"TRADE_SHOW_2010_ONSITE",
          "ProductId":76
        },
        {
          "Id":41236, 
          "ActionId":10, 
          "Description":"Trade Show 2010 - Pre-Registered",
          "AlternateId":"TRADE_SHOW_2010_PRE_REGISTERED",
          "ProductId":76,
          "Category":[2,3,4]
        },
        { 
          "Id":41237, 
          "ActionId":11, 
          "Description":"Trade Show 2010 - No Show",
          "AlternateId":"TRADE_SHOW_2010_NO_SHOW",
          "ProductId":76,
          "Category":[2,3,4]
        }
      ]
    } 

**Table of Contents**

×

