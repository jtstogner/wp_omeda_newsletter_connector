# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-remove-audience

[**Knowledge Base Home**](../omedaclientkb/)

## Email Deployment Remove Audience

### Summary

The Deployment Remove Audience API provides the ability to remove a list that
is currently assigned to a deployment.

At the time the call is made, the system will first check that the list name
submitted in the request JSON is indeed currently assigned to the deployment.
If it is assigned, the service will unassign the list from the deployment.

If the list is not found assigned to the deployment, the service will return a
404 not found response.

Please keep the following in mind: If the first call to remove a list succeeds
the response will be 200. If the same call is made again, the response will be
a 404 error with a description that the deployment does not have an assigned
list (because you removed it in your previous call).

An HTTP POST request is used when remove a list that is assigned to a
deployment.

### Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/remove/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/remove/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

### Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-remove-audience) for more
details. If omitted, the default content type is application/json.

Optional header element:

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
UserId| required| string| UserId of the omail account authorized for this
deployment. This is generally the âOwnerUserIdâ specified in the
[Deployment Api](../omedaclientkb/email-deployment)  
TrackId| required| string| TrackId is the unique identifier for the
deployment.  
RecipientList| conditional| string| The name of the list or Omail Output to
remove from the deployment.  
QueryName| conditional| string| The name of the query to remove from the
deployment.  
SplitNumber| optional| integer| The split number the list to be removed is
assigned to.  
  
### Request Examples

#### POST JSON Request Example: When removing an audience list from a
deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "RecipientList": "customer_list_april_2012.csv",
        "SplitNumber": 1
    
    }
    

#### POST JSON Request Example: When removing an Omail Output from a
deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "RecipientList": "My Onq Query",
        "SplitNumber": 1
    
    }
    

#### POST JSON Request Example: When removing a query from a deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "QueryName": "My Onq Query",
        "SplitNumber": 1
    
    }
    

### Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST (400
Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

#### Successful Submission

A successful submission will remove the specified list assigned to the
specified split. The response object will contain the TrackId of the
deployment, a unique âSubmissionIdâ, and a url to call the [Deployment
Lookup API](../omedaclientkb/email-deployment-lookup) for this deployment.

##### JSON Example

CODE

    
    
    {
      "ResponseInfo":[
        {
          "TrackId":"FOO0200300112",
          "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
          "Url" : "https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/lookup/FOO0200300112/*"
        }
      ]
    }
    

#### Failed Submission

Potential errors:

CODE

    
    
    Deployment {trackId}  has no audience list assigned.
    Deployment {trackId}  has no audience list assigned. An audience list with file name '{audienceList}' was removed from this deployment on {date} by {userId}.
    Audience list {audienceList} is not assigned to this deployment. Currently assigned audience list is '{audienceList}' - assigned on {date} by {userId}.
    

A failed submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a TrackId
submitted is not found in our system or if the list you are trying to remove
is not found assigned to the deployment.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
appropriate HTTP Method (POST) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

##### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          Error": "'TrackId' is a required field." 
        },
        {
          "Error": "Audience list 'customerlist1.csv' is not assigned to this deployment."
        }
      ],
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F"
    	
    }

**Table of Contents**

×

