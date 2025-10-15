# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-merge-
history-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the merge history for the requested
**Customer Id**.

  * If the customer has been merged into another customer or deactivated an error message will be returned. (Please see failure section for more details)

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerid}/mergehistory/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerid}/mergehistory/*
    

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

## Lookup All Customers for Email Address

Retrieves all customers for the email address.

### Field Definition

The following table describes the data elements present on the response from
the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### CustomerMergeHistory Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| the internal customer identifier  
  
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
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "CustomerMergeHistory":[
          {
             "Id":111
          },
          {
             "Id":2222
          }
       ]
    }
    

#### Failure

###### Standard Customer Error Message

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Customer 12345 was not found."
          }
       ]
    }
    

###### Merged Customer Error Message

CODE

    
    
    {
       "SubmissionId":"2da476ca-9ae6-4b4a-a77c-4aafbd275028",
       "Errors":[
          {
             "MergedIntoCustomerId":99999,
             "Error":"Customer id 22 is valid but not active. Please use 99999."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No customerId was provided.
    Customer {customerId} was not found.
    Customer id {customerId} is valid but not active. Please use {mergedIntoCustomerId}.
    OmedaCustomerId {customerId} is pending deactivation. Please try again later.
    No merge history was found for Customer {customerId}.
    There was a problem determining the winner. Please contact customer service.
    There was a problem with merging this customer. Please contact customer service.
    Problem determining winner for Customer Id {customerId}.
    

This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

**Table of Contents**

×

