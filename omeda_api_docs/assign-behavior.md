# Content from: https://knowledgebase.omeda.com/omedaclientkb/assign-behavior

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Assign Behavior API provides the ability to add or update behavior
information for an existing customer. Note that this service deposits data
into a queue, it does not expressly process data directly. The remaining back
end processing of the data (insertion into the marketing database) happens
through a decoupled processing layer and depends on your own individual
database configuration

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/assignbehavior/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/assignbehavior/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/assign-behavior) for more details. If omitted,
the default content type is application/json.x-omeda-inputida unique id with
which to process your request.Contact your Omeda Customer Services
Representative to obtain an inputid.

## Supported Content Types

JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store data in the database.

### Customer Elements

Attribute Name| Required?| Description  
---|---|---  
OmedaCustomerId| Required| ID of customer in Omeda database  
CustomerBehaviors| Required| JSON element containing multiple
**CustomerBehaviors** elements (see below)  
  
### Customer Behavior Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorId| Required| Omeda BehaviorID (Integer value)  
BehaviorDate| Required| Date the behavior occurred (yyyy-MM-dd HH:mm:ss )  
PromoCode (deprecated)| optional| Promocode  
BehaviorPromoCode| optional| Promocode  
BehaviorAttributes| optional| JSON element containing multiple
BehaviorAttributes Elements elements (see below)  
  
### Customer Behavior Attributes Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorAttributeTypeId| Required| Omeda Behavior Attribute Type ID (Integer
value)  
BehaviorAttributeValue| Conditional| attribute value â is required for all
BehaviorAttributes unless it is a type 1 and BehaviorAttributeValueId is
present on the request  
BehaviorAttributeValueId| Conditional| attribute value Id â can only be
present for Type 1 BehaviorAttributes and can only be present if
BehaviorAttributeValue is not present  
  
## Request Examples

### JSON Example

CODE

    
    
    {
    "OmedaCustomerId":220002,
    "CustomerBehaviors" :                 
      [
        {
          "BehaviorId":"104",
          "BehaviorDate":"2011-07-20 12:12:12",
          "BehaviorPromoCode":"123"
        },
        {
          "BehaviorId":"105",
          "BehaviorDate":"2011-07-20 12:12:12",
          "BehaviorPromoCode":"456",
          "BehaviorAttributes": [
             {
              "BehaviorAttributeTypeId":222,
              "BehaviorAttributeValue":"Article Name Something"
              },
              {
              "BehaviorAttributeTypeId":3,
              "BehaviorAttributeValueId":60
              }  
          ]
        }
      ]
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

The response has a **ResponseInfo** element with two sub-elements, a
CustomerId element, the Id for the customer, and a Url element, the URL that
allows you to retrieve customer information.

#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "CustomerId":220002,
          "Url":"http://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/220002/*"
        }
      ]
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

Potential errors:

CODE

    
    
    OmedaCustomerId {OmedaCustomerId } not found.
    Please Provide valid OmedaCustomerId.
    No behavior Information in request
    BehaviorId {BehaviorId} is not found.
    Invalid date format for BehaviorId {BehaviorId}
    Please provide BehaviorDate for BehaviorId {BehaviorId}
    

A failed POST submission error codes:

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
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          "Error": "BehaviorId {BehaviorId} is not found"
        }
      ]
    }
    

In the rare case that there is a server-side problem, an HTTP 500 (server
error) will be returned. This generally indicates a problem of a more serious
nature, and submitting additional requests may not be advisable.Please contact
Omeda Account Representative.

**Table of Contents**

×

