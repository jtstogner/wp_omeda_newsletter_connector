# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-optout-
queue

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The OptOut Queue API allows our client to OptOut their subscribers or
customers to their email deployments at the client, brand, and deployment type
level. All 3 OptOut levels can be submitted in one OptOut Queue API call.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/client/{clientAbbreviation}/optoutfilterqueue/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/client/{clientAbbreviation}/optoutfilterqueue/*
    

clientAbbreviation is the abbreviation for the client who is posting the data.

PLEASE NOTE: Unlike the majority of our APIs, the OptOutFilterQueue takes in
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

The following tables describe the hierarchical data elements.

### OptOuts Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
ClientOptOut| Optional| Array| Array element containing one or multiple
**ClientOptOut** elements (see below)  
BrandOptOut| Optional| Array| Array element containing one or multiple
**BrandOptOut** elements (see below)  
DeploymentTypeOptOut| Optional| Array| Array element containing one or
multiple **DeploymentTypeOptOut** elements (see below)  
  
#### ClientOptOut Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
EmailAddress| required| string| The customerâs email address for which the
client opt-out is requested.  
Source| optional| string| Allows you to set the source of the opt-out. If
omitted, the default source is âOptout API 2.â  
  
#### BrandOptOut Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
EmailAddress| required| string| The customerâs email address for which the
brand opt-out is requested.  
BrandId| required| string| The brand for which the opt-out is requested.  
Source| optional| string| Allows you to set the source of the opt-out. If
omitted, the default source is âOptout API 2.â  
  
#### DeploymentTypeOptOut Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
EmailAddress| required| string| The customerâs email address for which the
deployment type opt-out is requested.  
DeploymentTypeId| required| string| The deployment type for which the opt-out
is requested.  
Source| optional| string| Allows you to set the source of the opt-out. If
omitted, the default source is âOptout API 2.â  
PromoCode| optional| string| Allows you to set an optional promocode to tie to
the filter entry.  
  
### Client-Level OptOut

A Client-level OptOut signifies that the email address(es) submitted will be
Opted Out to all of the Clientâs Brandsâ active Deployment Types that is
associated with the **x-omeda-appid**. Example:

CODE

    
    
    You submit an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Brands associated with the x-omeda-appid are: XXP,XXD, and XXQ.
    The Deployment Types for Brand XXP are: 23,25,26.
    The Deployment Types for Brand XXD are: 27,28.
    The Deployment Types for Brand XXQ are: 33,34,36.
    

**RESULT** : A Client-level OptOut, in this case, will be done for all 8
Deployment Types.

### Brand-Level OptOut

A Brand-level OptOut signifies that the email address(es) submitted will be
Opted Out to all of the Brandâs active Deployment Types that is associated
with the given **x-omeda-appid**. Example:

CODE

    
    
    You submit an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Brand you have submitted is XXP.
    The Deployment Types for Brand XXP are: 23,25,26.
    

**RESULT** : A Brand-level OptOut, in this case, will be done for all 3
Deployment Types.

CODE

    
    
    You submit an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Brands you have submitted are XXP and XXZ.
    Only Brand XXP is associated with the submitted x-omeda-appid.
    The Deployment Types for Brand XXP are: 23,25,26.
    The Deployment Types for Brand XXZ are: 43,45,46.
    

**RESULT** : A Brand-level OptOut, in this case, will generate an error
because Brand XXZ is **NOT** associated with the **x-omeda-appid**.

### Deployment Type-Level OptOut

A Deployment Type-level OptOut signifies that the email address(es) submitted
will be Opted out to the active Deployment Types that is associated with the
given **x-omeda-appid**. Example:

CODE

    
    
    You're given an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Deployment Types you have submitted are: 23,25.
    

**RESULT** : A Deployment Type-level OptOut, in this case, will be done for
the 2 Deployment Types.

CODE

    
    
    You're given an x-omeda-appid of 11111111-1111-1111-1111-111111111111.
    The Deployment Types you have submitted are: 23,25,46.
    Only Deployment Types 23 and 25 are associated with a Brand that is associated with the x-omeda-appid.
    

**RESULT** : A Deployment Type-level OptOut, in this case, will generate an
error because the Deployment Type 46 is not associated with a Brand that is
associated with the **x-omeda-appid**.

## Request Examples

In these examples, weâre submitting:

CODE

    
    
    A Client-level OptOut for email addresses test4@omeda.com and test5@omeda.com
    
    A Brand-level OptOut for Brands XXG, XXH, XXL and XXM for email address test@omeda.com
    
    A Brand-level OptOut for Brands XXH and XXL for email address test2@omeda.com
    
    A Deployment Type-level OptOut for Deployment Types 4194 and 4804 for email address test66@omeda.com
    
    A Deployment Type-level OptOut for Deployment Types 4807 for email address test77@omeda.com
    

### JSON Example

CODE

    
    
    {
       "ClientOptOut":[
          {
             "EmailAddress":"test4@omeda.com"
          },
          {
             "EmailAddress":"test5@omeda.com",
             "Source":"Company FGH"
          }
       ],
       "BrandOptOut":[
          {
             "EmailAddress":"test@omeda.com",
             "BrandId":[
                "XXG",
                "XXH",
                "XXL",
                "XXM"
             ],
             "Source":"Company STU"
          },
          {
             "EmailAddress":"test2@omeda.com",
             "BrandId":[
                "XXH",
                "XXL"
             ]
          }
       ],
       "DeploymentTypeOptOut":[
          {
             "EmailAddress":"test66@omeda.com",
             "DeploymentTypeId":[
                4194,
                4804
             ],
             "PromoCode":"test"
          },
          {
             "EmailAddress":"test77@omeda.com",
             "DeploymentTypeId":[
                4807
             ],
             "Source":"Company WPO"
          }
       ]
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will create OptOut entries.

#### JSON Example

CODE

    
    
    {
       "SubmissionId":"762eae76-783f-4f97-b7fc-b884efc37bcc",
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

**IMPORTANT** : If an error occurs, **NONE** of the OptOuts submitted will be
processed. The errors array simply indicates the reason why the request was
rejected. Fixing the errors in the error array and resubmitting the request
should work, provided no new errors have been re-introduced. Please contact
your Omeda representative if you need assistance.

#### JSON Example

##### ClientOptOut

If no Brand is found for the x-omeda-appid submitted, you would get the error
message below.

CODE

    
    
    {
       "Submission":"af9b2e78-4bb5-415a-83d2-cb786023c291",
       "Errors":[
          {
             "Error":"There are currently no valid brands associated for the client submitted."
          },
          {
             "Error":"There were errors with your submission.  None of the OptOuts submitted in your request were created."
          }
       ]
    }
    

##### BrandOptOut

For each BrandId submitted, our system verifies that the BrandId is authorized
to receive OptOuts for the **x-omeda-appid** submitted. If a BrandId is not
authorized, you would get the error message below.

CODE

    
    
    {
       "Submission":"7ece5296-010c-4bcd-94f5-e5073b73c90c",
       "Errors":[
          {
             "Error":"BrandOptOut submission (XXG): the BrandId is not authorized for OptOuts for the AppId submitted. Email address test@omeda.com was not opted out."
          },
          {
             "Error":"BrandOptOut submission (XXH): the BrandId is not authorized for OptOuts for the AppId submitted. Email address test@omeda.com was not opted out."
          },
          {
             "Error":"BrandOptOut submission (XXL): the BrandId is not authorized for OptOuts for the AppId submitted. Email address test@omeda.com was not opted out."
          },
          {
             "Error":"BrandOptOut submission (XXM): the BrandId is not authorized for OptOuts for the AppId submitted. Email address test@omeda.com was not opted out."
          },
          {
             "Error":"There were errors with your submission.  None of the OptOuts submitted in your request were created."
          }
       ]
    }
    

##### DeploymentTypeOptOut

For each DeploymentTypeId submitted, our system verifies that it belongs to a
BrandId, and that the BrandId is authorized to receive OptOuts for the
**x-omeda-appid** submitted. If this occurs, you would get the error message
below.

CODE

    
    
    {
       "Submission":"52938fbb-f1c6-471b-99f0-e32f2f4902f9",
       "Errors":[
          {
             "Error":"DeploymentTypeOptOut submission (4194): the DeploymentTypeId is not authorized for OptOuts for the AppId submitted. Email address test66@omeda.com was not opted out."
          },
          {
             "Error":"DeploymentTypeOptOut submission (4804): the DeploymentTypeId is not authorized for OptOuts for the AppId submitted. Email address test66@omeda.com was not opted out."
          },
          {
             "Error":"DeploymentTypeOptOut submission (4807): the DeploymentTypeId is not authorized for OptOuts for the AppId submitted. Email address test77@omeda.com was not opted out."
          },
          {
             "Error":"There were errors with your submission.  None of the OptOuts submitted in your request were created."
          }
       ]
    }

**Table of Contents**

×

