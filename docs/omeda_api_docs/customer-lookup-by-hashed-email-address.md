# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-lookup-
by-hashed-email-address

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up customers using their **Hashed Email
Address**. The response will include a list of customer records including the
Customer Id(s) and the Customer Lookup URL(s).

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/hashedemail/{hashedEmailAddress}/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/hashedemail/{hashedEmailAddress}/*  
    
    

**brandAbbreviation** is the abbreviation for the brand

**hashedemail** is the Hashed value of the email address for which we are
requesting customer information

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

## Lookup All Customers for Hashed Email Address

Retrieves all customers for the hashed email address.

### Field Definition

The following table describes the data elements present on the response from
the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Customers Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| the internal customer identifier  
Url| Yes| Link| a link reference to the customer data as a resource.  
  
### Response

#### HTTP Response Codes

It is possible that multiple customers are returned for the same email
address. If only one is found, a 200 OK status is returned. If more than one
is found, a 300 Multiple Choices status is returned.

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
300 Multiple Choices| This response will be returned when more than one
customer was found for that email address. Response body will contain a list
of all customers that have that email address.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| This response occurs when the email address submitted was not
found.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
#### Success

CODE

    
    
    {
       "Customers":[
          {
             "Url":"http://ows.omedastaging.com/webservices/rest/brand/FOO/customer/111/*"
             "Id":111
          },
          {
             "Url":"http://ows.omedastaging.com/webservices/rest/brand/FOO/customer/2222/*"
             "Id":2222
          }
       ]
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Hashed Email address 12345646 not found"
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    Hashed Email address {emailAddress} not found.

**Table of Contents**

×

