# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-approval-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Approval Lookup API provides the ability to retrieve the
approval queue information such as tests, users and comments.

## Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/approvals/{trackId}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/approvals/{trackId}/*
    

brandAbbreviationis the abbreviation for the brandtrackIdis the unique
identifier for the deployment being requested.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-approval-lookup) for more
details. If omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:GETSee [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Field Definition

The following table describes the hierarchical data elements.

### Approval Lookup Elements Returned

#### Base Lookup Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
ScheduledDate| conditional| date| This will give the scheduled date if the
deployment has been scheduled to send Example: â2015-04-24 15:10:11â
(Central Standard Time)  
RequestedDate| required| date| The preliminary date given when the Deployment
was created Example: â2015-04-23 15:10:11â (Central Standard Time)  
SplitCount| required| integer| The number of deployment splits. This will
correspond to the number of splits found in the Omail Message Content pleat.  
Status| required| string| The current status of the deployment. Valid values
are : âCancelledâ, âNewâ,
âSendingâ,âScheduledâ,âSentâ,âWaiting Reviewâ,âNot
Acceptedâ,âAcceptedâ,âSubmittedâ, and âApprovedâ.  
ApprovalDate| conditional| date| If the deployment has been approved, this
will be the date it was approved to send. Example: â2015-04-24 12:10:11â
(Central Standard Time)  
FinalApproverUserId| required| string| The Final Approver for the deployment.
This is specified when the deployment is created.  
Tests| required| array| The tests that have been sent the deployment. If 2
tests have been sent, the Tests array will have a size of 2. Please see Tests
Array Element below.  
  
#### Tests Array Element

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
TestNumber| required| integer| The first test sent is test 1, the second is
test 2, etc.  
SentDate| required| date| The date that the test was sent.  
SentStatus| required| string| The status of the test. Valid values are Ready,
Sending, or Sent.  
ApprovalStatusCode| required| string| The approval status code of the test,
description for each field is found in the ApprovalStatusDescription field
below. Valid values are âNEWâ, âWAITINGâ,
âAPPROVEDâ,âDISAPPROVEDâ, and FINAL_APPROVEDâ.  
ApprovalStatusDescription| required| string| The approval status description
of the test. Valid values are âNot Sentâ, âWaiting for responseâ,
âAll Testers Approvedâ,âRejectedâ, and âFinal Approver Approvedâ.  
TestSplits| required| array| Each deployment test can have one or many test
splits depending on the number of splits that the deployment base deployment
has. For example if the deployment has 3 splits, tests array object will
contain a TestSplits array of size 3. See TestSplits Array element below.  
  
#### TestSplits Array Element

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
SplitNumber| required| integer| The first split is split 1, the second is
split 2, etc.  
Id| required| integer| The test split id â unique identifier â also used
to lookup the html or text content of the deployment (See HtmlTestContentUrl
and TextTestContentUrl below).  
FromName| required| string| The âFromâ name that will be used for this
particular split.  
FromEmail| required| string| The Email Address that the deployment email will
be from.  
Subject| required| string| The deployment email subject for this test split.  
HtmlSpamScore| conditional| double| The SpammAssassin spam score for the html
content of this test split. Only available if the deployment has html content.  
TextSpamScore| conditional| double| The SpammAssassin spam score for the text
content of this test split. Only available if the deployment has a text
content.  
HtmlSpamReport| conditional| string| The SpammAssassin analysis of the
deployment html content. Only available if the deployment has a htmlcontent.  
TextSpamReport| conditional| string| The SpammAssassin analysis of the
deployment text content. Only available if the deployment has a text content.  
HtmlTestContentUrl| conditional| string| The Deployment Test Content Lookup
Api Url to lookup the html content of the split that was sent for this test.
Only available if the split has html content.Example:
<https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/html/20632822/*>
where 20632822 is the Id field returned above.  
TextTestContentUrl| conditional| string| The Deployment Test Content Lookup
Api Url to lookup the text content of the split that was sent for this test.
Only available if the split has text content.Example:
<https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/text/20632822/*>
where 20632822 is the Id field returned above.  
SplitStatus| required| string| Either âOPENâ or âCLOSEDâ. A test split
is CLOSED if the deployment has been scheduled to send or if a new test has
been sent. Otherwise the test split will be OPEN.  
SplitApprovalStatusCode| required| string| The approval status code of the
test split, description for each field is found in the
SplitApprovalStatusDescriptionfield below. Valid values are âNEWâ,
âWAITINGâ, âAPPROVEDâ,âDISAPPROVEDâ, and FINAL_APPROVEDâ.  
SplitApprovalStatusDescription| required| string| The approval status
description of the test split. Valid values are âNot Sentâ, âWaiting for
responseâ, âAll Testers Approvedâ,âRejectedâ, and âFinal Approver
Approvedâ.  
MessageType| required| string| Valid values are âTextâ, âHtmlâ, or
âBothâ.  
ApprovalDate| conditional| date| If the split has been approved â this field
will hold the date it was approved.  
Testers| required| array| Each Test Split element will have an array of
Testers. Each element in the testers array gives important information, such
as whether the test reached the tester successfully, the number of opens,
whether they approved the test, any comments they have on the test, etc.
Please see Testers Array Element below.  
  
#### Testers Array Element

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
Opens| required| integer| The number of times the tester has opened this
particular deployment test split. This tracking is only available for html
deployments.  
TesterDeliveryEmail| required| email| The email address that the test was sent
to.  
SentDate| conditional| date| If the test has been sent, this field will give
the date that the test was sent to this particular tester.  
TextTestApprovalStatusCode| conditional| string| The approval status code of
the tester. Valid values are âAPPROVEDâ, âDISAPPROVEDâ, âWAITINGâ.
Element is only available if there is text content in the deployment split.  
HtmlTestApprovalStatusCode| conditional| string| The approval status code of
the tester. Valid values are âAPPROVEDâ, âDISAPPROVEDâ, âWAITINGâ.
Element is only available if there is htmlcontent in the deployment split.  
TextTestResponseDate| conditional| date| The date the tester responded to the
test for this test split. This field is only available if the tester has
responded to this test split and the deployment split has text content.  
HtmlTestResponseDate| conditional| date| The date the tester responded to the
test for this test split. This field is only available if the tester has
responded to this test split and the deployment split has html content.  
TextDeliveryStatus| conditional| text| If there was a problem sending the text
version to the test recipient, this field will provide a short description.
This field is only available if there is an error delivering the test to the
recipients inbox and if the deployment split has text content.  
HtmlDeliveryStatus| conditional| text| If there was a problem sending the html
verion to the test recipient, this field will provide a short description.
This field is only available if there is an error delivering the test to the
recipients inbox and if the deployment split has htmlcontent.  
IsFinalApprover| required| boolean| Whether this particular tester is the
designated Final Approver of the deployment. Valid values are âtrueâ /
âfalseâ.  
FirstOpenDate| conditional| date| If the tester has opened the test email for
this test split, this field will give the first date that the test split email
was opened.  
LastOpenDate| conditional| date| If the tester has opened the test email for
this test split, this field will give the last date that the test split email
was opened.  
TesterName| required| string| The First and Last name of the tester.  
TesterComments| conditional| array| Each Testers Array element will have an
array of TesterComments if the tester has left a comment for a particular test
split. Please see TesterComments Array Element below.  
  
#### TesterComments Array Element

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
HtmlTestComment| conditional| string| The comment the tester made regarding
the html content of the split. Only available if the tester has left a comment
and the test split contains html content.  
HtmlTestCommentDate| conditional| date| The date the tester made the comment
regarding the html content of the split. Only available if the tester has left
a comment and the test split contains html content.  
TextTestComment| conditional| string| The comment the tester made regarding
the text content of the split. Only available if the tester has left a comment
and the test split contains text content.  
TextTestCommentDate| conditional| date| The date the tester made the comment
regarding the text content of the split. Only available if the tester has left
a comment and the test split contains text content.  
  
## Response Example

### JSON Example â 1 split â 1 test sent â James Smith did not approve
the text version and left comments â final approver has not responded to
tests

CODE

    
    
    {
      "OwnerUserId": "johndoe",
      "Tests": [{
        "SentDate": "2010-04-29 13:46:18",
        "ApprovalStatusCode": "WAITING",
        "SentStatus": "Sent",
        "TestSplits": [{
          "FromEmail": "postmaster@renewals.marketing.com",
          "Testers": [
            {
              "Opens": "3",
              "TesterDeliveryEmail": "james.smith@marketing.com",
              "SentDate": "2010-04-29 13:46:25",
              "TextTestApprovalStatusCode": "DISAPPROVED",
              "HtmlTestResponseDate": "2010-04-29 13:56:27",
              "HtmlTestApprovalStatusCode": "WAITING",
              "FirstOpenDate": "2010-04-29 13:47:12",
              "LastOpenDate": "2010-04-29 13:47:16",
              "TextTestResponseDate": "2010-04-29 13:55:28",
              "TesterName": "James Smith",
              "IsFinalApprover": false,
              "TesterComments": [
                {
                  "HtmlTestComment": "Profile Preference Link doesn't work",
                  "HtmlTestCommentDate": "2010-04-29 13:56:27"
                },
                {
                  "HtmlTestComment": "Renew Now link needs to have the reader number embedded to prepopulate the sub form.",
                  "HtmlTestCommentDate": "2010-04-29 13:56:06"
                },
                {
                  "TextTestComment": "Profile Preference link doesn't work.",
                  "TextTestCommentDate": "2010-04-29 13:55:28"
                },
                {
                  "TextTestComment": "The link for "if this email was forwarded to you...."doesn't work",
                  "TextTestCommentDate": "2010-04-29 13:55:10"
                },
                {
                  "TextTestComment": "Renew Now link doesn't go anywhere - need it to go to the sub page with prepopulated info.",
                  "TextTestCommentDate": "2010-04-29 13:54:14"
                }
              ]
            },
            {
              "Opens": "1",
              "TesterDeliveryEmail": "john.doe@marketing.com",
              "SentDate": "2010-04-29 13:46:22",
              "TextTestApprovalStatusCode": "WAITING",
              "HtmlTestApprovalStatusCode": "WAITING",
              "FirstOpenDate": "2010-04-29 13:59:03",
              "LastOpenDate": "2010-04-29 13:59:03",
              "TesterName": "John Doe",
              "IsFinalApprover": true
            }
          ],
          "Subject": "Marketing Renewal",
          "HtmlTestContentUrl": "https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/html/20632822/*",
          "TextTestContentUrl": "https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/text/20632822/*",
          "HtmlSpamScore": 2.69,
          "SplitApprovalStatusDescription": "Rejected",
          "SplitStatus": "OPEN",
          "TextSpamReport": "Content analysis details:   (2.4 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 2.4 URG_BIZ                BODY: Contains urgent matter ",
          "SplitNumber": 1,
          "HtmlSpamReport": "Content analysis details:   (2.7 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 2.4 URG_BIZ                BODY: Contains urgent mattern 0.2 HTML_IMAGE_RATIO_08    BODY: HTML has a low ratio of text to image arean 0.0 HTML_MESSAGE           BODY: HTML included in messagen 0.1 MIME_HTML_ONLY         BODY: Message only has text/html MIME parts ",
          "FromName": "Renewal Marketing",
          "SplitApprovalStatusCode": "DISAPPROVED",
          "TextSpamScore": 2.38,
          "Id": 16001633,
          "MessageType": "Both"
        }],
        "ApprovalStatusDescription": "Waiting For Response",
        "TestNumber": 1
      }],
      "TrackId": "OTB100429002",
      "DeploymentName": "Marketing Renewal Deployment 1",
      "RequestedDate": "2010-04-30 16:00:00",
      "SplitCount": 1,
      "FinalApproverUserId": "johndoe"
    }
    

### JSON Example â 1 split â 2 tests sent â Jan Smith found problems
with the text and html version for the first test â a second test was sent
and she approved the html and text version â John Doe approved the second
test

CODE

    
    
    {
      "OwnerUserId": "johndoe",
      "Tests": [
        {
          "SentDate": "2015-03-04 13:06:51",
          "ApprovalStatusCode": "WAITING",
          "SentStatus": "Sent",
          "TestSplits": [{
            "FromEmail": "renewals@marketing.com",
            "Testers": [
              {
                "Opens": "5",
                "TesterDeliveryEmail": "jansmith@marketing.com",
                "SentDate": "2015-03-04 13:06:59",
                "TextTestApprovalStatusCode": "DISAPPROVED",
                "HtmlTestResponseDate": "2015-03-04 13:31:50",
                "HtmlTestApprovalStatusCode": "DISAPPROVED",
                "FirstOpenDate": "2015-03-04 13:23:12",
                "LastOpenDate": "2015-03-04 13:33:53",
                "TextTestResponseDate": "2015-03-04 13:28:34",
                "TesterName": "Jan Smith",
                "IsFinalApprover": false,
                "TesterComments": [
                  {
                    "HtmlTestComment": "Please confirm if the link for "www.marketing.com" works. The TEXT and HTML versions both go to a 404 page, but when you view the newsletter in a web browser, the link is fine. The "www.marketing.com" link should direct to: http://www.marketing.com/landing/",
                    "HtmlTestCommentDate": "2015-03-04 13:31:50"
                  },
                  {
                    "TextTestComment": "Please confirm if the link for "www.marketing.com" works. The TEXT and HTML versions both go to a 404 page, but when you view the newsletter in a web browser, the link is fine. The "www.marketing.com" link should direct to: http://www.marketing.com/landingtext/",
                    "TextTestCommentDate": "2015-03-04 13:28:34"
                  },
                  {
                    "TextTestComment": "Please make sure that the text "New Subscriber" and "Existing Subscribers" are on two separate lines. The TEXT version has the two phrases running together.",
                    "TextTestCommentDate": "2015-03-04 13:27:10"
                  }
                ]
              },
              {
                "Opens": "2",
                "TesterDeliveryEmail": "john.doe@marketing.com",
                "SentDate": "2015-03-04 13:07:02",
                "TextTestApprovalStatusCode": "WAITING",
                "HtmlTestApprovalStatusCode": "WAITING",
                "FirstOpenDate": "2015-03-04 13:33:26",
                "LastOpenDate": "2015-03-04 13:54:46",
                "TesterName": "John Doe",
                "IsFinalApprover": true
              }
            ],
            "Subject": "Get your renewals today!",
            "HtmlSpamScore": 3.3,
            "SplitApprovalStatusDescription": "Waiting For Response",
            "SplitStatus": "OPEN",
            "TextSpamReport": "Content analysis details:   (1.1 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 1.1 HS_INDEX_PARAM         URI: Link contains a common tracker pattern. ",
            "SplitNumber": 1,
            "HtmlSpamReport": "Content analysis details:   (3.3 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 1.1 HS_INDEX_PARAM         URI: Link contains a common tracker pattern.n 2.1 HTML_IMAGE_RATIO_04    BODY: HTML has a low ratio of text to image arean 0.0 HTML_MESSAGE           BODY: HTML included in messagen 0.1 MIME_HTML_ONLY         BODY: Message only has text/html MIME parts ",
            "HtmlTestContentUrl": "https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/html/2063282/*",
            "TextTestContentUrl": "https://ows.omedastaging.com/webservices/rest/brand/OTB/omail/deployment/test/content/lookup/text/2063282/*",
            "FromName": "Marketing.com",
            "SplitApprovalStatusCode": "WAITING",
            "TextSpamScore": 1.11,
            "Id": 157376491622,
            "MessageType": "Both"
          }],
          "ApprovalStatusDescription": "Waiting For Response",
          "TestNumber": 1
        },
        {
          "SentDate": "2015-03-04 13:36:03",
          "ApprovalStatusCode": "FINAL_APPROVED",
          "SentStatus": "Sent",
          "TestSplits": [{
            "FromEmail": "renewals@marketing.com",
            "Testers": [
              {
                "Opens": "1",
                "TesterDeliveryEmail": "jansmith@marketing.com",
                "SentDate": "2015-03-04 13:36:10",
                "TextTestApprovalStatusCode": "APPROVED",
                "HtmlTestResponseDate": "2015-03-04 13:41:57",
                "HtmlTestApprovalStatusCode": "APPROVED",
                "FirstOpenDate": "2015-03-04 13:40:31",
                "LastOpenDate": "2015-03-04 13:40:31",
                "TextTestResponseDate": "2015-03-04 13:41:33",
                "TesterName": "Jan Smith",
                "IsFinalApprover": false,
                "TesterComments": [
                  {
                    "HtmlTestComment": "Again, links work now. Thanks for the prompt response!",
                    "HtmlTestCommentDate": "2015-03-04 13:41:57"
                  },
                  {
                    "TextTestComment": "Links work. Thank you!",
                    "TextTestCommentDate": "2015-03-04 13:41:33"
                  }
                ]
              },
              {
                "Opens": "2",
                "TesterDeliveryEmail": "john.doe@marketing.com",
                "SentDate": "2015-03-04 13:36:08",
                "TextTestApprovalStatusCode": "APPROVED",
                "HtmlTestApprovalStatusCode": "APPROVED",
                "TesterName": "John Doe",
                "IsFinalApprover": true
              }
            ],
            "Subject": "Connect with Icom at IWCE - Booth 621",
            "HtmlSpamScore": 3.3,
            "SplitApprovalStatusDescription": "Final Approver Approved",
            "SplitStatus": "OPEN",
            "TextSpamReport": "Content analysis details:   (1.1 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 1.1 HS_INDEX_PARAM         URI: Link contains a common tracker pattern. ",
            "SplitNumber": 1,
            "HtmlSpamReport": "Content analysis details:   (3.3 points)nn pts rule name              descriptionn---- ---------------------- --------------------------------------------------n 1.1 HS_INDEX_PARAM         URI: Link contains a common tracker pattern.n 2.1 HTML_IMAGE_RATIO_04    BODY: HTML has a low ratio of text to image arean 0.0 HTML_MESSAGE           BODY: HTML included in messagen 0.1 MIME_HTML_ONLY         BODY: Message only has text/html MIME parts ",
            "FromName": "marketing.com",
            "SplitApprovalStatusCode": "FINAL_APPROVED",
            "TextSpamScore": 1.11,
            "Id": 15737649212,
            "MessageType": "Both"
          }],
          "ApprovalStatusDescription": "Final Approver Approved",
          "TestNumber": 2
        }
      ],
      "TrackId": "OTB150454009",
      "DeploymentName": "Marketing Renewals March",
      "RequestedDate": "2015-03-06 09:00:00",
      "SplitCount": 1,
      "FinalApproverUserId": "johndoe"
    }
    

### Failed Submission

A failed GET submission may be due to several factors:

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

