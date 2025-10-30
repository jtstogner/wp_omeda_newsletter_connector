# Content from: https://knowledgebase.omeda.com/omedaclientkb/store-behavior-
category

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve and add the Behavior Categories
defined for a given brand.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

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

There are two HTTP methods supported:

  1. POST : for creating a new Behavior Category

  2. PUT : for updating a Behavior Category

## Field Definition

The following tables describe the data elements present on the requests and
responses from the API. In addition to the below elements, a **SubmissionId**
element will also be returned with all responses. This is a unique identifier
for the web services response. It can be used to cross-reference the response
in Omedaâs database.

### Behavior Category Elements

Element Name| Data Type| Description  
---|---|---  
Id| Integer| Only allowed when doing an update. Behavior Category Identifier  
Description| String| Only allowed when doing a create. Description of the
Behavior Category.  
AlternateId| String| An id that can be used to uniquely identify this Behavior
Category (perhaps in your content management system).  
StatusCode| Integer| Only allowed when doing an update. â0â to deactivate,
â1â to activate.  
  
## Create Individual Behavior Category

An HTTP POST **creates** a new Behavior Category for a given brand. See
[W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    

### Request

POST requests, by their nature, will not have an **Id** element, since POST is
reserved for creating new elements, and the service governs the allocation of
ids.

#### Example Request

CODE

    
    
    { 
       "Description":"Construction",
       "AlternateId":"123EXTERNAL"
    }
    

### Response â Success

Upon successful creation of a Behavior Category, an HTTP 200 will be issued.
The response has a **ResponseInfo** element with one sub-element, a
**BehaviorCategoryId** element, which is the Id for the Behavior Category.

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
          "BehaviorCategoryId":8903889
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
          "Error": "A Behavior Category with that name already exists"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    A Behavior Category with that name already exists
    The Description Element is required
    

## Update Individual Behavior Category

An HTTP PUT **updates** a Behavior Category for a given brand. See [W3Câs
PUT specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details. The only elements that will be allowed to be updated is StatusCode
and AlternateId.

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/category/*
    

### Request

PUT requests indicates an update request for this web service. Service will
accept the **Id** , **AlternateId** and **StatusCode** elements that you want
to change update.

#### Example Request

CODE

    
    
    {
      "Id": 3476982,
      "AlternateId": "EXTERNAL_ID_28373",
      "StatusCode": 0
    }
    

### Response â Success

Upon successful update of a Behavior Category, an HTTP 200 will be issued. The
response has a **ResponseInfo** element with one sub-element, a **message**
element, which will simple return **Update successful**.

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
          "message":"Update successful"
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
correct HTTP Method (PUT) for this request.  
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
          "Error": "A Behavior Category Id 1378903 was not found in your database"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    A Behavior Category Id {Behavior Category ID} was not found in your database
    The Behavior Category Id {Behavior Category ID} was already active, no update made
    The Behavior Category Id {Behavior Category ID} was already inactive, no update made

**Table of Contents**

×

