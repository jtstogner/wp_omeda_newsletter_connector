# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-lookup-
by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The behavior lookup API call returns behavior information for a specified
customer. Behavior information can be requested for a specific behavior OR for
behaviors associated with a specific product OR all behaviors

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Request URI

To look up all behaviors for a specific customer:

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/*
    

To look up a specific behavior for a specific customer:

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/*
    

To look up all behaviors for a specific product for a specific customer:

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/product/{productId}/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/product/{productId}/*
    

brandAbbreviationis the abbreviation of the brand.customerIdis the Omeda
Customer Id. (encrypted customer id may also be used)behaviorIdis the Omeda
Behavior Id.productIdis the Omeda Product Id.

### HTTP Headers

The HTTP header must contain the following elements:x-omeda-appida unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Content
Types](../omedaclientkb/behavior-lookup-by-customer-id) for more details. If
omitted, the default content type is application/json.

### Content Type

The content type is **application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup Behavior By Customer Id

Retrieves a record containing all available name, contact, and demographic
information about the customer.

### Field Definition

The following tables describe the data elements present on the responses from
the API.

#### Behaviors Elements

Element Name| Data Type| Description  
---|---|---  
BehaviorId| Integer| Identifies the behavior being returned for the customer  
FirstOccurenceDate| Datetime (format: yyyy-mm-dd hh:mm:ss)| First time the
behavior occurred.  
LastOccurenceDate| Datetime (format: yyyy-mm-dd hh:mm:ss)| Most recent time
the behavior occurred  
NumberOfOccurrences| Integer| Total number of behavioral occurrences  
PromoCode| String| Returns the most recent promo code (if any) associated with
the behavior. Always optional.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
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
  
#### Success

CODE

    
    
    {
       "Customer":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/54353453254/*",
       "Behaviors":[
          {
             "LastOccurrenceDate":"2011-02-08 11:50:46.657",
             "BehaviorId":534543543,
             "FirstOccurrenceDate":"2011-02-08 11:50:46.657",
             "NumberOfOccurrences":1
          },
          {
             "LastOccurrenceDate":"2011-02-08 11:50:46.657",
             "BehaviorId":65465634234,
             "FirstOccurrenceDate":"2011-02-08 11:50:46.657",
             "NumberOfOccurrences":1
          }
       ],
       "SubmissionId":"24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

CODE

    
    
    {
       "Submission":"f6d6d1af-234a-42b4-ad4f-e47263700fa8",
       "Errors":[
          {
             "Error":"ProductId 99934 not found."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    Invalid OmedaCustomerId.
    OmedaCustomerId {OmedaCustomerId} not found.
    Invalid BehaviorId.
    BehaviorId {BehaviorId} not found.
    Invalid ProductId.
    ProductId {ProductId} not found.
    No behaviors associated with ProductId {ProductId} 
    No behaviors associated with OmedaCustomerId {OmedaCustomerId} 

**Table of Contents**

×

