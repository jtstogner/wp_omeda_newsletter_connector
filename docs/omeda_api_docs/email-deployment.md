# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-deployment

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Service API provides the ability to post/put deployment
information to Email Builder. This information is used to either create a new
Email Builder deployment, or update an existing Email Builder deployment.
Deployment information is validated for basic information.

An HTTP POST request is used to create a new deployment.

An HTTP PUT request is used to update an existing deployment. The TrackId of
the deployment to update must be included in the request JSON.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/Email Builder/deployment/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/Email Builder/deployment/*
    

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
Content Types](../omedaclientkb/email-deployment) for more details. If
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

POST method is used when creating a new deployment.PUTSee [W3Câs PUT
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6) for
details.

PUT method is used when updating information for an existing deployment.

## Field Definition

The following tables describe the hierarchical data elements.

#### Deployment Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
TrackId| conditional| string| If the user wishes to update an existing
deployment. They are required to make a HTTP PUT request along with a TrackId
for an existing deployment. For a new deployment, the user uses a POST request
and should not submit a TrackId.  
DeploymentName| conditional| string| The name designated for the deployment.
Max Characters: 80. Required for Create call, optional for Update call.  
DeploymentDate| conditional| date| The tentative date the deployment will be
sent. Format: âyyyy-MM-dd HH:mmâ. The date must be in the future. Required
for Create call, optional for Update call.  
DeploymentTypeId| conditional| integer| The Deployment Type Identifier in the
Email Builder system. The [Brand Lookup API](../omedaclientkb/brand-lookup-
apis) can be used to retrieve valid values for this field. Required for Create
call, optional for Update call.  
OwnerUserId| required| string| The User ID of an active Email Builder account
to be designated as the âownerâ. The owner is generally the account that
will be working on the deployment throughout the creation and sending process.  
CampaignId| optional| string| An optional Campaign Id to assign to the
deployment. Max Characters: 100.  
Splits| conditional| integer| The number of splits the deployment has. Each
split will have its own designated email information such as html content,
text content, from name, mailbox, email subject, etc. Required for Create
call, optional for Update call.  
TrackOpens| required| byte| 1 = Track and store the opening of HTML emails. 0
= do not track and store the opening of HTML emails.  
TrackLinks| required| byte| 1 = Track and store link clicks on the deployment.
0 = do not track and store link clicks on the deployment.  
Testers| optional| array| An array of json objects. Each object contains
deployment tester information: First Name, Last Name, and Email Address.  
FinalApproverUserId| optional| string| The User ID of an active Email Builder
account to be designated as the âfinal approverâ. If final approver is not
specified, it will be defaulted to the OwnerUserID.  
Notes| optional| string| Optional user-specified notes regarding the
deployment.  
ReloadOnqQueryBeforeFinalDeployment| optional| byte| 1 = If deployment
audience is from an Audience Builder query, then re-execute the query before
final deployment time. 0 = do not re-execute Audience Builder query before
deployment time (if applicable).  
BillingCategoryCode| optional| string| Optional 8 characters billing category.
It must be already defined in Email Builder before it can be used in API call.  
UseContentRecommendation| optional| byte| 1 = Use Content Recommendations, 0 =
Do not use content recommendations. If it is not specified, it will be
defaulted to 0.  
ContentRecommendationBehaviorId| conditional| integer| The Behavior identifier
in the Omeda system. Required if UseContentRecommendation = 1  
UseImagesInRecommendation| conditional| byte| 1 = Use Images in Content
Recommendations, 0 = Do not use Images in Content Recommendations. Required if
UseContentRecommendation = 1  
NumberOfRecommendations| conditional| integer| Valid values are 1 â 10.
Required if UseContentRecommendation = 1  
  
#### Testers Elements

ReportRecipientoptionalinteger1 = Receives Automated Report Send. 0 = Does not
receive Automated Report Send. If not specified, it will be defaulted to 0.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
FirstName| required| string| The first name of the tester. Max Characters: 80.  
LastName| required| string| The last name of the tester. Max Characters: 80.  
EmailAddress| required| string| The email address of the tester. Max
Characters: 255.  
  
### POST JSON Request Example: When creating a new deployment

CODE

    
    
    {
        "DeploymentName": "Test Warmup - #1",
        "DeploymentDate": "2012-02-29 13:45",
        "DeploymentTypeId": 124,
        "CampaignId": "Campaign1",
        "OwnerUserId": "Email Builderuser1",
        "FinalApproverUserId": "Email Builderuser1",
        "Splits": 1,
        "TrackLinks": 1,
        "TrackOpens": 1,
        "Notes": "Don't send until Mar. 30th.",
        "ReloadOnqQueryBeforeFinalDeployment": 1,
        "BillingCategoryCode": "O1230001",
        "Testers": [
            {
                "FirstName": "John",
                "LastName": "Doe",
                "EmailAddress": "john@doe.com"
            },
            {
                "FirstName": "Jill",
                "LastName": "Doe",
                "EmailAddress": "jill@doe.com"
            }
        ]
    }
    

### POST JSON Request Example: When creating a new deployment with content
recommendations

CODE

    
    
    {
        "DeploymentName": "Test Warmup - #1",
        "DeploymentDate": "2024-02-29 13:45",
        "DeploymentTypeId": 124,
        "CampaignId": "Campaign1",
        "OwnerUserId": "Email Builderuser1",
        "FinalApproverUserId": "Email Builderuser1",
        "Splits": 1,
        "TrackLinks": 1,
        "TrackOpens": 1,
        "Notes": "Don't send until Mar. 30th.",
        "ReloadOnqQueryBeforeFinalDeployment": 1,
        "UseContentRecommendation": 1,
        "ContentRecommendationBehaviorId": 123,
        "UseImagesInRecommendation": 0,
        "NumberOfRecommendations": 3,
        "BillingCategoryCode": "O1230001",
        "Testers": [
            {
                "FirstName": "John",
                "LastName": "Doe",
                "EmailAddress": "john@doe.com"
            },
            {
                "FirstName": "Jill",
                "LastName": "Doe",
                "EmailAddress": "jill@doe.com"
            }
        ]
    }
    

### PUT JSON Request Example: When updating an existing deployment

CODE

    
    
    {
        "TrackId" : "FOO0200300112",
        "DeploymentName": "Test Warmup - #2",
        "DeploymentDate": "2024-02-29 03:00:00 PM",
        "DeploymentTypeId": 124,
        "CampaignId": "Campaign2",
        "OwnerUserId": "Email Builderuser1",
        "FinalApproverUserId": "Email Builderuser1",
        "Splits": 1,
        "TrackLinks": 1,
        "TrackOpens": 1,
        "Notes" : "Updated notes for deployment."
        "Testers": [
            {
                "FirstName": "John",
                "LastName": "Doe",
                "EmailAddress": "john@doe.com"
            }
        ]
    }
    

## Response Examples

Responses possible: a successful POST/PUT (200 OK Status) or a failed POST/PUT
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful POST Submission

A successful POST submission will create an Email Builder deployment shell
with the designated information from the request body. A successful POST
request will return a Url that can be used to retrieve deployment information
such as link tracking, delivery statistics, deployment status, history, etc.
(See [Deployment Lookup Resource](../omedaclientkb/email-deployment-lookup)).

### Successful PUT Submission

A successful PUT submission will update deployment information for an existing
deployment. The service reads the TrackId element from the request body, finds
the appropriate deployment, and updates fields according to request body data.
A successful PUT request will return a Url that can be used to retrieve
deployment information such as link tracking, delivery statistics, deployment
status, history, etc. (See [Deployment Lookup
Resource](../omedaclientkb/email-deployment-lookup)).

#### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
          "TrackId":"FOO0200300112",
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/Email Builder/deployment/lookup/FOO0200300112/*"
        }
      ]
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    'OwnerUserId' {ownerUserId} was not found.
    'FinalApproverUserId' {finalApproverUserId} was not found.
    'OwnerUserId' {ownerUserId} is not active.
    'FinalApproverUserId' {finalApproverUserId} is not active.
    '{RequiredFieldName}' is a required field.
    The Duplicate value '{emailAddress}' submitted for Testers array, field 'EmailAddress'. Tester emails must be unique.
    The The value '{splits}' for field 'Splits' cannot be greater than 1.
    The value '{trackLinks}' for field 'TrackLinks' must be 0 or 1.
    The value '{trackOpens}' for field 'TrackOpens' must be 0 or 1.
    'TrackId' is a required when updating an existing deployment.
    No deployment was found matching trackId '{trackId}'.
    Deployment '{trackId}' cannot be edited. Sent, Scheduled , Approved, or Cancelled deployments cannot be edited.
    OwnerUserId '{ownerUserId}' is not authorized to edit deployment '{trackId}'"
    'TrackId' is not a valid field when creating a deployment. It will be auto-generated.
    Invalid value '{deploymentDate}' for field 'DeploymentDate'. The date must be in the future.
    Invalid value '{deploymentDate}' for field 'DeploymentDate'. Date format yyyy-MM-dd HH:mm is required.
    Field 'DeploymentTypeId' is a required field.
    Invalid value '{deploymentTypeId}' submitted for field 'DeploymentTypeId'. The Deployment Type was not found.
    Invalid value '{deploymentTypeId}' submitted for field 'DeploymentTypeId'. The Deployment Type is not active.
    Deployment 'FOO09030021' has been edited from the Email Builder portal and is not eligible for API access. Last edited by Email BuilderAccount2 on 2012-02-04 22:15:00.
    Deployment 'FOO09030021'  was created within the Email Builder portal and is not eligible for API access.
    Unknown category code 'O1230001' submitted for field 'BillingCategoryCode'.
    The value '{UseContentRecommendation}' for field 'UseContentRecommendation' must be 0 or 1.
    OwnerUserId '{ownerUserId}' is not authorized to create deployments that Use Content Recommendations.
    'ContentRecommendationBehaviorId' is required when the value for 'UseContentRecommendation' is 1.
    The value '{ContentRecommendationBehaviorId}' for field 'ContentRecommendationBehaviorId' is not a valid Content Recommendation Behavior.
    'NumberOfRecommendations' is required when the value for 'UseContentRecommendation' is 1.
    The value '{NumberOfRecommendations}' for field 'NumberOfRecommendations' must be between 1 and 10.
    

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
          Error": "A 'TrackId' element is required when making a PUT request to update a deployment." 
        },
        {
          "Error": "'DeploymentName' is a required field."
        }
      ]
    }
    

## Notice

Deployments that have been created or modified from within the Email Builder
web portal can not be managed using the Email Builder API Suite.

**Table of Contents**

×

