# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-audience-
assignment-status

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The List Assignment Status API provides the ability to get the status of a
customer list that is currently being assigned from the Omail FTP Site to a
deployment. For more information on triggering the assignment process, please
see [Deployment Audience Service](../omedaclientkb/email-deployment-add-
audience).

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/status/{listId}/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/status/{listId}/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.listIdis the numeric list id returned from [Deployment Add
Audience](../omedaclientkb/email-audience-assignment-status).

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-audience-assignment-status) for more
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

The following tables describe the hierarchical data elements.

#### List Assignment Response Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
ListId| required| integer| Numeric identifier for the list.  
AssignmentStatusCode| required| string| The current status of the list as it
is being assigned. Valid values are:âLIST_REMOVEDâ, âQUEUEDâ,
âIN_PROCESSâ, âDONEâ, âWARNINGâ, and âERRORâ.  
TrackId| required| string| The unique identifier for the deployment that the
list belongs to.  
SplitNumber| required| integer| The deployment split that the list belongs to.  
RecipientList| conditional| string| The name of the list for the list id being
queried.  
RecipientCount| conditional| integer| If the **AssignmentStatus** object
returns **DONE** , this will return the number of Recipient records that were
attached to the Deployment.  
QueryName| conditional| string| The name of the Query that is being assigned.  
Message| conditional| string| Optional message. Example: âThere were 4
invalid recipient records; 4 of which contain invalid email addresses.â  
  
## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "ListId":100345,
      "TrackId":"FOO020030012",
      "AssignmentStatus":"IN_PROCESS",
      "SplitNumber": 1,
      "RecipientList": "subscribers.csv"
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    File not found for audience list id '{listId}'
    No deployment found for  '{listId}'
    Deployment '{trackId}' associated with this file was created within the Omail portal and is not eligible for API access.
    Deployment '{trackId}' assocated with this file has been edited from the Omail portal and is not eligible for API access. Last edited by {userName} on {changedDate}.
    List removed: Audience list '{fileName}' was removed by {userId} on {removalDate}.
    

A failed POST submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if the ListId
submitted in the url is not found in Omail.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (GET) for this request.  
409 Conflict| Typically, this error occurs when their was a problem during the
list assignment process associated with the content of the list. Example: File
contained no valid recipient records.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          Error": "'ListId' is a required field." 
        },
        {
          "Error": "ListId 10043 was not found."
        },
        {
          "Error": "File contained no valid recipient records."
        }
      ]
    }

**Table of Contents**

×

