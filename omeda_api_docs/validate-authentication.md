# Content from: https://knowledgebase.omeda.com/omedaclientkb/validate-
authentication

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to validate a username and password for
authentication. This can be used to authenticate a user and get the Omeda
Customer Id for the authenticated user. **It will only validate usernames of
active customers.**

  * Prior to using the Authentication APIs a Password Policy and Authentication Namespace must be setup. Please contact your Account Representative to start the setup process.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/authentication/validate/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/authentication/validate/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/validate-authentication) for more details.

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
POST method to store authentication data in the database. All fields are
required

### Authentication Elements

Attribute Name| Description  
---|---  
Username| the username for this customer and namespace. Username must be
unique for the Brand & Namespace combination.  
Password| the password for this customer and namespace. The password is case
sensitive and it must meet the length requirement that is set in the Password
Policy. Password can only consist of alphanumeric characters or
~!@#$%^&*()_-+=?.<>  
ExternalCustomerIdNamespace| the namespace associated with the authentication
instance.  
  
## Request Examples

### JSON Example

CODE

    
    
    {
       "Username":"someUser",
       "Password":"somePassword",
       "ExternalCustomerIdNamespace":"AbcAuth"
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will create a Transaction in the data queue. The
response has a **ResponseInfo** element with two sub-elements, a TransactionId
element, the Id for the transaction, and a Url element, the URL that allows
you to check the status of your transaction. See [Transaction Lookup
Service](../omedaclientkb/transaction-lookup) for more details.

#### JSON Examples

CODE

    
    
    {
       "ResponseInfo":[
          {
             "OmedaCustomerId":100058767,
             "EncryptedCustomerId":"YKSLF3K323423",
             "Success":"Username and Password match. "
          }
       ],
       "SubmissionId":"083a265e-f0ca-4b17-9c8d-92a40e77ce14"
    }
    

CODE

    
    
    {
       "ResponseInfo":[
          {
             "OmedaCustomerId":100058767,
             "EncryptedCustomerId":"YKSLF3K323423",
             "Warning":"This temporary password will expire on 10-18-2013",
             "Success":"Username and Password match. "
          }
       ],
       "SubmissionId":"d19205e6-0bd6-474b-8590-73474bef8c51"
    }
    

CODE

    
    
    {
       "ResponseInfo":[
          {
             "OmedaCustomerId":100058767,
             "EncryptedCustomerId":"YKSLF3K323423",
             "StatusCode":2,
             "Warning":"This temporary password will expire on 10-18-2013",
             "Success":"Username and Password match. "
          }
       ],
       "SubmissionId":"d19205e6-0bd6-474b-8590-73474bef8c51"
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

Potential errors:

CODE

    
    
    Multiple matches found for {username} and Password combination. 
    Username is required 
    Username cannot be blank 
    Password is required 
    Password cannot be blank 
    ExternalCustomerIdNamespace is required 
    ExternalCustomerIdNamespace cannot be blank 
    ExternalCustomerIdNamespace not found 
    Multiple matches found for ExternalCustomerIdNamespace {externalNamespace} 
    The password for this account is inactive. 
    The password for this account expired {date} 
    Error occurred. Multiple users found for this Username 
    Password Policy not found for Brand. 
    Multiple Password Policies found. 
    Password does not meet minimum length requirement. 
    Password exceeds maximum length requirement.
    

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
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
       "SubmissionId":"3d1533ec-67c3-46ed-8d2c-44ec5233cd24",
       "Errors":[
          {
             "Error":"Username and Password do not match."
          }
       ]
    }

**Table of Contents**

×

