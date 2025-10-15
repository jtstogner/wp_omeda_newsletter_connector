# Content from: https://knowledgebase.omeda.com/omedaclientkb/brand-
comprehensive-lookup-service

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve information about a single brand,
including its defined products, demographics, deployment types, and other
cross referencing information. This service is useful for building your own
data mapping service when reading or writing from/to other Omeda services.
Results from this API should be cached and then refreshed at an interval by
the user. This API is not intended to be hit real time by web traffic or
otherwise hit with a great frequency.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/comp/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/comp/*
    

brandAbbreviationis the abbreviation for the brand

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Comprehensive Lookup by Brand

Retrieves all information about a single brand.

### Field Definition

The following tables describe the hierarchical data elements present on the
response from the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Brand Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The brand identifier.  
Description| Yes| String| The name of the brand.  
BrandAbbrev| Yes| String| The abbreviation for the brand (used in most web
service URLs).  
CustomerCount| Yes| Integer| The count of all customers that are associated
with the brand (regardless of status).  
Demographics| Yes| List| A list of Demographic elements. These define the
customized information that is being collected about a customer for this
brand.  
Products| Yes| List| A list of Product elements. These specify the products
that can be associated with customers for this brand.  
ContactTypes| Yes| List| A list of ContactType elements. These decode the
different forms of contact a customer can have.  
DeploymentTypes| Yes| List| A list of DeploymentType elements. These decode
the opt-out codes that emails are sent out under.  
  
##### Demographics Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| This is the Omeda demographic id, and is the value used for
the **OmedaDemographicId** attribute when utilizing the [Save Customer and
Order API](../omedaclientkb/save-customer-and-order).  
DemographicType| Yes| Integer| Type of demographic. See [Demographic
Types](../omedaclientkb/brand-comprehensive-lookup-service#Additional-
Information) for the list of values and their descriptions  
Description| Yes| String| The name of the demographic, and the value used for
the **ClientDemographicId** attribute when utilizing the [Save Customer and
Order API](../omedaclientkb/save-customer-and-order).  
DemoLegacyId| Yes| String| This is the Demographic ID that is used in the
Omedaâs V10 system.  
DemographicValues| Yes| List| a list of DemographicValue elements. These
define the values associated with the customized demographic information that
is being collected about a customer  
AuditedProducts| No| List| This is a list of Product Ids that the demo is
audited for. If the demo is not audited for any product, this will not be
returned.  
OmedaWebformText| Yes| String| Omeda INTERNAL use only  
OmedaWebformViewCode| Yes| Integer| Omeda INTERNAL use only. See [View
Codes](../omedaclientkb/brand-comprehensive-lookup-service#Additional-
Information) for the list of values and their descriptions  
OmedaWebformSequence| Yes| Integer| Omeda INTERNAL use only  
  
###### DemographicValues Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| This is the Omeda demographic value id, and the value used
for the **OmedaDemographicValue** attribute when utilizing the [Save Customer
and Order API](../omedaclientkb/save-customer-and-order).  
Description| Yes| String| The name of the demographic value.  
ShortDescription| Yes| String| The short name of the demographic value.  
DemographicValueType| Yes| Integer| Type of demographic value. See
[Demographic Value Types](../omedaclientkb/brand-comprehensive-lookup-
service#Additional-Information) for the list of values and their descriptions  
AlternateId| Yes| String| The clientâs associated value to Omedaâs
demographic value, and the value used for the **ClientDemographicValue**
attribute when using the [Save Customer and Order API](../omedaclientkb/save-
customer-and-order).  
Sequence| Yes| Integer| Order in which to display demographic items. If you
would like this order to be adjusted, please contact your Account
Representative.  
OmedaWebformText| Yes| String| Omeda INTERNAL use only  
OmedaWebformViewCode| Yes| Integer| Omeda INTERNAL use only. See [View
Codes](../omedaclientkb/api-standard-constants-and-codes) for the list of
values and their descriptions  
OmedaWebformSequence| Yes| Integer| Omeda INTERNAL use only  
  
##### Products Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The product identifier.  
Description| Yes| String| Name of the product.  
ProductType| Yes| Integer| âTypeâ of Product. See [Product
Types](../omedaclientkb/api-standard-constants-and-codes).  
AlternateId| Yes| String| This is the Product ID that is used in Omedaâs V10
system.  
Frequency| No| Integer| Frequency of the product issues per year.  
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
  
##### ContactTypes Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| Contact type identifier.  
Description| Yes| String| Name of the contact type. See [Contact
Types](../omedaclientkb/api-standard-constants-and-codes) for detailed list of
contact type descriptions.  
  
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
      "CustomerCount":4,
      "Demographics":[
        {
          "Id":1,
          "DemographicType":1,
          "Description":"Job Title",
          "AuditedProducts":[
             101,
             102,
             103
          ],
          "DemographicValues":[
            {
              "Id":1, 
              "Description":"Owner, President, CEO", 
              "ShortDescription":"Owner",
              "AlternateId":"02"
            },       
            { 
              "Id":2, 
              "Description":"CIO, CFO, CXO", 
              "ShortDescription":"CIO, CFO, CXO",
              "AlternateId":"04"
            },
            {
              "Id":3, 
              "Description":"Vice President", 
              "ShortDescription":"VPs",
              "AlternateId":"05"
            },
            { 
              "Id":4, 
              "Description":"Management", 
              "ShortDescription":"Management",
              "AlternateId":"09"
            }
          ]
        },
        {
          "Id":2,
          "DemographicType":2,
          "Description":"Technology",
          "DemographicValues":[
            {
              "Id":11, 
              "Description":"Java", 
              "ShortDescription":"Java",
              "AlternateId":"23"
            },
            { 
              "Id":12, 
              "Description":"C#", 
              "ShortDescription":"C#",
              "AlternateId":"34"
            },
            { 
              "Id":13, 
              "Description":"C, C++", 
              "ShortDescription":"C, C++",
              "AlternateId":"35"
            }
          ]
        }
      ],
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
      ],
      "ContactTypes":[ 
        { 
          "Id":100, 
          "Description":"Business Address" 
        },
        { 
          "Id":200, 
          "Description":"Business Phone" 
        },
        { 
          "Id":300, 
          "Description":"Primary Email" 
        },
        { 
          "Id":310, 
          "Description":"Secondary Email" 
        }
      ],
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

#### Demographics â DemographicType

StatusCode| Description  
---|---  
1| A single-choice-allowed type of question. (Two or more choices â one
answers)  
2| A multiple-choice-allowed type of question. (Two or more choices â many
answers)  
3| An open-form short answer question. (No choices â text input)  
5| A yes/no answer.  
6| A date answer.  
7| A whole number answer.  
8| A decimal answer.  
  
#### Demographics â DemographicValueType

StatusCode| Description  
---|---  
0| Standard Choice: Type indicating a single-choice-allowed type of question.
(Two or more choices â one answers).  
3| None-of-the-above Choice: None-of-the-above choice value type. A type of
standard choice whose selection may force some special validation.  
4| âOtherâ Choice: This value type will represent the âOtherâ option (
Open ended Coding ).  
  
#### MarketingClasses â StatusCode

StatusCode| Description  
---|---  
0| Customers with this StatusCode may or may not receive the product.  
1| Customers with this StatusCode will receive the product.  
  
#### DeploymentTypes â StatusCode

StatusCode| Description  
---|---  
0| Inactive  
1| Active  
  
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
  
#### Webforms â ViewCode

StatusCode| Description  
---|---  
0| Hidden  
1| Required  
2| Optional  
  
**Table of Contents**

×

