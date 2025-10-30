# Content from: https://knowledgebase.omeda.com/omedaclientkb/update-
authentication

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to post an update to username and/or password
for an existing customer for authentication.

  * Prior to using the Authentication APIs a Password Policy and Authentication Namespace must be setup. Please contact your Account Representative to start the setup process.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/authentication/update/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/authentication/update/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/update-authentication) for more details.

## Supported Content Types

JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:PUTSee [W3Câs PUT
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store authentication data in the database.

### Authentication Elements

Attribute Name| Required?| Description  
---|---|---  
ExternalCustomerIdNamespace| required| the namespace associated with the
authentication instance.  
Username| required| the current username for this customer and namespace.  
New Username| optional**| the new username for this customer and namespace.
Username must be unique for the Brand & Namespace combination.  
Password| required| the current password for this customer and namespace.  
New Password| optional**| the new password for this customer and namespace.
The password is case sensitive and it must meet the length requirement that is
set in the Password Policy. Password can only consist of alphanumeric
characters or ~!@#$%^&*()_-+=?.<>  
  
** Note: either New Username or New Password must be provided in order to
update authentication, but either one can be updated individually.

## Request Examples

### JSON Example

CODE

    
    
    {
       "Username":"someUser",
       "NewUsername":"newUsername",
       "Password":"somePassword",
       "NewPassword":"newPassword",
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

#### JSON Example

CODE

    
    
    {
       "ResponseInfo":[
          {
             "OmedaCustomerId":100060713,
             "Success":"Username/Password combination updated successfully."
          }
       ],
       "SubmissionId":"0575fce0-fba9-4ab9-8a24-14553da0023f"
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

Potential errors:

CODE

    
    
    Nothing to change. Please enter a new Username or a new Password 
    PasswordPolicy not found. 
    Failed to authenticate user. Please try again. 
    Multiple matches found. Cannot update. 
    NewUsername {newUsername} is already in use. 
    Username is required 
    Username cannot be blank 
    Password is required. 
    Password cannot be blank 
    NewUsername cannot be blank 
    NewPassword cannot be blank 
    Error Occurred. External Customer Namespace not found. 
    Error Occurred. Multiple External Customer Namespaces found. 
    Password is inactive. 
    Password has expired. 
    Password Policy not found for Brand. 
    Multiple Password Policies found. 
    Password does not meet minimum length requirement. 
    Password exceeds maximum length requirement.
    Password can only consist of alphanumeric characters or ~!@#$%^&*()_-+=?.<>
    

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
       "SubmissionId":"a4b4b472-4120-4e63-9dea-23ce6e2e5669",
       "Errors":[
          {
             "Error":"Failed to authenticate user. Please try again."
          },
          {
             "Error":"Failed to update account."
          }
       ]
    }

**Table of Contents**

×

