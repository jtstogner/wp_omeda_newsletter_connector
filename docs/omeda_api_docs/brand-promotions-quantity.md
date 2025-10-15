# Content from: https://knowledgebase.omeda.com/omedaclientkb/brand-
promotions-quantity

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve information about a specified
quantity of Brandâs Promotions. Including the Promotion Products, Price
Group and Price Code Information.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/promotion/quantity/{quantity}/* 
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/promotion/quantity/{quantity}/* 
    

brandAbbreviation is the abbreviation for the brand quantity is quantity of
promo codes to be returned, they are the most recent

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup All Promotions

Retrieves all defined Promotions for the brand.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Brand Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The brand identifier.  
Description| Yes| String| The name of the brand.  
BrandAbbrev| Yes| String| The abbreviation for the brand (used in most web
service URLs).  
Promotion| Yes| List| List of Promotion Elements attached to this Brand.  
  
##### Promotion Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
PromoCode| Yes| String| This is the unique way of identifying a Promotion
Code.  
AlternateId| No| String| This is the Alternate Id used to refer to the
Promotion.  
Description| Yes| String| This is the descriptive name for the Promotion.  
PromoChannel| No| Integer|  
PromoType| No| Integer|  
DataSourceId| No| Integer|  
EffectiveDate| No| DateTime| The date and time the promotion starts to be
effective. yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
EndDate| No| DateTime| The date and time the promotion is no longer effective.
yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
QuantitySent| No| int|  
SalesChannel| No| String|  
StatusCode| Yes| int| Status of the current promotion.  
PromotionProduct| Yes| List| A list of PromotionProduct elements. These define
the Products that are attached to the Promotion.  
  
##### Promotion Product Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| int| This is the unique Identifier for the Promotion Product.  
ProductId| Yes| int| The Product Id of the product associated with the
Promotion.  
ParentProductId| No| int| This would the the Parent Product associated, if
any.  
ProductPriceGroup| No| List| A list of ProductPriceGroup elements. These
define any Price Groups that are attached to the Product for a Promotion.  
SKU| No| string| the id assigned to the single copy sale product used to
indicate the individual issue  
  
##### Promotion Price Group Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| long| The product price group identifier.  
EndDate| No| Datetime| The date and time the price group is no longer
effective. yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StartDate| No| Datetime| The date and time the price group starts to be
effective. yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| Yes| int| Status of the current price group.  
ProductPriceCode| No| List| A list of ProductPriceCode elements. These define
any Prices that are attached to the Price Group for a Product.  
  
##### Promotion Price Code Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| long| The product price code identifier.  
GeographicLocation| Yes| String| Geographic Location that is valid code for
the associated price code.  
Term| Yes| int| The term/length for the product associated with the price
group.  
Amount| Yes| BigDecimal| The price associated with the product for the term.  
Status| Yes| int| The current status of the price for the associated product.  
PriceChoiceId| No| int|  
PriceChoiceDescription| No| String|  
CountryCode| No| String| The Country for which the this price code is valid.  
RegionCode| No| String| The Region for which the this price code is valid.  
RequestedVersionType| No| String| The Requested Version Type for which the
this price code is valid.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Promotions are found, an HTTP 404 (not found)
response will be returned.  
  
#### Example Response

CODE

    
    
    {
       "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
       "Id":3000,
       "Description":"AppDev Today",
       "BrandAbbrev":"APPDEV",
       "Promotion":[
          {
             "Description":"Second Promotion",
             "EffectiveDate":"2012-10-01 00:00:00.0",
             "PromoCode":"Promo2",
             "QuantitySent":0,
             "PromoType":2,
             "PromoChannel":1,
             "PromotionProduct":[
                {
                   "Id":1,
                   "ProductId":10
                },
                {
                   "Id":2,
                   "ProductId":8,
                   "ProductPriceGroup":[
                      {
                         "StatusCode":5,
                         "Description":"Price Group",
                         "EndDate":"2013-10-30 07:43:29.853",
                         "StartDate":"2012-10-30 07:43:29.853",
                         "Id":44,
                         "ProductPriceCode":[
                            {
                               "Status":1,
                               "Amount":12.5,
                               "PriceChoiceId":1,
                               "GeographicLocation":3,
                               "ProductPriceCodeId":111,
                               "Term":1
                            },
                            {
                               "Status":1,
                               "Amount":11.5,
                               "PriceChoiceId":2,
                               "GeographicLocation":2,
                               "ProductPriceCodeId":311,
                               "Term":1
                            }
                         ]
                      }
                   ]
                }
             ]
          }
       ]
    } 

**Table of Contents**

×

