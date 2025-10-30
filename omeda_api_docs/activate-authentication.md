# Content from: https://knowledgebase.omeda.com/omedaclientkb/activate-
authentication

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to activate a userâs status code for the
customer id and namespace.

  * Prior to using the Authentication APIs a Password Policy and Authentication Namespace must be setup. Please contact your Account Representative to start the setup process.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/authentication/activate/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/authentication/activate/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/activate-authentication) for more details.

## Supported HTTP Methods

There is one HTTP method supported:PUTSee [W3Câs PUT
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
PUT method to store authentication data in the database. All fields are
required

### Authentication Elements

Attribute Name| Description  
---|---  
Omeda Customer Id| the internal id of the customer.  
Status Code| the status code value is either 1 or 2.  
ExternalCustomerIdNamespace| the namespace associated with the authentication
instance.  
  
## Request Examples

### JSON Example

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "StatusCode":1,
       "ExternalCustomerIdNamespace":"AbcAuth"
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

