# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-search

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service retrieves a list of most recent deployments for a given brand
based on search parameters.

## Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/search/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/search/*
    

brandAbbreviationis the abbreviation for the brand

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-search) for more details. If
omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs
POSTspecs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following table describes the hierarchical data elements.

### Search Elements Submitted

Element Name| Required?| Data Type| Description  
---|---|---|---  
DeploymentName| optional| string| Text match for deployment name.  
TrackId| optional| string| Text match for deployment trackId.  
EnteredByOrAssignedTo| optional| string| Text match for user. Matches both the
deployment Owner and the deployment creator. In the case of API deployments,
the owner and the creator will be the same user.  
DeploymentDateStart| optional| string| Deployments have been sent after this
date. Format: âyyyy-MM-dd HH:mmâ, Ex. 2012-01-03 09:30.  
DeploymentDateEnd| optional| string| Deployments have been sent prior to this
date. Format: âyyyy-MM-dd HH:mmâ, Ex. 2012-02-03 21:30.  
Statuses| optional| array| An array of internal Omail deployment statuses.
Valid submission statuses are: âNEWâ,âSENT_OR_SENDINGâ,
âSCHEDULEDâ, âSENDINGâ, âSENTâ, âCANCELLEDâ,
âWAITING_REVIEWâ. Values other than these will be ignored by the API.  
DeploymentDesignations| optional| array| An array of Deployment Designations.
Valid values are: âNewsletterâ,âWebinarâ,âThird
Partyâ,âResearchâ,âLive Conferencesâ,âVirtual Conferencesâ, and
âMarketingâ.  
NumResults| optional| integer| Maximum number of deployments returned. Max /
Default is 200.  
Type| optional| integer| The deployment type ID you wish to filter the results
by.  
  
### Deployment List Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
Owner| required| string| Omail user who owns the deployment. Generally this is
the creator of the deployment.  
Status| required| string| The deployment state.  
FinalApprover| required| string| Omail account userID specified as the final
approver of the deployment.  
Url| required| link| Url for the [Deployment Lookup
Api](../omedaclientkb/email-deployment-lookup).  
DeploymentTypeId| required| integer| Deployment type id for the deployment.  
DeploymentTypeDescription| required| string| The ânameâ of the deployment
type for the deployment. Example: âDigital Newslettersâ.  
DeploymentDesignation| required| string| The deployment designation.  
TrackId| required| string| Omail deployment tracking number.  
CreatedDate| required| datetime| Date & time the deployment was created. yyyy-
MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
CreatedBy| required| string| Omail account userID that created the deployment.  
ScheduledDate| conditional| datetime| Date & time the deployment is scheduled
to deploy. yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
SentDate| conditional| datetime| Avaialable only if the deployment has been
sent. Date & time the deployment was sent. yyyy-MM-dd HH:mm:ss format.
Example: 2010-03-08 21:23:34.  
DeploymentName| required| string| User-specified deployment name  
  
## Request Example #1

This example would search the Omail system for a deployment name containing
the letter âeâ, with TrackId containing the letter âeâ, with a
scheduled date between March. 28th 2012 12:00 AM and April 2nd, 2012 at 11:59
PM, with a status of âSENTâ or âSENDINGâ,for deployment whose type is
designated as âWebinarâ or âNewsletterâ, with a maximum number of
results returned being 50.

CODE

    
    
    {
        "DeploymentName": "e",
        "TrackId": "e",
        "EnteredByOrAssignedTo": "e",
        "DeploymentDateStart": "2012-03-28 00:00",
        "DeploymentDateEnd": "2012-04-02 23:59",
        "Statuses": [
            "SENT_OR_SENDING"
        ],
    
        "DeploymentDesignations" : [
         "Webinar", "Newsletter"
        ],
    
        "NumResults": 50
    }
    

## Request Example #2

This would be the search object for searching the top 50 most recent
deployments that have a status of sent.

It is important to note that if a field is not included in the search JSON,
then the field is not included as part of the search. For instance, in this
example, âDeploymentDateStartâ and âDeploymentDateEndâ were not
included in the search JSON. This means that the search will not be restricted
by the date the deployment was sent.

CODE

    
    
    {
    
        "Statuses": [
            "SENT"
        ],
        "NumResults": 50
    }
    

## Response Example

### JSON Example

CODE

    
    
    {
        "Deployments": [
            {
                "Owner": "JDOE",
                "Status": "Scheduled",
                "FinalApprover": "JDOE",
                "Url" : "https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/FOO120330019/*"
                "DeploymentTypeId": 10057,
                "DeploymentTypeDescription": "Digital Subscriptions",
                "TrackId": "FOO120330019",
                "CreatedDate": "2012-03-30 11:53:23",
                "CreatedBy": "JDOE",
                "ScheduledDate": "2012-04-04 20:00:00",
                "DeploymentName": "4-4-12 FOO Deployment #1",
                "DeploymentDesignation": "Webinar"
            },
            {
                "Owner": "JDOE",
                "Status": "Scheduled",
                "FinalApprover": "JDOE",
                "Url" : "https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/FOO120330019/*"
                "DeploymentTypeId": 10057,
                "DeploymentTypeDescription": "Webinar Notices",
                "TrackId": "FOO120330029",
                "CreatedDate": "2012-03-30 16:37:03",
                "CreatedBy": "JDOE",
                "ScheduledDate": "2012-04-03 09:00:00",
                "DeploymentName": "4-3-12 FOO Deployment #2",
                "DeploymentDesignation": "Newsletter"
            },
    }
    

### Failed Submission

A failed POST submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications. In the case of an update, this can occur if an HTTP PUT
request is submitted without a TrackId element in the submitted json data.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a
BrandAbbreviation submitted is not found in our system.  
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
          "Error": "The AppId submitted is forbidden access."
        }
      ]
    }

**Table of Contents**

×

