# Content from: https://knowledgebase.omeda.com/omedaclientkb/brand-group-
lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the Information defined for a given
GroupId.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/group/{groupId}*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/group/{groupId}*
    

brandAbbreviation is the abbreviation for the brand groupId is the known group
id

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

## Lookup Group

Retrieves all information for the specified GroupId.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

### Field Definition

The following tables describe the hierarchical data elements present on the
response from the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Group Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
GroupTypeCode| Yes| Integer| The brand identifier.  
GroupName| Yes| String| The name of the brand.  
GroupCode| Yes| String| The abbreviation for the brand (used in most web
service URLs).  
GroupAdminUserName| Yes| String| The count of all customers that are
associated with the brand (regardless of status).  
PassPhrase| Yes| String| The count of all customers that are associated with
the brand (regardless of status).  
GroupAdminCode| No| Integer| A list of Demographic elements. These define the
customized information that is being collected about a customer for this
brand.  
MaxMembers| No| Integer| A list of Product elements. These specify the
products that can be associated with customers for this brand.  
ActiveMembers| No| Integer| A list of ContactType elements. These decode the
different forms of contact a customer can have.  
RemainingMembers| No| Integer| A list of DeploymentType elements. These decode
the opt-out codes that emails are sent out under.  
StatusCode| Yes| Integer| A list of DeploymentType elements. These decode the
opt-out codes that emails are sent out under.  
  
##### Group Products Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
GroupProductId| Yes| Integer| The OmedaProductId associated with the group.  
GroupProductPrice| Yes| String| The price set for this Product.  
EarningCode| Yes| String| ââ  
GroupExpireDate| Yes| Integer| The expire date set for this Product.  
  
##### Customers Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
OmedaCustomerId| Yes| Integer| This is the Omeda demographic value id, and the
value used for the **OmedaDemographicValue** attribute when utilizing the
[Save Customer and Order API](../omedaclientkb/save-customer-and-order).  
FirstName| No| String| The name of the demographic value.  
LastName| No| Integer| Type of demographic value. See [Demographic Value
Types](../omedaclientkb/brand-comprehensive-lookup-service#Additional-
Information) for the list of values and their descriptions  
EmailAddress| No| Integer| Order in which to display demographic items. If you
would like this order to be adjusted, please contact your Account
Representative.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Behaviors are found, an HTTP 404 (not found)
response will be returned.  
  
#### Example Response

CODE

    
    
    {
      "SubmissionId":"C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Behavior":[
        {
          "Id":41234, 
          "ActionId":3, 
          "Description":"Trade Show 2010 - Attended",
          "AlternateId":"TRADE_SHOW_2010_ATTENDED",
          "ProductId":76
        },       
        { 
          "Id":41235, 
          "ActionId":8, 
          "Description":"Trade Show 2010 - Onsite",
          "AlternateId":"TRADE_SHOW_2010_ONSITE",
          "ProductId":76
        },
        {
          "Id":41236, 
          "ActionId":10, 
          "Description":"Trade Show 2010 - Pre-Registered",
          "AlternateId":"TRADE_SHOW_2010_PRE_REGISTERED",
          "ProductId":76,
          "Category":[2,3,4]
        },
        { 
          "Id":41237, 
          "ActionId":11, 
          "Description":"Trade Show 2010 - No Show",
          "AlternateId":"TRADE_SHOW_2010_NO_SHOW",
          "ProductId":76,
          "Category":[2,3,4]
        }
      ]
    } 

**Table of Contents**

×

