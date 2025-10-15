# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Lookup API provides the ability to retrieve deployment
information such as link tracking, delivery statistics, deployment status,
history, etc. via an HTTP GET request.

## Base Resource URI

CODE

    
    
    For Production, use: 
    https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/{trackId}/*
    
    For Testing, use: 
    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/{trackId}/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted. trackId is the unique identifier for the deployment being requested.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-deployment) for more details.
If omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is **application/json**. JSON
application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported: GET See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

GET method is used when retrieving deployment information

## Field Definition

The following tables describe the hierarchical data elements.

#### Lookup Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
SentCount| required| integer| Number of emails that have been successfully
sent to email inboxes.  
SentDate| condition| date| If the deployment has been sent, the date the
deployment was sent. Example: â2012-04-23 15:10:11â (Central Standard
Time)  
DeploymentName| required| string| The user-entered name of the deployment,
designated when the deployment was created.  
UniqueOpens| required| integer| The number of unique email accounts that
opened this deployment.  
SplitCount| required| integer| The number of splits for this deployment.  
BounceCount| required| integer| The number of emails that were not accepted
into recipient email inboxes after the 72-hour retry period.  
ApprovalDate| required| date| The date the deployment was approved for
scheduling. For Omail API generated deployments, this date will be the date
that the user made the [Deployment Schedule API](../omedaclientkb/email-
deployment-schedule) call. An example: â2012-04-23 08:58:55â (Central
Standard Time).  
UniqueClicks| required| integer| The number of unique email addresses that
clicked on this deployment.  
RetryCount| required| integer| The number of email addresses that are
currently in retry status. Emails that âbounceâ (are not accepted by the
recipients ISP) will be retried for a 72 hour period.  
Splits| required| array| Split information for the deployment. Each deployment
can have one or many splits, each of which has its own email information such
as Subject, From, Html content, and Text content. Please see âSplits
Elementâ table below.  
TotalOpens| required| integer| The total number of email inboxes that opened
the deployment  
OwnerUserId| required| string| The Omail account userId that is authorized to
edit the deployment.  
SendingCount| required| integer| The number of emails that are currently in
âsendingâ status. They are in the process of being delivered.  
FinalApproverUserId| required| date| The Final Approver for the deployment.
This is specified when the deployment is created.  
TrackLinks| required| string| true / false  
LinkTracking| optional| array| An array of objects that hold a list of the
links that were tracked with total click counts and unique click counts etc.
Please see the âLinkTracking Elementsâ below for a list of fields  
CampaignId| optional| string| The Campaign Id specified when the deployment
was created, an empty string if none was specified.  
ScheduledDate| required| date| The date the deployment has been scheduled to
send.  
RequestedDate| required| date| The date the deployment has been originally
requested to be sent  
TrackOpens| required| string| true/false  
Notes| optional| string| Optional user-specified text that is set when the
deployment is created.  
Status| required| string| The current status of the deployment. Valid values
are : âCancelledâ, âNewâ,
âSendingâ,âScheduledâ,âSentâ,âWaiting Reviewâ,âNot
Acceptedâ,âAcceptedâ,âSubmittedâ, and âApprovedâ.  
TotalClicks| required| integer| Total number of clicks registered for this
deployment.  
TrackId| required| string| The tracking number used to identify the
deployment.  
CreatedBy| required| string| The Omail account UserId or service that created
the deployment.  
CreatedDate| required| date| The date the deployment was created.  
RecipientCount| required| integer| The number of recipients being deployed to.  
IsFiltered| required| string| true/false  
ModificationHistory| required| array| An array of objects that hold
information regarding changes that have been made to the deployment.  
Testers| optional| array| An array of json objects. Each object contains
deployment tester information: First Name, Last Name, and Email Address.  
DeploymentTypeId| required| integer| The Deployment Type Identifier in the
Omail system. The [Cross Reference API](../omedaclientkb/brand-comprehensive-
lookup-service) can be used to see all available deployment types for a given
brand.  
DeploymentTypeDescription| required| string| The Deployment Type description
in the Omail system. The [Cross Reference API](../omedaclientkb/brand-
comprehensive-lookup-service) can be used to see all available deployment
types for a given brand.  
DeploymentDesignation| required| string| The deployment designation.  
ReloadOnqQueryBeforeFinalDeployment| required| boolean| Whether the deployment
is set to re-execute an assigned Audience Bilder query, when applicable.  
BillingCategoryCode| optional| string| 8 characters billing category if
assigned to deployment  
  
#### Splits Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
FromEmail| required| string| The âFromâ email address, specified when the
deployment content was created.  
TextSpamScore| required| double| The SpamAssassin spam score calculated for
the deployment text content. Example: â1.2â. Value will be 0.0 if no text
content is present.  
Subject| required| string| The subject of the email, specified when the
deployment content was created.  
FromName| required| string| The âFromâ name, specified when the deployment
content was created.  
RecipientList| conditional| string| The name of the recipient file used when
the deployment was created.  
QueryName| conditional| string| The name of the Audience Builder query used
for the deployment.  
OutputCriteria| conditional| string| The name of the Audience Builder output
criteria used when using an Audience Builder query for the deployment
audience, otherwise âDefaultâ.  
HtmlSpamScore| required| double| The SpamAssassin spam score calculated for
the deployment Html content. Example: â1.2â. Value will be 0.0 if no Html
content is present.  
Sequence| required| integer| The split number: 1, 2, etcâ¦ depending on how
many splits the deployment has.  
HtmlContentUrl| required| link| A url to retrieve the html content for the
given deployment. Format :
[http://ows.omeda.com/webservice/rest/brand/{brandAbbreviation}/omail/deployment/content/lookup/html/{trackId}/1/*](http://ows.omeda.com/webservice/rest/brand/%7BbrandAbbreviation%7D/omail/deployment/content/lookup/html/%7BtrackId%7D/1/*)
.  
TextContentUrl| required| link| A url to retrieve the text content for the
given deployment. Format :
[http://ows.omeda.com/webservice/rest/brand/{brandAbbreviation}/omail/deployment/content/lookup/text/{trackId}/1/*](http://ows.omeda.com/webservice/rest/brand/%7BbrandAbbreviation%7D/omail/deployment/content/lookup/text/%7BtrackId%7D/1/*)
.  
  
#### LinkTracking Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
Category| optional| string| The category of the link  
CategoryValue| optional| string| Description value for the link category  
Keywords| optional| array| String array of keywords for the link  
LinkName| optional| string| Name of the link  
LinkTag| optional| string| Tag name of the link  
LinkUrl| required| string| Url of the link  
MessageType| required| string| HTML or TEXT, indicating which message body
type the link belongs to  
Tracked| required| integer| 0 = not tracked, 1 = tracked  
WebTracking| required| integer| 0 = web tracking off, 1 = web tracking on  
UniqueClickCount| optional| integer| Number of total unique clicks for the
link  
ClickCount| optional| integer| Number of total clicks for the link  
  
#### ModificationHistory Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
ChangeDescription| required| string| The description of the change made.  
ChangedBy| required| string| The Omail account UserId that made the change.  
ChangedDate| required| date| The date the change was made.  
  
#### Testers Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
FirstName| required| string| The first name of the tester.  
LastName| required| string| The last name of the tester.  
EmailAddress| required| string| The email address of the tester.  
  
### GET JSON Request Example

CODE

    
    
    {
        "SentCount": 642,
        "SentDate": "2012-04-23 15:10:11",
        "UniqueOpens": 5,
        "DeploymentName": "FOO Deployment #3 - April",
        "SplitCount": 1,
        "BounceCount": 10,
        "ApprovalDate": "2012-04-23 14:58:55",
        "UniqueClicks": 1,
        "RetryCount": 128,
        "Splits": [
            {
                "FromEmail": "subscriber.net",
                "TextSpamScore": 0,
                "Subject": "Join Now through April 27",
                "FromName": "Greenbook.net",
                "RecipientList": "Comp actives 063011.csv",
                "HtmlSpamScore": 0.1,
                "SplitNumber": 1,
                "HtmlContentUrl": "http://ows.omeda.com/webservice/rest/brand/FOO/omail/deployment/content/lookup/html/FOO120423006/1/*",
                "TextContentUrl": "http://ows.omeda.com/webservice/rest/brand/FOO/omail/deployment/content/lookup/text/FOO120423006/1/*",
            }
        ],
        "ModificationHistory": [
            {
                "ChangeDescription": "Deployment created (new). Requested date/time is Wed Apr 14 14:00:00 CDT 2010",
                "ChangedBy": "omailAccount1",
                "ChangedDate": "2012-02-03 14:00:00"
            },
            {
                "ChangeDescription": "FinalApproverEmail changed from: '' to: 'omailAccount1'",
                "ChangedBy": "omailAccount1",
                "ChangedDate": "2012-02-03 14:10:00"
            },
            {
                "ChangeDescription": "split #1: message header and content changed",
                "ChangedBy": "omailAccount1",
                "ChangedDate": "2012-02-03 14:20:00"
            },
        ],
        "Testers": [
            {
                "FirstName": "John",
                "LastName": "Doe",
                "EmailAddress": "john@doe.com"
            }
        ],
        "LinkTracking": [
            {
                "ClickCount": 15,
                "LinkUrl": "http://omedastaging.wpengine.com",
                "UniqueClickCount": 7
            }
        ],
        "TotalOpens": 5,
        "OwnerUserId": "omailaccount1",
        "SendingCount": 2,
        "FinalApproverUserId": "omailaccount1",
        "TrackLinks": "true",
        "CampaignId": "",
        "ScheduledDate": "2012-04-23 15:10:00",
        "RequestedDate": "2012-04-23 14:00:00",
        "TrackOpens": "true",
        "Notes": "04/23/2012 - cloned by \"omailaccount1\"\n- see deployment \"FOO Deployment #2\"  trackID=FOO120406005 for additional notes",
        "Status": "Sending",
        "TotalClicks": 1,
        "TrackId": "FOO120423006",
        "CreatedBy": "omailaccount1",
        "CreatedDate": "2012-04-23 14:49:31.84",
        "RecipientCount": 782,
        "IsFiltered": "true",
        "DeploymentTypeId": 10019,
        "DeploymentTypeDescription": "Digital Newsletters",
        "DeploymentDesignation": "Newsletter",
        "ReloadOnqQueryBeforeFinalDeployment": "true",
        "BillingCategoryCode": "O1230005"
    }
    

### Failed Submission

A failed request will return a unique submissionId that can be used as needed.
A failed submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a TrackId in the
url is not found in our system.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
appropriate HTTP Method (GET) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          Error": "Could not find deployment matching track Id FOO0908832" 
        }
      ],
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F"
    }

**Table of Contents**

×

