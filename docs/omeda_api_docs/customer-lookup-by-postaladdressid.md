# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-lookup-
by-postaladdressid

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up customers using their **Postal Address
Id**(this is the ID on the magazine mailing labels). The response will include
Customer Id and various links to look up additional Customer information such
as Demographics, Addresses, etc for a single Customer record.

  * If the customer has been merged into another customer or deactivated an error message will be returned. (Please see failure section for more details)

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{postaladdressId}/postaladdressid/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{postaladdressId}/postaladdressid/*
    

brandAbbreviation is the abbreviation for the brand postaladdressId is the
internal postal address id. This is typically what a magazine subscriber would
find on their mailing label.

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

content type is **application/json**.

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup Customer By Postal Address Id

Retrieves a single customer record containing all available name, contact, and
demographic information about the customer.

### Field Definition

The following table describes the data elements present on the response from
the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Customer Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| internal id (for use on certain databases)  
Customer| Yes| Link| a link reference to the customer data as a resource.  
Addresses| Yes| Link| a link reference to the address data as a resource.  
Phones| Yes| Link| a link reference to the phone data as a resource.  
EmailAddresses| Yes| Link| a link reference to the email data as a resource.  
CustomerDemographics| Yes| Link| a link reference to the customer demographic
data as a resource.  
Subscriptions| Yes| Link| a link reference to the subscription data as a
resource.  
  
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
       "Id" : 12345,
       "Customer" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/*",  
       "Addresses" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/address/*",  
       "Phones" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/phone/*",
       "EmailAddresses" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/email/*",
       "CustomerDemographics" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/demographic/*",
       "Subscriptions" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/subscription/*",
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

###### Standard Customer Error Message

CODE

    
    
    {
       "SubmissionId":"44385156-9df6-4568-94ae-5597d26a4e60",
       "Errors":[
          {
             "Error":"Postal Address Id 123456 was not found."
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

    
    
    Postal Address Id {postaladdressId} was not found.
    Customer id {customerId} is valid but not active. Please use {mergedIntoCustomerId}.
    OmedaCustomerId {customerId} is pending deactivation. Please try again later.

**Table of Contents**

×

