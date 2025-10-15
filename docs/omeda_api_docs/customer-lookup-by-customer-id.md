# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-lookup-
by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up a Customer by the **Customer id**. The
response will include basic Customer information and various links to look up
additional Customer information such as Demographics, Addresses, etc for a
single Customer record.

  * If the customer has been merged into another customer or deactivated an error message will be returned. (Please see failure section for more details)

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/*
    

brandAbbreviation is the abbreviation for the brand customerId is the internal
customer id (encrypted customer id may also be used)

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is always **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup Customer By Customer Id

Retrieves a record containing all available name, contact, and demographic
information about the customer.

### Field Definition

The following table describes the data elements present on the response from
the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Customer Elements

OriginalPromoCodeNoStringOriginal âPromo Codeâ that was used to create
this customer.

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Conditional| Integer| internal id (for use on certain databases)  
ReaderId| Conditional| Integer| reader id (for use on certain databases)
typically either the reader id or the id is returned, but not both.  
EncryptedCustomerId| Yes| String| The Encrypted Customer Id for the customer  
Salutation| No| String| âMrs.â, âMr.â, etc.  
FirstName| No| String| first name  
MiddleName| No| String| middle name  
LastName| No| String| last name  
Suffix| No| String| âJr.â, âSr.â, âIIIâ, etc.  
Title| No| String| job title  
Gender| No| String| âFâ for Female, âMâ for Male, âUâ for Unknown.  
PromoCode| No| String| âPromo Codeâ last used to create/update this
customer.  
SignUpDate| No| DateTime| Date & time customer âsigned upâ as customer.
yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
ChangedDate| No| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| No| Short| 1 for âActiveâ, 0 for âDeleted/Inactiveâ, 2 for
âProspectâ.  
MergeCode| Yes| Short| 1 for âMergeableâ, 0 for âNon-Mergeableâ  
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
       "Salutation" : "Mr.",
       "FirstName" : "John",
       "MiddleName" : "J",
       "LastName" : "Smith",
       "Suffix" : "Jr.",
       "Title" : "Developer",
       "Gender" : "M",
       "ClientCustomerId" : "543210",
       "OriginalPromoCode" : "2009_SIGNUP_SPECIAL",
       "PromoCode" : "2010_RENEWAL",
       "SignUpDate" : "2009-03-08 21:23:34",
       "ChangedDate" : "2010-03-08 14:07:12",
       "StatusCode" : 1,
       "MergeCode" : 1,
       "Addresses" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/address/*",  
       "Phones" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/phone/*",
       "EmailAddresses" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/email/*",
       "CustomerDemographics" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/demographic/*",
       "Subscriptions" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/subscription/*"
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
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

    
    
    Customer {customerId} was not found.
    Customer id {customerId} is valid but not active. Please use {mergedIntoCustomerId}.
    OmedaCustomerId {customerId} is pending deactivation. Please try again later.

**Table of Contents**

×

