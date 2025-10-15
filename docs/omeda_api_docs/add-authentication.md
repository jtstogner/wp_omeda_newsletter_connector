# Content from: https://knowledgebase.omeda.com/omedaclientkb/add-
authentication

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to post a username and password to an existing
customer for authentication. This can be used to capture the log in
credentials to be used for logging into a gated site.

  * Prior to using the Authentication APIs a Password Policy and Authentication Namespace must be setup. Please contact your Account Representative to start the setup process.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/authentication/add/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/authentication/add/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/add-authentication) for more details.

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

Attribute Name| Required?| Description  
---|---|---  
OmedaCustomerId| required| the internal id of the customer.  
Username| required| the username for this customer and namespace. Username
must be unique for the Brand & Namespace combination.  
Password| required| the password for this customer and namespace. The password
is case sensitive and it must meet the length requirement that is set in the
Password Policy. Password can only consist of alphanumeric characters or
~!@#$%^&*()_-+=?.<>  
ExternalCustomerIdNamespace| required| the namespace associated with the
authentication instance.  
StatusCode| optional| if present, the status code must be value 2 (pending
activation). Any value other than null, will generate an error.  
  
## Request Examples

### JSON Example

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "Username":"someUser",
       "Password":"somePassword",
       "ExternalCustomerIdNamespace":"AbcAuth"
    }
    

or

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "Username":"someUser",
       "Password":"somePassword",
       "ExternalCustomerIdNamespace":"AbcAuth",
       "StatusCode":2
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

#### JSON Example

CODE

    
    
    {
       "ResponseInfo":[
          {
             "Success":"Customer credentials added successfully"
          }
       ],
       "SubmissionId":"c68b1952-03a4-41a0-9b3e-ceb976d96917"
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

Potential errors:

CODE

    
    
    OmedaCustomerId not found
    Omeda Customer is not active
    Username is required
    Username cannot be blank
    Password is required
    Password cannot be blank
    Customer already has a Username
    Customer {customerId} is not a member of this brand.
    ExternalCustomerIdNamespace not found
    Error Occurred. Multiple External Customer Namespaces found.
    Username {userName} is already in use.
    Password Policy not found for Brand.
    Multiple Password Policies found.
    Password does not meet minimum length requirement.
    Password exceeds maximum length requirement.
    Password can only consist of alphanumeric characters or ~!@#$%^&*()_-+=?.<>
    Failed to process password.
    There was a problem saving the password.
    

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
       "SubmissionId":"42468454-9708-4b8d-8ffe-3a0decee20a2",
       "Errors":[
          {
             "Error":"OmedaCustomerId not found"
          },
          {
             "Error":"There was a problem saving the account."
          }
       ]
    }
    

**Table of Contents**

×

