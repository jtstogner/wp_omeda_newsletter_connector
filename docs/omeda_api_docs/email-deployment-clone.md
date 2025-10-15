# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-clone

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Clone Deployment Service API provides the ability to post deployment
information to Omail. This information is used to clone existing Omail
deployment. Deployment information is validated for basic information.

An HTTP POST request is used to clone existing deployment. The TrackId of the
deployment to clone must be included in the request JSON.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/clone/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/clone*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

Please note: When migrating from the testing environment to the production
environment, deployments created or updated in the testing environment will
not be available in the production environment. You will need to create new
deployments and for this reason should not hard code any deployment tracking
numbers in your code.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-clone) for more details. If
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

POST method is used when creating a new deployment.

## Field Definition

The following tables describe the hierarchical data elements.

  * Any optional attributes if not specified â retain their values from cloned deployment unless otherwise stated in description.

#### Deployment Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
CloneTrackId| required| string| TrackId of the existing deployment to be
cloned.  
DeploymentName| optional| string| The name designated for the deployment. Max
Characters: 80. If not sent â the string âCopy of â will be added to
name of cloned deployment.  
DeploymentDate| required| date| The tentative date the deployment will be
sent. Format: âyyyy-MM-dd HH:mmâ. The date must be in the future.  
OwnerUserId| optional| string| The User ID of an active Omail account to be
designated as the âownerâ. The owner is generally the account that will be
working on the deployment throughout the creation and sending process.  
CampaignId| optional| string| An optional Campaign Id to assign to the
deployment. Max Characters: 50.  
TrackOpens| optional| byte| 1 = Track and store the opening of HTML emails. 0
= do not track and store the opening of HTML emails.  
TrackLinks| optional| byte| 1 = Track and store link clicks on the deployment.
0 = do not track and store link clicks on the deployment.  
Testers| optional| array| An array of json objects. Each object contains
deployment tester information: First Name, Last Name, and Email Address. When
provided â passed testers are merged with testers on cloned deployment.  
FinalApproverUserId| optional| string| The User ID of an active Omail account
to be designated as the âfinal approverâ.  
Notes| optional| string| Optional user-specified notes regarding the
deployment.  
ReloadOnqQueryBeforeFinalDeployment| optional| byte| 1 = If deployment
audience is from an Audience Builder query, then re-execute the query before
final deployment time. 0 = do not re-execute Audience Builder query before
deployment time (if applicable).  
  
#### Testers Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
FirstName| required| string| The first name of the tester. Max Characters: 80.  
LastName| required| string| The last name of the tester. Max Characters: 80.  
EmailAddress| required| string| The email address of the tester. Max
Characters: 255.  
  
### POST JSON Request Example: cloning deployment

CODE

    
    
    {
        âCloneTrackId" : "FOO0200300112",
        "DeploymentName": "Test Warmup - #2",
        "DeploymentDate": "2012-02-29 13:45",
        "CampaignId": "Campaign2",
        "OwnerUserId": "omailuser1",
        "Notes": "Don't send until Mar. 30th."
    }
    

## Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST (400
Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful POST Submission

A successful POST submission will clone specified existing deployment and
create an Omail deployment shell with the designated information from the
request body. A successful POST request will return a Url that can be used to
retrieve deployment information such as link tracking, delivery statistics,
deployment status, history, etc. (See [Deployment Lookup
Resource](../omedaclientkb/email-deployment-lookup)).

#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
          "TrackId":"FOO0200300112",
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/omail/deployment/lookup/FOO0200300112/*"
        }
      ]
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    'OwnerUserId' {ownerUserId} is not active.
    'FinalApproverUserId' {finalApproverUserId} is not active.
    '{RequiredFieldName}' is a required field.
    The Duplicate value '{emailAddress}' submitted for Testers array, field 'EmailAddress'. Tester emails must be unique.
    The value '{trackLinks}' for field 'TrackLinks' must be 0 or 1.
    The value '{trackOpens}' for field 'TrackOpens' must be 0 or 1.
    'CloneTrackId' is a required field.
    No deployment was found matching trackId '{trackId}'.
    Deployment '{trackId}' cannot be cloned.
    OwnerUserId '{ownerUserId}' is not authorized to edit deployment '{trackId}'"
    Invalid value '{deploymentDate}' for field 'DeploymentDate'. The date must be in the future.
    Invalid value '{deploymentDate}' for field 'DeploymentDate'. Date format yyyy-MM-dd HH:mm is required.
    

A failed POST submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications. In the case of an update, this can occur if an HTTP PUT
request is submitted without a TrackId element in the submitted json data.  
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
          Error": "A 'CloneTrackId' element is required." 
        },
        {
          "Error": "' DeploymentDate' is a required field."
        }
      ]
    }

**Table of Contents**

×

