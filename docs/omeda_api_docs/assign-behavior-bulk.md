# Content from: https://knowledgebase.omeda.com/omedaclientkb/assign-behavior-
bulk

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to create many Behaviors defined for a given
brand to multiple customers. For example, TODO.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/assign-behavior-bulk) for more details. If
omitted, the default content type is application/json.x-omeda-inputida unique
id with which to process your request.Contact your Omeda Customer Services
Representative to obtain an inputid.

### Content Type

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

  1. POST : for creating a new Behavior for many customers

## Field Definition

The following tables describe the data elements present on the requests and
responses from the API. In addition to the below elements, a **SubmissionId**
element will also be returned with all responses. This is a unique identifier
for the web services response. It can be used to cross-reference the response
in Omedaâs database.

#### Customer Elements

Attribute Name| Required?| Description  
---|---|---  
OmedaCustomerId| Required| ID of customer in Omeda database  
CustomerBehaviors| Required| JSON element containing multiple
**CustomerBehaviors** elements (see below)  
  
#### Customer Behavior Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorId| Required| Omeda Event ID (Integer value)  
BehaviorDate| Required| Date the behavior occurred (yyyy-MM-dd HH:mm:ss )  
PromoCode (deprecated)| optional| Promocode  
BehaviorPromoCode| optional| Promocode  
BehaviorAttributes| optional| JSON element containing multiple
BehaviorAttributes Elements elements (see below)  
  
#### Customer Behavior Attributes Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorAttributeTypeId| Required| Omeda Behavior Attribute Type ID (Integer
value)  
BehaviorAttributeValue| Required| attribute value  
  
## Create Behaviors

An HTTP POST **creates** a new Behavior for a given customer. See [W3Câs
POST specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/assignbehavior/bulk/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/assignbehavior/bulk/*
    

### Request

POST requests, by their nature, will not have an **Id** element, since POST is
reserved for creating new elements, and the service governs the allocation of
ids. There is no need to send in a **StatusCode** in the request since the
service assumes the Behavior you are creating will be active.

Please note that if any errors exist for any of the Customers, no entries will
be saved for the request. The entire request must be valid for any of the
entries to save.

#### Example Request

CODE

    
    
    {
      "Customers":[
        {
          "OmedaCustomerId":220002,
          "CustomerBehaviors" : [
            {
              "BehaviorId":"104",
              "BehaviorDate":"2012-05-10 15:12:12",
              "BehaviorPromoCode":"123"
            },
            {
              "BehaviorId":"105",
              "BehaviorDate":"2012-05-10 15:32:12",
              "BehaviorPromoCode":"456"
            }
          ]
        },
        {
          "OmedaCustomerId":220005,
          "CustomerBehaviors" : [
            {
              "BehaviorId":"104",
              "BehaviorDate":"2012-05-10 15:12:12",
              "BehaviorPromoCode":"123"
            },
            {
              "BehaviorId":"105",
              "BehaviorDate":"2012-05-10 15:32:12",
              "BehaviorPromoCode":"456"
              "BehaviorAttributes": [
                {
                "BehaviorAttributeTypeId":222,
                "BehaviorAttributeValue":"Article Name Something"
                },
                {
                "BehaviorAttributeTypeId":3,
                "BehaviorAttributeValue":60
                }  
              ]
            }
          ]
        }
      ]
    }
    

### Response â Success

Upon successful creation of a Behavior, an HTTP 200 will be issued. The
response has a **ResponseInfo** element with one sub-element, a **Id**
element, which is the Id for the Behavior.

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
          "OmedaCustomerId":220002,
          "Message": "2 Behaviors Submitted."
        },
        {
          "OmedaCustomerId":220005,
          "Message": "2 Behaviors Submitted"
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
          "Error": "OmedaCustomerId 274829 does not exist"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    OmedaCustomerId {OmedaCustomerId} does not exist.  No Customer Behaviors have been saved.
    OmedaCustomerId {OmedaCustomerId} exists but is not active.  No Customer Behaviors have been saved.
    BehaviorId {BehaviorId} passed in for OmedaCustomerId {OmedaCustomerId}, does not exist.  No Customer Behaviors have been saved.
    BehaviorId {BehaviorId} passed in for OmedaCustomerId {OmedaCustomerId}, exists but is not active.  No Customer Behaviors have been saved.
    BehaviorDate passed in for OmedaCustomerId {OmedaCustomerId} and BehaviorId {BehaviorId} has an invalid format.  No Customer Behaviors have been saved.

**Table of Contents**

×

