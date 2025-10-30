# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-unschedule

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Unschedule API provides the ability to unschedule a deployment,
perhaps to allow further editing. A deployment can be unscheduled anytime
before the user-defined deployment date. A deployment that is unscheduled will
not be sent until a new [Deployment Schedule Api](../omedaclientkb/email-
deployment-schedule) call is made.

The Deployment Unschedule API should not be confused with the [Deployment
Cancel Api](../omedaclientkb/email-deployment-cancel). A Deployment Cancel Api
call cancels a deployment and prevents any further editing of the deployment.

An HTTP POST request is used when scheduling a deployment to send.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/unschedule/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/unschedule/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-deployment-unschedule) for
more details. If omitted, the default content type is application/json.

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
  
### POST JSON Request Example: When unscheduling a deployment

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

A successful submission will unschedule the deployment. The deployment will no
longer be queued to send and can be edited further.

#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
          "TrackId":"FOO0200300112"
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/omail/deployment/lookup/FOO0200300112/*"
        }
      ]
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    The field '{fieldName}' is required.
    Deployment '{trackId}' is not currently scheduled.
    Deployment '{trackId}' was sent on {sendDate}.
    Deployment '{trackId}' has been previously cancelled.
    User '{userName}' is not authorized to unchedule this deployment.
    

A failed request will return a unique submissionId that can be used as needed.
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

