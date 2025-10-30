# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-schedule

[**Knowledge Base Home**](../omedaclientkb/)

## Email Deployment Schedule

### Summary

The Deployment Schedule API provides the ability to schedule a deployment for
sending. Once scheduled, a deployment is queued to send and will send at the
specified time. Before a deployment can be scheduled, the a test deployment
must be sent using the [Deployment Test](../omedaclientkb/email-deployment-
test). Once you schedule a deployment, you can [unschedule the
deployment](../omedaclientkb/email-deployment-unschedule) at any time before
the deployment is deployed to recipients. Accordingly, if you wish to change
the scheduled date for a currently scheduled deployment, you will need to
[unschedule the deployment](../omedaclientkb/email-deployment-unschedule) and
then make another Deployment Schedule Api call with the new date you prefer.

The Deployment Schedule Api also does a series of validation steps on the
deployment. This validation includes but is not limited to, ensuring that
merge variables are properly formatted, that the audience list being used has
fields that match merge variables in the deployment content, and that the
message content is valid.

An HTTP POST request is used when scheduling a deployment to send.

### Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/schedule/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/schedule/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

### Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-schedule) for more details.
If omitted, the default content type is application/json.

### Supported Content Types

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

POST method is used when assigning a list to a deployment split that does not
have an existing list attached.

### Field Definition

The following tables describe the hierarchical data elements.

##### List Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
UserId| required| string| UserId of the omail account authorized to schedule
this deployment. This must be the same account userId designated as either
âOwnerUserId or âFinalApproverUserIdâ during the [Deployment
Api](../omedaclientkb/email-deployment) call.  
TrackId| required| string| TrackId is the unique identifier for the
deployment.  
ScheduledDate| required| date| ScheduledDate is the date and time (Central
Daylight/Standard Time) that the deployment will start sending.Format:
âyyyy-MM-dd HH:mmâ, Ex. 2012-01-03 21:45 (January 3rd, 2012 at 9:45 PM).
Time is Central Daylight/Standard Time. Alternatively, you may pass in
â[NOW]â in your API call, and the deployment will be scheduled for the
time the API call is made.  
  
#### POST JSON Request Example: When scheduling a deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "ScheduledDate": "2012-01-03 21:45"
    
    }
    

#### POST JSON Request Example: When scheduling a deployment for immediate
preparation and deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "ScheduledDate": "[NOW]"
    
    }
    

### Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST (400
Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

#### Successful Submission

A successful submission will schedule the deployment for the specified date. A
successful POST request will return a Url that can be used to retrieve
deployment information such as link tracking, delivery statistics, deployment
status, history, etc. (See [Deployment Lookup
Resource](../omedaclientkb/email-deployment-lookup)). A successful request
will also return a unique submission Id that can be used as needed.

##### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "TrackId":"FOO0200300112",
          "Url" : "https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/FOO0200300112/*",
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E"
        }
      ]
    }
    

#### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    The field '{fieldName}' is required.
    The scheduled date must be in the future.
    Deployment '{trackId}' is currently scheduled for {dateScheduled}. You must first unschedule the deployment if you wish to re-schedule.
    Deployment '{trackId}' was sent on {sendDate}.
    Deployment '{trackId}' has been previously cancelled.
    There are no recipients specified for this deployment.
    Merge variable {mergeVariable} not found on a list {listName}.
    The e-mail message has been modified.
    No deployment was found matching trackId '{trackId}'.
    UserId '{userId}' is not authorized to schedule deployment '{trackId}'.
    Deployment '{trackId}' has been edited from the Omail portal and is not eligible for API access. Last edited by {account} on 2012-02-04 22:15:00.
    Deployment '{trackId}'  was created within the Omail portal and is not eligible for API access.
    There is no mailbox domain set up for this communication type.
    Audience list {listName} is not valid (recipient upload did not complete).
    There are testers or seed recipients that are missing values for the merge variables.
    Illegal characters in merge variable name: {mergeVariable}.
    Mismatched start/end merge variables delimiters in TEXT message on Split 1.
    Mismatched start/end merge variables delimiters in HTML message on Split 1.
    Mismatched start/end merge variables delimiters in message subject on Split 1.
    Mismatched start/end merge variables delimiters in From line on Split 1.
    {mergeVariableName} is missing from the audience list.
    

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

##### JSON Example

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          Error": "'TrackId' is a required field." 
        },
        {
          "Error": "User 'omailaccount1' is not authorized to edit this deployment."
        }
      ]
    }
    

##### JSON Example with missing merge variables

CODE

    
    
    {
        "SubmissionId": "5fe556c4-4bdf-4274-846b-4ac6fef8b8e4",
        "Errors": [
            {
                "Error": "job_function is missing from the audience list"
            },
            {
                "Error": "employee size is missing from the audience list"
            }
        ]
    }

**Table of Contents**

×

