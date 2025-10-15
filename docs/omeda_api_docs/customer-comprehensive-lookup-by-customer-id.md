# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-
comprehensive-lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve the comprehensive information about
a single customer using the **Customer Id.**

  * If the customer has been merged into another customer or deactivated an error message will be returned. (Please see failure section for more details)

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/comp/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/comp/*
    

brandAbbreviationis the brand identifier for your brand or sitecustomerIdis
the customer identifier of the user (encrypted customer id may also be used)

### HTTP Headers

The HTTP header must contain the following element:x-omeda-appida unique id
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

## Lookup Customer Comprehensive Information

Retrieves the comprehensive information about a single customer.

### Field Definition

The following table describes the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

##### Customer Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| No| Integer| internal id (for use on certain databases)  
ReaderId| No| Integer| reader id (for use on certain databases) typically
either the reader id or the id is returned, but not both.  
Salutation| No| String| âMrs.â, âMr.â, etc.  
FirstName| No| String| first name  
MiddleName| No| String| middle name  
LastName| No| String| last name  
Suffix| No| String| âJr.â, âSr.â, âIIIâ, etc.  
Title| No| String| job title  
Gender| No| String| âFâ for Female, âMâ for Male, âUâ for Unknown.  
ClientCustomerId (DEPRECATED)| No| String| Please see [Customer Lookup By
External ID](../omedaclientkb/customer-lookup-by-external-id) in order to
Lookup customer identity information with an External ID used by client or an
Omeda legacy ID.  
OriginalPromoCode| No| String| Original âPromo Codeâ that was used to
create this customer.  
PromoCode| No| String| âPromo Codeâ last used to create/update this
customer.  
SignUpDate| No| DateTime| Date & time customer âsigned upâ as customer.
yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
ChangedDate| No| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
GlobalMinChangedDate| No| DateTime| Lowest âChangedDateâ found for the
entire Customer record. Logic will search âAddressesâ,
âEmailAddressesâ, âPhoneNumbersâ, âCustomerDemographicsâ and
âSubscriptionsâ and return the least recent date it finds. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
GlobalMaxChangedDate| No| DateTime| Highest âChangedDateâ found for the
entire Customer record. Logic will search âAddressesâ,
âEmailAddressesâ, âPhoneNumbersâ, âCustomerDemographicsâ and
âSubscriptionsâ and return the most recent date it finds. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| No| Short| 1 for âActiveâ, 0 for âDeleted/Inactiveâ, 2 for
âProspectâ.  
MergeCode| Yes| Short| 1 for âMergeableâ, 0 for âNon-Mergeableâ  
HasMergeHistory| No| Short| 1 â has Merge History (has loser customers
associated with it), 0 does not have Customer Merge History.  
Customer| Yes| Link| a link reference to the customer data as a resource.  
Addresses| No| List| a list of Address elements. see the [Address Lookup By
Customer Id](../omedaclientkb/postal-address-lookup-by-customer-id) for a list
of elements.  
PhoneNumbers| No| List| a list of Phone elements. see the [Phone Lookup By
Customer Id](../omedaclientkb/phone-lookup-by-customer-id) for a list of
elements.  
Emails| No| List| a list of Email elements. see the [Email Lookup By Customer
Id ](../omedaclientkb/email-address-lookup-by-customer-id)for a list of
elements.  
CustomerDemographics| No| List| a list of CustomerDemographic elements. see
the [Demographic Lookup By Customer Id](../omedaclientkb/demographic-lookup-
by-customer-id) for a list of elements.  
Subscriptions| No| List| a list of Subscription elements. see the
[Subscription Lookup By Customer Id](../omedaclientkb/subscription-lookup-by-
customer-id) for a list of elements. **Only Magazines (productType=1) and
Newsletters (productType=2) and Websites (productType=7) will be returned in
the Subscription Element. Use the**[**OptLookup API**](../omedaclientkb/email-
opt-in-out-lookup)**for Email Deployment updates (productType=5).**  
Behaviors| No| List| a list of Behavior elements. see the [Behavior Lookup by
Customer Id ](../omedaclientkb/behavior-lookup-by-customer-id)for a list of
elements.  
ExternalIds| No| List| a list of ExternalId elements. see the [External ID
Lookup by Customer Id ](../omedaclientkb/external-id-lookup-by-customer-id)for
a list of elements.  
Products| No| List| a list of additional product data for the customer. See
below for a list of elements.  
  
##### Products Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
OmedaProductId| Yes| Long| unique product identifier  
ShippingAddressId| No| Long| unique shipping address identifier  
EmailAddressId| No| Long| unique email address identifier  
TransactionDate| Yes| Date| Date of the transaction. yyyy-MM-dd format.
Example: 2017-09-08  
ProductFieldResponses| No| List| a list of product field responses for this
customer. See below for list of ProductFieldResponse elements  
  
##### Product Field Response Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
DataFieldId| Yes| Integer| unique data field identifier that this response is
for  
ValueText| Yes| String| text value of the response for this customer and data
field  
ValueDate| No| Date| date value of the response for this customer and data
field (only returned if the data field is date type)  
  
## Response

### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
### Success

CODE

    
    
    {
      "Customer":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/*",
      "Id":12345,
      "FirstName":"John",
      "MiddleName":"J",
      "LastName":"Smith",
      "Title":"Developer",
      "SignUpDate":"2016-03-12 00:00:00",
      "ChangedDate":"2016-03-15 07:56:40",
      "StatusCode":1,
      "MergeCode":1,
      "GlobalMinChangedDate":"2011-05-19 09:30:11.233",
      "Addresses":[
        {
          "Id":478928,
          "AddressContactType":100,
          "Company":"Omeda",
          "Street":"555 Huehl Road",
          "City":"Northbrook",
          "RegionCode":"IL",
          "Region":"Illinois",
          "PostalCode":"60062",
          "CountryCode":"USA",
          "Country":"United States",
          "StatusCode": 1,
          "ChangedDate": "2016-03-08 21:23:34"
        },
        {
          "Id":589129,
          "AddressContactType":110,
          "Street":"123 Walters Avenue",
          "City":"Northbrook",
          "RegionCode":"IL",
          "PostalCode":"60062",
          "CountryCode":"USA",
          "Country":"United States",
          "StatusCode": 2,
          "ChangedDate": "2016-03-08 21:23:34"
        }
      ],
      "Emails":[
        {
          "Id":472187,
          "EmailContactType":300,
          "EmailAddress":"jsmith@omeda.com",
          "StatusCode": 1,
          "ChangedDate": "2016-03-08 21:23:34"
        },
        {
          "Id":472690,
          "EmailContactType":310,
          "EmailAddress":"jsmith@domain.com",
          "StatusCode": 2,
          "ChangedDate": "2016-03-08 21:23:34"
        }
      ],
      "PhoneNumbers":[
        {
          "Id":472517,
          "PhoneContactType":200,
          "PhoneNumber":"8475648900",
          "Extension":"999",
          "StatusCode": 1,
          "ChangedDate": "2016-03-08 21:23:34"
        },
        {
          "Id":472518,
          "PhoneContactType":210,
          "PhoneNumber":"8475648901",
          "StatusCode": 2,
          "ChangedDate": "2016-03-08 21:23:34"
        }
      ],
      "CustomerDemographics":[
        {
          "Id":4201612,
          "DemographicId":100,
          "DemographicType":1,
          "ValueId":192,
          "ChangedDate": "2016-03-08 21:23:34"
        },
        {
          "Id":4201613,
          "DemographicId":101,
          "DemographicType":3,
          "ValueText":"Turquoise",
          "ChangedDate": "2016-03-08 21:23:34"
        },
        {
          "Id":4201614,
          "DemographicId":116,
          "DemographicType":6,
          "ValueDate":"2016-04-13 10:02:23",
          "ChangedDate": "2016-03-08 21:23:34"
        }
      ],
      "Subscriptions":[
        {
          "Id":8,
          "ProductId":7,
          "RequestedVersion":"P",
          "MarketingClassId":"1",
          "MarketingClassDescription":"Active Qualified",
          "Receive":1,
          "Quantity":1,
          "DataLockCode":0,
          "ChangedDate": "2016-03-08 21:23:34",
          "ShippingAddressId" : 123673467,
          "EmailAddressId" : 22176763
        }
      ],
      "ExternalIds":[
        {
          "Id":"478928",
          "Namespace":"SALESFORCE"
        },
        {
          "Id":"GH1GG4D56J211",
          "Namespace":"LINKEDIN"
        }
      ],
      "EncryptedCustomerId":"",
      "GlobalMaxChangedDate":"2012-01-31 16:05:22.58",
      "SubmissionId":"80F4C688-9404-4A28-B175-D5116A1DFBF9"
      
    }
    

### Failure

##### Standard Customer Error Message

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Customer 12345 was not found."
          }
       ]
    }
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"Could not find entry in classDefinitionMap for product [0]"
          }
       ]
    }
    

##### Merged Customer Error Message

CODE

    
    
    {
       "SubmissionId":"2da476ca-9ae6-4b4a-a77c-4aafbd275028",
       "Errors":[
          {
             "MergedIntoCustomerId":99999,
             "Error":"Customer id 22 is valid but not active. Please use 99999."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    Customer {customerId} was not found.
    Could not find entry in classDefinitionMap for product [{productId}]
    Customer id {customerId} is valid but not active. Please use {mergedIntoCustomerId}.
    OmedaCustomerId {customerId} is pending deactivation. Please try again later.

**Table of Contents**

×

