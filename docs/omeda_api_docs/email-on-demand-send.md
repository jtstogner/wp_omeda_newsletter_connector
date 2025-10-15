# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-on-demand-
send

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment API allows our clients to send a single Omail email deployment.

CODE

    
    
     Please work with your Client Services Manager for the initial setup and access to this API.
    

## Base Resource URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployemails/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployemails/*
    

brandAbbreviationis the brand identifier for your brand or site

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-on-demand-send) for more details. If
omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following table describes the data elements that can be included in the
POST method to request an email deployment.

### Elements

Element Name| Required?| Data Type| Description  
---|---|---|---  
Track Id| yes| string| publicly viewable tracking id for the relevant API
triggered deployment.  
EmailAddress| yes| string| address for the recipient of this email.  
FirstName| optional| string| first name of the recipient of this email.  
LastName| optional| string| last name of the recipient of this email.  
Subject| optional| string| if provided, will override the subject configured
on the deployment.  
TextContent| optional| string| if provided, will override all Text content
defined on the deployment.  
HtmlContent| optional| string| if provided, will override all HTML content
defined on the deployment.  
TextContentUrl| optional| string| if provided, the API will pull the text from
the url which will override all text content defined on the deployment.  
HtmlContentUrl| optional| string| if provided, the API will pull the HTML from
the url which will override all HTML content defined on the deployment.  
OmedaCustomerId| optional| string/number| If you have an omeda customer id or
encrypted customer id at the time you wish to send an On Demand Send email â
passing this field will allow you to query for clicks/opens of the email in
Audience Builder.  
Preference| optional| string| if provided, and if multiple content formats are
provided, indicates the preference for this email. Valid values are **TEXT**
and **HTML**.  
FromName| optional| string| if provided, overrides the name associated with
the from email configured on the deployment.  
MergeVariables| optional| List| If merge variables are included on the
deployment, they are required to be listed here as well. This is a list
consisting of the merge variable name followed by a colon (:) and then the
actual variable to be used. The list is separated by comas (,).  
  
## Request Examples

### JSON Example

CODE

    
    
    {
     "TrackId":"Track Id",
     "EmailAddress":"jsmith@omeda.com",
     "FirstName":"John",
     "LastName":"Smith",
     "Subject":"Confirmation Email",
     "TextContent":"This is a confirmation email.",
     "HtmlContent":"<h1>Subscription Email</h1><p>This is a confirmation email.</p>",
     "Preference":"HTML",
     "FromName":"Customer Service",
     "OmedaCustomerId":"1000000192",
     "MergeVariables": [{
        "city": "chicago",
        "state": "IL",
        "company": "omeda",
        "customerid": "123456789",
        "postalcode": "99911",
        "ordertotal": "$159.00",
        "orderid": "123456789",
        "fullname": "Jane Doe"
      }],
    }
    
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will create a deployment in Omail for a single
email.

CODE

    
    
    {
      "SubmissionId": "70xbe874-e715-4ad0-8306-4e89d3513764"
      "Status": "Email Message submitted for delivery",
    }
    
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

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
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "SubmissionId": "b6ce4257-6bd6-4286-b9a5-dfa7babcb4e8",
      "Errors": [
        {
          "Error": "Deployment is not an API triggered deployment"
        }
      ]
    }
    

In the rare case that there is a server-side problem, an HTTP 500 (server
error) will be returned. This generally indicates a problem of a more serious
nature, and submitting additional requests may not be advisable.Please contact
Omeda Account Representative.

**Table of Contents**

×

