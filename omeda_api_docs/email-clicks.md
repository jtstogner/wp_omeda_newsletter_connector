# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-clicks

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service retrieves Omail data related to clicks on links in emails using
various parameters.

## Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/click/search/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/click/search/*
    

brandAbbreviationis the abbreviation for the brand

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-clicks) for more details. If omitted,
the default content type is application/json.

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

### Search Elements Submitted â **Used for the POST request**

Element Name| Required?| Data Type| Description  
---|---|---|---  
DeploymentName| optional*| string| text match for deployment name. *required
if TrackId is not present  
TrackId| optional*| string| text match for deployment trackId. *required if
DeploymentName is not present  
StartDate| optional*| string| deployments have been clicked after this date.
Format: âyyyy-MM-dd HH:mmâ, Ex. 2012-01-03 09:30. *required if EndDate is
present  
EndDate| optional*| string| deployments have been clicked prior to this date.
Format: âyyyy-MM-dd HH:mmâ, Ex. 2012-02-03 21:30. *required if StartDate
is present  
  
### Deployment Click Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
DeploymentName| required| string| User-specified deployment name  
TrackId| required| string| Omail deployment tracking number.  
SentDate| required| string| Date that the deployment was sent  
Splits| required| string| JSON element containing one or more Split elements
(see below)  
  
### Split Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
Split| required| string| Split number  
SubjectLine| required| string| Email subject line for this split  
Links| required| string| JSON element containing one or more Link elements
(see below)  
  
### Link Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
TotalClicks| required| Integer| Sum of all of the NumberOfClicks returned in
the Clicks array (see Click Elements Returned below)  
LinkURL| required| string| The URL of the link that was clicked  
Clicks| required| string| JSON element containing one or more Click elements
(see Click Elements Returned below)  
TotalUnrealClicks| required| Integer| Sum of all of the NumberOfUnrealClicks
(bot clicks) returned in the unrealClicks array (see**** UnrealClick Elements
Returned below)  
UnrealClicks| required| string| JSON element containing one or more
unrealClick (bot clicks) elements (see UnrealClick Elements Returned****
below)  
  
### Click Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
NumberOfClicks| required| Integer| Number of the times that this customer
clicked the link  
ClickDate| required| string| Date and time which the customer clicked the link  
FirstName| required| string| first name  
LastName| required| string| last name  
CustomerId| required| string| Internal customer id (for use on certain
databases)  
EncryptedCustomerId| required| string| The Encrypted Customer Id for the
customer  
EmailAddress| required| string| Email address for which the click occurred  
Keyword| optional| string| Keyword for the link which was clicked  
Category| optional| string| Category for the link which was clicked  
CategoryValue| optional| string| Category value for the link which was clicked  
  
### UnrealClick Elements Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
UnrealClicks| required| string| JSON element containing one or more
UnrealClick (bot) Responses (see UnrealClick Reasons Returned below)  
FirstName| required| string| first name  
LastName| required| string| last name  
CustomerId| required| string| Internal customer id (for use on certain
databases)  
EncryptedCustomerId| required| string| The Encrypted Customer Id for the
customer  
EmailAddress| required| string| Email address for which the click occurred  
Keyword| optional| string| Keyword for the link which was clicked  
Category| optional| string| Category for the link which was clicked  
CategoryValue| optional| string| Category value for the link which was clicked  
  
### UnrealClick Reasons Returned

Element Name| Required ?| Data Type| Description  
---|---|---|---  
NumberOfUnrealClicks| required| Integer| Number of the times that this
customer (bot) clicked the link  
ClickDate| required| string| Date and time which the customer (bot) clicked
the link  
Reason| required| string| Code for unreal click reason (see UnrealClick Reason
Codes Legend below)  
  
#### UnrealClick Reason Codes Legend

