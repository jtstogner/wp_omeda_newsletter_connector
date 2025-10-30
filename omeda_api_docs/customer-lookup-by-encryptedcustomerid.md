# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-lookup-
by-encryptedcustomerid

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up a Customer by the **Encrypted Customer
id**. The response will include basic Customer information and various links
to look up additional Customer information such as Demographics, Addresses,
etc for a single Customer record.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{encryptedCustomerId}/encrypted/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{encryptedCustomerId}/encrypted/*
    

brandAbbreviation is the abbreviation for the brand encryptedCustomerId is the
internal encrypted customer id

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

## Lookup Customer by Encrypted Customer Id

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
Id| No| Integer| internal id (for use on certain databases)  
ReaderId| No| Integer| reader id (for use on certain databases) typically
either the reader id or the id is returned, but not both.  
Salutation| No| String| âMrs.â, âMr.â, etc.  
FirstName| No| String| first name  
MiddleName| No| String| middle name  
LastName| No| String| last name  
Suffix| No| String| âJr.â, âSr.â, âIIIâ, etc.  
Title| No| String| job title  
Gender| No| String| âFâ for Female, âMâ for Male, âUâ for Unknown.  
OriginalPromoCode| No| String| Original âPromo Codeâ that was used to
create this customer.  
PromoCode| No| String| âPromo Codeâ last used to create/update this
customer.  
SignUpDate| No| DateTime| Date & time customer âsigned upâ as customer.
yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
ChangedDate| No| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| No| Short| 1 for âActiveâ, 0 for âDeleted/Inactiveâ, 2 for
âProspectâ.  
MergeCode| Yes| Short| 1 for âMergeableâ, 0 for âNon-Mergeableâ.  
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
       "Subscriptions" : "https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/subscription/*",
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Encrypted CustomerId 12345 was not found."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    Encrypted CustomerId {encryptedCustomerId} was not found.

**Table of Contents**

×

