# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-lookup-
by-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve a single Behavior. You would use
this service to look up a behavior using the **Behavior Id**.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/{behaviorId}/*
     
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/{behaviorId}/*
    

brandAbbreviation is the abbreviation for the brand behaviorId is the known
behavior id

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

## Lookup Behavior By Id

An HTTP GET **retrieves** a single Behavior for a brand, by itâs ID.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Behavior Elements

Element Name| Always Returned| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Behavior Identifier  
ActionId| Yes| Integer| Behavior Action Identifier â all behaviors must
belong to a behavior action, which is predefined in the database. Some
examples of behavior actions are âViewâ, âDownloadâ, âRegisteredâ,
âAttendâ â each of which contains has numeric identifier.  
Description| Yes| String| Description of the Behavior.  
AlternateId| No| String| An id that can be used to uniquely identify this
behavior (perhaps in your content management system).  
OmedaProductId| No| Integer| Links the Behavior to a specific Product defined
in the database.  
  
### Response

When getting a single behavior, the **Behaviors** element is omitted and one
behavior is returned.

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event the Behavior is not found, an HTTP 404 (not found)
response will be returned.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Behavior":[
        {
          "Id":41237, 
          "TypeId":11, 
          "Description":"Trade Show 2010 - No Show",
          "AlternateId":"TRADE_SHOW_2010_NO_SHOW"
        }
      ]
    }

**Table of Contents**

×