Reason code| Reason Code Description| Which Clicks Are Negated?  
---|---|---  
1| Two clicks within 2000 milliseconds (2 seconds)| Offending Click  
2| More than 10 clicks within 30 seconds| All Clicks  
3| Percentage of fake clicks exceeds 50%| First Click where the Second Click
met Reason Code 1  
4| Number of unique source IP address exceeds 12| All Clicks  
5| Total number of clicks received exceeds 200| All Clicks  
6| Number of unique user agents exceeds 9| All Clicks  
7| User agent of click is within pre-defined list| Offending Click  
8| Source IP address of click is within Ignored IPs list
{tracking.agent_ip_range_ignore_list}| Offending Click  
9| Source IP address of click is within pre-defined list| Offending Click  
10| Link was clicked 5 seconds after send| Offending Click  
  
## Request Example #1

This example would search the Omail system for clicks for the deployment with
TrackId âOMP171010002Sâ which occurred between 2017-10-26 00:25 and
2017-10-28 23:59.

CODE

    
    
    {
        "TrackId": "OMP171010002S",
        "StartDate": "2017-10-26 00:25",
        "EndDate": "2017-10-28 23:59"
    }
    

## Request Example #2

This example would search the Omail system for clicks for the deployment with
name âRequal Attemptâ which occurred between 2017-10-26 00:25 and
2017-10-28 23:59.

CODE

    
    
    {
        "DeploymentName": "Requal Attempt",
        "StartDate": "2017-10-26 00:25",
        "EndDate": "2017-10-28 23:59"
    }
    

## Response Example

### JSON Example

CODE

    
    
    {
       "DeploymentName":"Verification Test",
       "splits":[
          {
             "SubjectLine":"Verification Tests",
             "links":[
                {
                   "TotalClicks":67,
                   "TotalUnrealClicks":5,
                   "LinkURL":"http://my.omeda.com",
                   "clicks":[
                      {
                         "EncryptedCustomerId":"",
                         "Category":"",
                         "NumberOfClicks":32,
                         "Keyword":"",
                         "FirstName":"Elist",
                         "CategoryValue":"",
                         "CustomerId":"",
                         "LastName":"Omeda",
                         "ClickDate":"2011-05-23 16:10",
                         "EmailAddress":"elist@omeda.com"
                      },
                      {
                         "EncryptedCustomerId":"",
                         "Category":"",
                         "NumberOfClicks":1,
                         "Keyword":"",
                         "FirstName":"Jeff",
                         "CategoryValue":"",
                         "CustomerId":"",
                         "LastName":"Bezos",
                         "ClickDate":"2011-05-23 16:14",
                         "EmailAddress":"jbezos@omeda.com"
                      }
                   ],
                   "unrealClicks":[
                      {
                         "EncryptedCustomerId":"",
                         "Category":"",
                         "Keyword":"",
                         "FirstName":"John",
                         "CategoryValue":"",
                         "CustomerId":"123",
                         "LastName":"Doe",
                         "UnrealClicks":[
                            {
                               "NumberOfUnrealClicks":1,
                               "ClickDate":"2011-05-23 16:10",
                               "Reason":1
                            },
                            {
                               "NumberOfUnrealClicks":2,
                               "ClickDate":"2011-05-23 16:10",
                               "Reason":3
                            }
                         ],
                         "EmailAddress":"jdoe@omeda.com"
                      },
                      {
                         "EncryptedCustomerId":"",
                         "Category":"",
                         "Keyword":"",
                         "FirstName":"Bill",
                         "CategoryValue":"",
                         "CustomerId":"",
                         "LastName":"Gates",
                         "UnrealClicks":[
                            {
                               "NumberOfUnrealClicks":1,
                               "ClickDate":"2011-05-23 16:10",
                               "Reason":1
                            },
                            {
                               "NumberOfUnrealClicks":1,
                               "ClickDate":"2011-05-23 16:10",
                               "Reason":3
                            }
                         ],
                         "EmailAddress":"bgates@omeda.com"
                      }
                   ]
                }
             ],
             "Split":"1"
          }
       ],
       "SentDate":"2011-05-23 00:00",
       "TrackId":"CGM110520002"
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

