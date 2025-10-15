# Content from: https://knowledgebase.omeda.com/omedaclientkb/deployment-type-
lookup-by-brand-api

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the defined deployment types of a
single brand. This service is useful for building your own data mapping
service when reading or writing from/to other Omeda services.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/deploymenttypes/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/deploymenttypes/*
    

brandAbbreviationis the abbreviation for the brand

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **application/json**. JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Deployment Type Lookup by Brand

Retrieves all deployment types of a single brand.

### Field Definition

The following tables describe the hierarchical data elements present on the
response from the API.

#### Brand Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The brand identifier.  
Description| Yes| String| The name of the brand.  
BrandAbbrev| Yes| String| The abbreviation for the brand (used in most web
service URLs).  
DeploymentTypes| Yes| List| A list of DeploymentType elements. These decode
the opt-out codes that emails are sent out under.  
  
##### DeploymentTypes Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Deployment type identifier.  
Name| Yes| String| The name of the deployment type.  
Description| Yes| String| The name of the deployment type.  
LongDescription| No| String| The text description of the deployment type.  
AlternateId| Yes| String| The clientâs associated value to Omedaâs
deployment type identifier.  
StatusCode| Yes| Byte| See [Deployment Type Status
Codes](../omedaclientkb/brand-comprehensive-lookup-service) for a list of
status codes and their associated values.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Brand Information is found, an HTTP 404 (not
found) response will be returned.  
  
#### Success

CODE

    
    
    { 
      "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Id":3000,
      "Description":"AppDev Today",
      "BrandAbbrev":"APPDEV",
        "DeploymentTypes":[
        {
          "Id":2344,
          "Description":"Framework Building",
          "AlternateId":"Frmwk Bldg",
          "StatusCode":1
        }
      ]
    }
           
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Brand 12345 was not found."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    Brand {brandId} was not found.
    

### Additional Information

#### DeploymentTypes â StatusCode

StatusCode| Description  
---|---  
0| Inactive  
1| Active  
  
**Table of Contents**

×

