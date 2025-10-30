# Content from: https://knowledgebase.omeda.com/omedaclientkb/form-submission-
api

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Form Submission API returns all transactions from a given form and
specified date range. Data including billing info, authentication, and gift
recipients will be excluded. This API will return results paginated, up to 200
transactions a page. You are able to specify the page number in the API (see
below).

## Base Resource URI

CODE

    
    
    For Production, use: 
    https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/formbuilder/registration/site/{site_context}/startdate/{mmddyyyy}/enddate/{mmddyyyy}/*
    or with page number: 
    https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/formbuilder/registration/site/{site_context}/startdate/{mmddyyyy}/enddate/{mmddyyyy}/page/{pagenumber}/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/formbuilder/registration/site/{site_context}/startdate/{mmddyyyy}/enddate/{mmddyyyy}/*
    or with page number:
    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/formbuilder/registration/site/{site_context}/startdate/{startdate}/enddate/{enddate}/page/{pagenumber}/*
    

brandAbbreviation is the abbreviation for the brand.  
site_context is the specific form for which we are requesting transactions
from. Example: <https://sample.dragonforms.com/> **omeda_new**  
startdate is the start of the date range of when transactions were made from
the specified form.  
enddate is the end of the date range of when transactions were made from the
specified form. This cannot exceed greater than 16 days from the startdate.

***** Note â formatting for either date value is MMddyyyy or MMddyyyy_HHmm
with the hours and minutes (on a 24 hour clock) being optional. For example,
January 2, 2023 1:23 PM would be formatted as 01022023_1323.  
If you use MMddyyyy **without** the hours and minutes, then enddate must be
prior to the current date. If using MMddyyyy_HHmm (with hours and minutes),
you can use current date as the enddate but with a time prior to current time.

## HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

## Content Type

The content type is **application/json**. JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:  
GET : See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Field Definition

The following table describes the data elements present on the response from
the API.

### Form Submission Return Elements

Attribute Name| Data Type| Description  
---|---|---  
FormTransactions| JSON Array| JSON element containing multiple**Form
Transaction Elements** elements (see below)  
PageSummary| String| Defines which page number of results are displayed out of
total number pages of results. Each page will display up to 200 transactions.  
SubmissionID| String| A unique identifier for the web services response. It
can be used to cross-reference the response in Omedaâs database.  
  
### Form Transactions Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
FormData| JSON Array| JSON element containing multiple**Form Data Elements**
elements (see below)  
  
### Form Data Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
Addresses| JSON Array| JSON element containing multiple**Addresses** elements
(see below)  
CustomerDemographics| JSON Array| JSON element containing multiple**Customer
Demographics** elements (see below)  
CustomerStatusId| Integer| Status of the customer record: 0=deleted, 1=active,
3=test.  
Emails| JSON Array| JSON element containing multiple**Emails** elements (see
below)  
Filters| JSON Array| JSON element containing multiple**Filters** elements (see
below)  
FirstName| String| First name of customer, up to 100 characters long.  
LastName| String| Last name of customer, up to 100 characters long.  
Products| JSON Array| JSON element containing multiple**Products** elements
(see below)  
SessionId| String| A unique identifier for the web services response. It can
be used to cross-reference the response in Omedaâs database.  
SiteId| Integer| A unique identifier for the specified form.  
Title| String| Job title, up to 100 characters long.  
FormSubmissionDate| Date| Date and time of when the form submission was made.
yyyy-mm-dd hh:mm:ss  
  
### Addresses Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
AddressProducts| Integer| Comma-separated list of product ids to associate
this address.  
ApartmentMailStop| String| Apartment, mail stop or suite number, up to 255
characters long.  
Company| String| Company name, up to 255 characters long.  
PostalCode| String| ZIP code or postal code.  
RegionCode| String| For country_code=âUSAâ or âCANâ, a 2-character US
state or Canadian code used by the postal service. Omeda also has region codes
for other countries of the world.  
Street| String| First line of street address, up to 255 characters long.  
  
### Customer Demographics Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
OmedaDemographicId| Integer| Identifier that specifies the explicit omeda
demographic ID.  
OmedaDemographicValue| Integer| The value id that is associated with the
**OmedaDemographicId** provided above selected by the customer.  
  
### Emails Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
EmailAddress| String| Email Address associated to the Form Data transaction.  
EmailProducts| String| Comma-separated list of product ids to associate this
email address.  
  
### Filters Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
DeploymentTypeOptIn| JSON Array| JSON element containing
multiple**DeploymentTypeOptIn** elements (see below)  
DeploymentTypeOptOut| JSON Array| JSON element containing
multiple**DeploymentTypeOptOut** elements (see below)  
  
### DeploymentTypeOptIn Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
DeploymentTypeId| String| The deployment type for which the opt-in is
requested.  
EmailAddress| String| The customerâs email address for which the deployment
type opt-in is requested.  
PromoCode| String| Promo Code tied to the opt-in requested.  
  
### DeploymentTypeOptOut Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
DeploymentTypeId| String| The deployment type for which the opt-out is
requested.  
EmailAddress| String| The customerâs email address for which the deployment
type opt-out is requested.  
PromoCode| String| Promo Code tied to the opt-out requested.  
  
### Product Return Elements

**Attribute Name**| **Data Type**| **Description**  
---|---|---  
OmedaProductId| Integer| Omeda product id for the product being requested.
Magazines (productType=1), Newsletters (productType=2), etc. See list of all
Product Types [here](../omedaclientkb/api-standard-constants-and-codes).  
Recieve| Integer| 1 = opt-in, 0 = opt-out. Assumed to be 1 if not given.
Explicitly allows this order service to capture opt-out behaviors as part of
the order transaction.  
RequestedVersion| String| Applicable only for products that have different
versions (âPâ for print, âDâ for digital, âBâ for both).  
  
## Response

### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See Example Response below.  
400 Bad Request| This response will be returned when Start or End Dates are
invalid. Either future dates are inputted, End Date is prior to Start Date, or
the date range exceeds more than 2 days.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| This response occurs when the form submitted was not found.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
### Successful Submission

A successful submission will return Form Submission data from the given form
and date range.

#### JSON Example

CODE

    
    
    {
       "FormTransactions":[
          {
             "FormData":[
                {
                   "Addresses":[
                      {
                         "Company":"Omeda",
                         "RegionCode":"IL",
                         "AddressProducts":"978",
                         "Street":"1 N Dearborn",
                         "PostalCode":"60602",
                         "City":"Chicago",
                         "CountryCode":"USA"
                      }
                   ],
                   "CustomerDemographics":[
                      {
                         "OmedaDemographicValue":[
                            "5011003"
                         ],
                         "OmedaDemographicId":"5010102"
                      },
                      {
                         "OmedaDemographicValue":[
                            "5010888"
                         ],
                         "OmedaDemographicId":"5010098"
                      },
                      {
                         "OmedaDemographicValue":[
                            "5214094"
                         ],
                         "OmedaDemographicId":"5210743"
                      }
                   ],
                   "Products":[
                      {
                         "Receive":1,
                         "Quantity":1,
                         "OmedaProductId":978
                      }
                   ],
                   "SiteId":5011,
                   "Filters":[
                      {
                         "DeploymentTypeOptIn":[
                            {
                               "DeploymentTypeId":[
                                  4609
                               ],
                               "PromoCode":"iphone",
                               "EmailAddress":"o.user@test.com"
                            }
                         ]
                      },
                      {
                         "DeploymentTypeOptOut":[
                            {
                               "DeploymentTypeId":[
                                  4609
                               ],
                               "PromoCode":"iphone",
                               "EmailAddress":"o.user@test.com"
                            }
                         ]
                      }
                   ],
                   "PromoCode":"iphone",
                   "Phones":[
                      {
                         "Number":"8475324600"
                      }
                   ],
                   "FirstName":"Jane",
                   "CustomerStatusId":1,
                   "LastName":"Doe",
                   "Emails":[
                      {
                         "EmailProducts":"978",
                         "EmailAddress":"o.user@test.com"
                      }
                   ],
                   "SessionId":"A53B18625ABC73E5E1EDE8E000D446B0"
                }
             ]
          }
       ],
       "SubmissionId":"A59354C1-D9C8-4DD9-9783-87C06C1D4007",
       "PageSummary": [
            {
                "TotalPagesAvailable": 1,
                "CurrentPageNumber": 1
            }
    ]
    }

**Table of Contents**

×

