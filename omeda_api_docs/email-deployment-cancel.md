# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-cancel

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Cancel API provides the ability to cancel a deployment. Please
be warned, once cancelled, a deployment will not be sent and cannot be edited
further. If you need to unschedule a deployment and continue editing, please
see the [Deployment Unschedule API](../omedaclientkb/email-deployment-
unschedule).

An HTTP POST request is used when canceling a deployment.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/cancel/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/cancel/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-cancel) for more details. If
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

POST method is used when assigning a list to a deployment split that does not
have an existing list attached.

## Field Definition

The following tables describe the hierarchical data elements.

#### List Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
UserId| required| string| UserId of the omail account authorized for this
deployment. This is generally the âOwnerUserIdâ specified in the
[Deployment Api](../omedaclientkb/email-deployment)  
TrackId| required| string| TrackId is the unique identifier for the
deployment.  
  
### POST JSON Request Example: When cancelling a deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002"
    
    }
    

## Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST (400
Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful submission will cancel the specified deployment. A successful
POST request will return a Url that can be used to retrieve deployment
information such as link tracking, delivery statistics, deployment status,
history, etc. (See [Deployment Lookup Resource](../omedaclientkb/email-
deployment-lookup)). A successful request will also return a unique
submissionId that can be used as needed.

#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "TrackId":"FOO0200300112",
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/omail/deployment/list/status/1000343/*",
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E"
        }
      ]
    }
    

### Failed Submission

A failed request will return a unique submissionId that can be used as needed.

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    The field '{fieldName}' is required.
    Deployment '{trackId}' was sent on {sendDate}.
    Deployment '{trackId}' has been previously cancelled.
    User '{userName}' is not authorized to cancel this deployment.
    

A failed submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid or a UserId that is not
authorized to edit the specified deployment.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a TrackId
submitted is not found in our system.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
appropriate HTTP Method (POST) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          Error": "'TrackId' is a required field." 
        },
        {
          "Error": "User 'omailaccount1' is not authorized to edit this deployment."
        }
      ],
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F"
    }

**Table of Contents**

×

