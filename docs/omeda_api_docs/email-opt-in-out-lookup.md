# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-opt-in-
out-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service returns Opt In/Out information stored for a given customer.

## Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/filter/email/{emailAddress}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/filter/email/{emailAddress}/*
    

brandAbbreviation is the abbreviation for the brand emailAddress is the email
address you are searching for

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

There is one HTTP method supported: GET See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Field Definition

The following tables describe the hierarchical data elements.

### ResponseInfo Elements

Attribute Name| Data Type| Description  
---|---|---  
Filters| Array| Array element containing one or multiple **Filter** elements
(see below)  
EmailAddress| String| The email address submitted in the request.  
Submission| String| Unique id for your request.  
  
#### Filters Elements

Element Name| Data Type| Description  
---|---|---  
Source| String| How the filter was inserted into our system.  
DeploymentTypeId| Integer| The id for which the deployment is opted in or
opted out.  
Status| String| Whether the customer is opted in or opted out. IN=Opted In,
OUT=Opted Out  
Brand| String| The Brand for which the deployment type id belongs to.  
CreatedDate| String| The date and time the filter was created.  
ChangedDate| String| The date and time the filter was last updated.  
DesignationTypeId| Integer| Id of the Designation Type.  
DeploymentTypeName| String| Name of the Deployment Type.  
  
## Response Examples

The possible HTTP response codes are as follows: 200 success 400 bad request
404 nothing found 500 internal server error

### Successful Response (HTTP 200)

In these examples, the email address
[**test4@omeda.com**](mailto:test4@omeda.com) is opted in deployment types
42432 and 480643, and it is opted out of deployment type 528143.

#### JSON Example

CODE

    
    
    {
       "Filters":[
          {
             "Source":"Optin API 2",
             "DeploymentTypeId":42432,
             "DeploymentTypeName":"Acme Products"
             "DesignationTypeId":1
             "Status":"IN",
             "Brand":"XXM",
             "CreatedDate":"2010-12-15 11:10:05 CST",
             "ChangedDate":"2010-12-15 11:10:05 CST"
          },
          {
             "Source":"Optin API 2",
             "DeploymentTypeId":480643,
             "DeploymentTypeName":"Acme Products"
             "DesignationTypeId":1
             "Status":"IN",
             "Brand":"XXZ",
             "CreatedDate":"2010-12-15 11:10:05 CST",
             "ChangedDate":"2010-12-15 11:10:05 CST"
          },
          {
             "Source":"Optin API 2",
             "DeploymentTypeId":528143,
             "DeploymentTypeName":"Acme Products"
             "DesignationTypeId":1
             "Status":"OUT",
             "Brand":"XXP",
             "CreatedDate":"2010-12-15 11:10:05 CST",
             "ChangedDate":"2010-12-15 11:10:05 CST"
          }
       ],
       "EmailAddress":"test4@omeda.com",
       "Submission":"7fe70124-14c9-4210-8dbc-e2beac44a203"
    }
    

### Error Response (HTTP 404)

In these examples, no Opt-In nor Opt-Outs are found for the email address
[**test4@omeda.com**](mailto:test4@omeda.com)

#### JSON Example

CODE

    
    
    {
       "Submission":"C961641F-EA94-4DAA-80E4-6B44F13DA8BE",
       "Errors":[
          {
             "Error":"There are no opt-ins/opt-outs for test4@aomeda.com"
          }
       ]
    }

**Table of Contents**

×

