# Content from: https://knowledgebase.omeda.com/omedaclientkb/store-behavior

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to add and update Behaviors defined for a given
brand. For example, you might use it to keep your marketing database at Omeda
in sync with your content taxonomy category. Doing this allows you to store
individual [customer behavior directly in your marketing
database](../omedaclientkb/behavior-lookup-by-customer-id) once the behavior
âvalid valuesâ are defined.

## General Technical Requirements

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/store-behavior) for more details. If omitted,
the default content type is application/json.

### Content Type

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

  1. POST : For creating or a new Behavior.

  2. PUT : For updating an existing Behavior.

## Field Definition

The following tables describe the data elements that can be sent to this API.
In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

### Behavior Elements

Element Name| Required onâ¦| | **Data Type**|  Description  
---|---|---|---|---  
| **POST req.**| **PUT req.**| |   
Id| Not allowed| Yes| Integer| Behavior Identifier  
ActionId| Yes| Not allowed| Integer| Behavior Action Identifier â all
behaviors must belong to a behavior action, which is predefined in the
database. Some examples of behavior actions are âViewâ, âDownloadâ,
âRegisteredâ, âAttendâ â each of which contains a numeric
identifier. You are not allowed to update an ActionId in an existing Behavior.  
Description| Yes| Not allowed| String| Description of the Behavior.  
AlternateId| No| No| String| An id that can be used to uniquely identify this
behavior (perhaps in your content management system).  
ProductId| No| Not allowed| Integer| Links the Behavior to a specific Product
defined in the database.  
StatusCode| Not allowed| No| Integer| Only allowed when doing an update.
â0â to deactivate, â1â to activate.  
  
## Create Individual Behavior

An HTTP POST **creates** a new Behavior for a given brand. See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    

### Request

POST requests, by their nature, will not have an **Id** element, since POST is
reserved for creating new elements, and the service governs the allocation of
ids.

#### Example Request

CODE

    
    
    { 
       "ActionId":4, 
       "Description":"Trade Show 2010 - Exhibited",
       "AlternateId":"TRADE_SHOW_2010_EXHIBITED",
       "ProductId":3489093
    }
    

### Response â Success

Upon successful creation of a behavior, an HTTP 200 will be issued. The
response has a **ResponseInfo** element with two sub-elements, a
**BehaviorId** element, the Id for the Behavior, and a **Url** element, the
URL that allows you do a lookup on the Behavior.

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "ResponseInfo":[
        {
          "BehaviorId":8907512,
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/8907512/*"
        }
      ]
    }
    

### Response â Failure

If an error occurs repeatedly, please contact your Omeda representative.

#### HTTP Response Codes

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact Omeda Account Representative.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          "Error": "A Behavior with that name already exists"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    A Behavior with that name already exists
    The BehaviorAction {ActionId} does not exist.
    The BehaviorAction {ActionId} is inactive, no updates were made.
    ProductId entered is not a valid
    ProductId entered is not valid for Brand {Brand Abbreviation}
    The StatusCode Element cannot be used for a Create request.
    

## Update Individual Behavior

An HTTP PUT **updates** an existing Behavior for a given brand. See [W3Câs
PUT specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6) for
details.

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/*
    

### Request

A PUT request must have an **Id** element, since PUT is reserved for updating
existing Behaviors.

#### Example Request

CODE

    
    
    { 
       "Id":41238, 
       "AlternateId":"TRADE_SHOW_2010_EXHIBITED_SEMINAR",
    }
    

### Response â Success

Upon successful update of a behavior, an HTTP 200 will be issued. The response
has a **ResponseInfo** element with two sub-elements, a **Message** element,
indicating success, and a **Url** element, the URL that allows you do a lookup
on the Behavior just updated.

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "ResponseInfo":[
        {
          "Message":"Behavior was updated successfully",
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/8907512/*"
        }
      ]
    }
    

### Response â Failure

If an error occurs repeatedly, please contact your Omeda representative.

#### HTTP Response Codes

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact Omeda Account Representative.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          "Error": "The Id you submitted does not exist in the database"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    BehaviorId {Id} was not found in your database
    The ActionId Element cannot be used for an update request.
    The ProductId Element cannot be used for an update request.
    The Description Element cannot be used for an update request.
    Either AlternateId or StatusCode must be present on the request.
    The Status must either 0 or 1.

**Table of Contents**

×

