# Content from: https://knowledgebase.omeda.com/omedaclientkb/phone-lookup-by-
customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up a Customerâs Phone Numbers by the
**Customer id**.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/phone/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/phone/*
    

brandAbbreviation is the abbreviation for the brand customerId is the internal
customer id (encrypted customer id may also be used)

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

## Lookup Phone Information By Customer Id

Retrieves a record containing all available phone information about a
customer.

### Field Definition

The following table describes the hierarchical data elements.

#### Phone Numbers Elements

Element Name| Description  
---|---  
Customer| Element containing an http reference to the owning customer
resource.  
PhoneNumbers| each Phone element contains all phone information.  
  
##### Phone Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| unique phone identifier  
PhoneContactType| Yes| Integer| integer that defines the type of phone (see
[Standard API Constants and Codes](../omedaclientkb/api-standard-constants-
and-codes))  
PhoneNumber| Yes| String| actual phone number  
Extension| No| String| phone extension  
ChangedDate| Yes| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| No| Byte| Status of the postal address: 1 is the primary address,
2 is an active address.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
#### Success

CODE

    
    
    {
      "Customer":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/*",
      "PhoneNumbers":[
        {
          "Id":472517,
          "PhoneContactType":200,
          "PhoneNumber":"8475648900",
          "Extension":"999",
          "StatusCode": 1,
          "ChangedDate": "2015-03-08 21:23:34"
        },
        {
          "Id":472518,
          "PhoneContactType":210,
          "PhoneNumber":"8475648901",
          "StatusCode": 1,
          "ChangedDate": "2015-03-08 21:23:34"
        }
      ]
      "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No phone number found for customer 12345."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No phone number found for customer {customerId}.

**Table of Contents**

×

