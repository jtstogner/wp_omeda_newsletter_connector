# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-optin-
queue

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The OptIn Queue API allows our client to OptIn their subscribers or customers
to their email deployments at the client, brand, and deployment type level.
All 3 OptIn levels can be submitted in one OptIn Queue API call.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/client/{clientAbbreviation}/optinfilterqueue/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/client/{clientAbbreviation}/optinfilterqueue/*
    

clientAbbreviation is the abbreviation for the client who is posting the data.

PLEASE NOTE: Unlike the majority of our APIs, the OptInFilterQueue takes in
the CLIENT name, not a BRAND name. Please consult us to receive the proper
client name.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-opt-in-out-lookup) for more
details. If omitted, the default content type is application/json.

## Supported Content Types

There are three content types supported. If omitted, the default content type
is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported: POST See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store data in the database.

### OptIns Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
DeploymentTypeId| Optional| Array| Array element containing one or multiple
**DeploymentTypeOptIn** elements (see below)  
  
#### DeploymentTypeOptIn Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
EmailAddress| required| string| The customerâs email address for which the
deployment type opt-in is requested.  
DeploymentTypeId| required| string| The deployment type for which the opt-in
is requested.  
DeleteOptOut| optional| integer| 0 or 1, 0 = No delete, 1 = Delete. We
**highly recommend** reading the [Additional
Information](../omedaclientkb/email-opt-in-out-lookup) section for details.  
Source| optional| string| Allows you to set the source of the opt-in. If
omitted, the default source is âOptin API 2.â  
PromoCode| optional| string| Allows you to set an optional promocode to tie to
the filter entry.  
  
### Deployment Type-Level OptIn

A Deployment Type-level OptIn signifies that the email address(es) submitted
will be Opted In to the active Deployment Types that is associated with the
given **x-omeda-appid**. Example:

CODE

    
    
    You're given an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Deployment Types you have submitted are: 23,25.
    

**RESULT** : A Deployment Type-level OptIn, in this case, will be done for the
2 Deployment Types.

CODE

    
    
    You're given an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Deployment Types you have submitted are: 23,25,46.
    Only Deployment Types 23 and 25 are associated with a Brand that is associated with the x-omeda-appid.
    

**RESULT** : A Deployment Type-level OptIn, in this case, will generate an
error because the Deployment Type 46 is not associated with a Brand that is
associated with the **x-omeda-appid**.

## Request Examples

In these examples, weâre submitting:

CODE

    
    
    A Deployment Type-level OptIn for Deployment Types 4194 and 4804 for email address test66@omeda.com, 
    and we want to delete all corresponding OptOuts for that email address (DeleteOptOut=1)
    
    A Deployment Type-level OptIn for Deployment Types 4807 for email address test77@omeda.com, 
    but do not delete all corresponding OptOuts for that email address (no DeleteOptOut)
    

### JSON Example

CODE

    
    
    {
       
       "DeploymentTypeOptIn":[
          {
             "EmailAddress":"test66@omeda.com",
             "DeploymentTypeId":[
                4194,
                4804
             ],
             "DeleteOptOut":1,
             "PromoCode":"test"
          },
          {
             "EmailAddress":"test77@omeda.com",
             "DeploymentTypeId":[
                4807
             ],
             "Source":"Company MNO"
          }
       ]
    }
    
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission may create OptIn entries. Messages may be
returned in some cases.

#### JSON Example

If all OptIns have been processed **Without** any messages returned, you will
get the following response:

CODE

    
    
    {
       "Submission":"1be32302-8cbc-4106-96db-e79e17de6490",
       "Success":"Your submission was successful"
    }
    

If you submit a request **Without** âDeleteOptOutâ:1, it may be possible
that zero, one or more OptIns have not been processed. A âMessageâ element
will describe the reason why the specific OptIn has not been processed. Please
read the [Additional Information](../omedaclientkb/email-opt-in-out-lookup)
section for more details. A typical response may be:

CODE

    
    
    {
       "Messages":[
          {
             "Message":"ClientOptIn submission: the DeleteOptOut element was not set to delete OptOuts. 
    Email address test66@omeda.com already has an OptOut and it was not Opted In for deployment type id 4194."
          }
       ],
       "Submission":"953f0dbb-bda8-4a73-b630-b6bc58380654",
       "Success":"Your submission was successful"
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
resource that is searched for is not found.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

**IMPORTANT** : If an error occurs, **NONE** of the OptIns submitted will be
processed. The errors array simply indicates the reason why the request was
rejected. Fixing the errors in the error array and resubmitting the request
should work, provided no new errors have been re-introduced. Please contact
your Omeda representative if you need assistance.

#### JSON Example

##### DeploymentTypeOptIn

For each DeploymentTypeId submitted, our system verifies that it belongs to a
BrandId, and that the BrandId is authorized to receive OptIns for the
**x-omeda-appid** submitted. If this occurs, you would get the error message
below.

CODE

    
    
    {
       "Submission":"31f8c71a-d2f8-47a6-9b58-fc56439ffe9a",
       "Errors":[
          {
             "Error":"DeploymentTypeOptIn submission (4194): the DeploymentTypeId is not authorized for OptIns for the AppId submitted. Email address test66@omeda.com was not opted in."
          },
          {
             "Error":"There were errors with your submission.  None of the OptIns submitted in your request were created."
          }
       ]
    }
    

## Additional Information

### DeleteOptOut Rules

By convention, we use âDeleteOptOutâ to determine whether an âINâ
submission will override an existing âOUTâ submission.

Rule| Action  
---|---  
DeleteOptOut not set to 1 or omitted| If an âOUTâ entry exists in the
database for the given submission, then the âINâ is not written for this
submission. The âOUTâ remains.  
DeleteOptOut = 1| If an âOUTâ entry exists for a submission, it will be
overwritten with the âINâ that is being submitted.  
  
**Table of Contents**

×

