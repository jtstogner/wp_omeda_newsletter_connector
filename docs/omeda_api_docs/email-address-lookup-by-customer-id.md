# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-address-
lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up a Customerâs Email Addresses by the
**Customer Id**. This service returns all active email address information
stored for the given customer.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/email/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/email/*
    

brandAbbreviation

is the abbreviation for the brand

customerId

is the internal customer id (encrypted customer id may also be used)

### HTTP Headers

The HTTP header must contain the following element:

x-omeda-appid

a unique id provided to you by Omeda to access your data. The request will
fail without a valid id.

### Content Type

The content type is **application/json**.

JSON

application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup Email Addresses By Customer Id

Retrieves a record containing all available active email information about a
customer.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Emails Elements

Element Name| Description  
---|---  
Customer| Element containing an http reference to the owning customer
resource.  
Emails| each Email element contains all email information.  
  
##### Email Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Long| unique email identifier  
EmailContactType| No| Integer| integer that defines the type of email (see
[Standard API Constants and Codes](../omedaclientkb/api-standard-constants-
and-codes)).  
EmailAddress| Yes| String| actual email address  
ChangedDate| Yes| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| No| Byte| Status of email address: 1 is the primary email address,
2 is an active email address.  
HashedEmailAddress| No| String| The hashed value of the email address.  
  
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
       "Emails":[
          {
            "Id":472517,
            "EmailContactType":300,
            "EmailAddress":"jsmith@omeda.com",
            "StatusCode": 1,
            "ChangedDate": "2015-03-08 21:23:34"
          },
          {
            "Id":472518,
            "EmailContactType":310,
            "EmailAddress":"jsmith@domain.com",
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
             "Error":"No email address found for customer 12345."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No email address found for customer {customerId).
    

**Table of Contents**

×

