# Content from: https://knowledgebase.omeda.com/omedaclientkb/product-lookup-
by-brand-api

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the defined products of a single
brand. This service is useful for building your own data mapping service when
reading or writing from/to other Omeda services.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/products/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/products/*
    

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
Products| Yes| List| A list of Product elements. These specify the products
that can be associated with customers for this brand.  
  
##### Products Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The product identifier.  
ProductType| Yes| Integer| âTypeâ of Product. See [Product
Types](../omedaclientkb/api-standard-constants-and-codes).  
Description| Yes| String| Name of the product.  
AlternateId| Yes| String| This is the Product ID that is used in Omedaâs V10
system.  
Frequency| No| String| Frequency of the product issues per year.  
FrequencyType| No| String| The possible frequency types (Daily â DY, Weekly
â WK, Monthly â MO, Yearly â YR, bi-weekly â BIW, bi-monthly â BIM,
Manual â MA)  
DeploymentTypeId| Yes| Integer| If the product is linked to a DeploymentType
(Omail), then this ID will be returned.  
MarketingClasses| No| List| A list of MarketingClasses elements. These
elements will only be returned if the [Product Types](../omedaclientkb/api-
standard-constants-and-codes) is 1 (Magazine) or 2 (Newsletter) or 7
(Website).  
Issues| No| List| A list of Issues elements. These elements will only be
returned if the [Product Types](../omedaclientkb/api-standard-constants-and-
codes) is 1 (Magazine).  
  
###### MarketingClasses Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Marketing class identifier.  
Description| Yes| String| Name of the marketing class.  
ShortDescription| Yes| String| A short name of the marketing class.  
ClassId| Yes| String| Marketing class identifier associated with legacy
products.  
StatusCode| Yes| Integer| See [Marketing Class
Statuses](../omedaclientkb/brand-comprehensive-lookup-service) for a list of
status codes and their associated values  
  
###### Issues Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Issue identifier.  
Description| Yes| String| A description for the Issue.  
IssueDate| Yes| Datetime| Date of the issue in yyyy-MM-dd format. Example:
2012-05-01.  
AlternateId| Yes| String| Omedaâs legacy Issue ID.  
StatusCode| Yes| Byte| See [Issue Status Codes](../omedaclientkb/brand-
comprehensive-lookup-service) for a list of status codes and their associated
values.  
  
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
          "Products":[
        { 
          "Id":1, 
          "Description":"AppDev Today Web Access", 
          "ProductType":3, 
          "MarketingClasses":[
            {
              "StatusCode":0,
              "ShortDescription":"Need More Info",
              "ClassId":"2",
              "Description":"Need More Information",
              "Id":24
            },
            {
              "StatusCode":1,
              "ShortDescription":"Frmwk",
              "ClassId":"3",
              "Description":"Framework",
              "Id":22
            }
          ],
          "Issues":[
            {
              "Id":478437,
              "Description":"March 2012 Issue",
              "IssueDate": "2012-05-01",
              "AlternateId": "201205",
              "StatusCode": 30
            },
            {
              "Id":478438,
              "Description":"April 2012 Issue",
              "IssueDate": "2012-04-01",
              "AlternateId": "201205",
              "StatusCode": 30
            }
          ]
        },
        { 
          "Id":2, 
          "Description":"AppDev Today Magazine", 
          "ProductType":1
        }
      ]
           
    

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

#### Issues â StatusCode

StatusCode| Description  
---|---  
0| Planned  
10| Open  
20| Locked  
30| Closed  
40| Current Supplement  
45| Current Supplement Closed  
50| In Progress  
55| In Progress Closed  
  
#### MarketingClasses â StatusCode

StatusCode| Description  
---|---  
0| Customers with this StatusCode may or may not receive the product.  
1| Customers with this StatusCode will receive the product.  
  
**Table of Contents**

×

