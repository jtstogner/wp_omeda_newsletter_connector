# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-test

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Test API provides the ability to send test copies of your
deployment to the test recipients that were specified when the deployment was
created. Test recipients are optional and can be added to a deployment via the
[Deployment Api](../omedaclientkb/email-deployment). A Deployment Test
Resource api call is required before [Scheduling a
deployment](../omedaclientkb/email-deployment-schedule).

An HTTP POST request is used to send a test.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/sendtest/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/sendtest/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-deployment-test) for more
details. If omitted, the default content type is application/json.

Optional header element:

## Supported Content Types

If omitted, the default content type is **application/json**. JSON
application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported: POST See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

POST method is used when creating a new deployment.

## Field Definition

The following tables describe the hierarchical data elements.

#### Deployment Test Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
UserId| required| string| UserId of the active omail account authorized for
this deployment. This is generally the âOwnerUserIdâ specified in the
[Deployment Api](../omedaclientkb/email-deployment)  
TrackId| required| string| The TrackId for the deployment.  
  
### POST JSON Request Example: When sending deployment tests

CODE

    
    
    {
        "UserId": "omailaccount1",
        "TrackId": "FOO020300219"
    }
    

## Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST(400 Bad
Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will send test copies of the specified deployment
to the deployment test recipients. The response object will contain the
TrackId of the deployment, a unique âSubmissionIdâ, and a url to call the
[Deployment Lookup API](https://training.omeda.com/knowledge-base/api-email-
deployment-lookup-resource/) for this deployment.

Status| Description  
---|---  
200 OK| Please be sure to check the response json for warnings. A typical
warning would be that your content has no unsubscribe link.  
  
#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
          "TrackId":"FOO0200300112",
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/omail/deployment/lookup/FOO0200300112/*",
          "Warnings" : ["Warning" : "Missing Unsubscribe Link for split 1 in HTML"}]
        }
      ]
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    The field '{fieldName}' is required.
    One of the following fields must be set: 'HtmlContent' or 'TextContent'.
    No deployment was found matching trackId '{trackId}'.
    Deployment '{trackId}' cannot be edited. Sent, Scheduled , Approved, or Cancelled deployments cannot be edited.
    UserId '{userId}' is not authorized to edit deployment '{trackId}'"
    Deployment '{trackId}' has been edited from the Omail portal and is not eligible for API access. Last edited by {account} on 2012-02-04 22:15:00.
    Deployment '{trackId}'  was created within the Omail portal and is not eligible for API access.
    There is no final approver assigned to this deployment.
    There is no mailbox domain set up for this communication type.
    Audience list {listName} is not valid (recipient upload did not complete).
    From Name is missing from Split 1.
    Mailbox is missing from Split 1.
    Subject is missing from Split 1.
    There is no Message Body (Text) on Split 1.
    There is no Message Body (HTML) on Split 1.
    Message Body (Text) on Split 1 should not contain html tags.
    Message Body (HTML) on Split 1 must contain html and body tags.
    There are no splits that are ready or approved for testing.
    There are testers or seed recipients that are missing values for the merge variables.
    Illegal characters in merge variable name: {mergeVariable}.
    Mismatched start/end merge variables delimiters in TEXT message on Split 1.
    Mismatched start/end merge variables delimiters in HTML message on Split 1.
    Mismatched start/end merge variables delimiters in message subject on Split 1.
    Mismatched start/end merge variables delimiters in From line on Split 1.
    

A failed POST submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the deployment content is
invalid. See potential errors for details.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a TrackId
submitted is not found in our system.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          "Error": "'TrackId' is a required field." 
        },
        {
          "Error": "'UserId' is a required field."
        }
      ]
    }

**Table of Contents**

×

