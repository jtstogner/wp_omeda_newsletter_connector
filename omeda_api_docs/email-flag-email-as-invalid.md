# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-flag-
email-as-invalid

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Invalid Email API allows our client to mark a customer Email Address as
invalid for a brand.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/email/invalid/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/email/invalid/*
    

brandAbbreviationis the abbreviation for the brand who is posting the data.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-flag-email-as-invalid) for more details.
If omitted, the default content type is application/json.

## Supported Content Types

There are three content types supported. If omitted, the default content type
is **application/json**.JSONapplication/json

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
POST method to store data in the database.

#### DeploymentTypeOptIn Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
EmailAddress| required| string| String element containing the email address
that will be marked as invalid.  
  
### JSON Example

CODE

    
    
    {
        "EmailAddress": "test@test.com"
    }
    
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will create BounceStatus entries for all
customers with the EmailAddress provided.

#### JSON Example

The following is an example of the response that is returned from a successful
POST submission:

CODE

    
    
    {  
       "EmailAddress":"test@test.com",
       "OmedaCustomerIds":[  
          1100000000,
          1100000001
       ],
       "Success":"Email Address has been marked as Invalid for customers"
    }
    

### Failed Submission

A failed POST submission may be due to several factors:

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
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

The following is an example of a response that might be returned from a failed
POST submission:

CODE

    
    
    {  
       "Errors":[  
          {  
             "Error":"Email address test@test.com was not found."
          }
       ],
       "SubmissionId":"853295f9-de02-4c86-8321-caeea8403cd5"
    }

**Table of Contents**

×

