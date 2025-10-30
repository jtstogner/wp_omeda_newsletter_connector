# Content from: https://knowledgebase.omeda.com/omedaclientkb/gift-lookup-by-
donor-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service returns all available gift information where a given **Customer
Id** is the**Donor ID** and it can be filtered with**optional Product Id**.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/gift/*
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/gift/product/{productId}/*

CODE

    
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/gift/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/gift/product/{productId}/*

**customerId** is the internal customer id (encrypted customer id may also be
used) **productId (optional)** is the product id.

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/subscription-lookup-by-customer-id)
for more details. If omitted, the default content type is application/json.

### Content Type

The content type is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported: GET See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Lookup Gifts By Donor Id

Retrieves a record containing all gifts with the given Customer Id as the
Donor Id.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Gift Recipient Elements

Element Name| **Optional?**| **Data Type**|  Description  
---|---|---|---  
FirstName| no| String| First name of the gift recipient.  
Id| no| Integer| Customer ID of the gift recipient.  
LastName| no| String| Last Name of the Gift Recipient  
Addresses| no| List| A list of address elements. See the [Postal Address
Lookup by Customer ID ](../omedaclientkb/postal-address-lookup-by-customer-
id)for a list of elements.  
Subscriptions| no| List| A list of Subscription elements. see the
[Subscription Lookup By Customer Id](../omedaclientkb/subscription-lookup-by-
customer-id) for a list of elements  
Emails| no| List| A list of Email elements. see the [Email Lookup By Customer
Id ](../omedaclientkb/email-address-lookup-by-customer-id)for a list of
elements.  
  
### Response

#### HTTP Response Codes

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
  
#### Success Example

CODE

    
    
    {
        "GiftRecipients":[
           {
              "Addresses":[
                 {
                    "Company":"bally",
                    "RegionCode":"IL",
                    "Country":"UNITED STATES",
                    "PostalCode":"60657",
                    "Region":"ILLINOIS",
                    "Street":"837 w barry ave",
                    "Id":408067,
                    "City":"CHICAGO",
                    "CountryCode":"USA",
                    "StatusCode":1,
                    "ChangedDate":"2021-02-24 07:29:40"
                 }
              ],
              "Customer":"https://ows.omeda.com/webservices/rest/brand/CGM/customer/142498/",          
    "Subscriptions":[
                {
                   "VerificationDate":"2021-02-19",
                   "PromoCode":"Test",
                   "VerificationAge":1,
                   "SourceId":20,
                   "ActualVersionCode":"P",
                   "RequestedVersionCode":"P",
                   "CopiesRemaining":20,
                   "ProductId":7,
                   "MarketingClassDescription":"Active Qualified",
                   "NumberOfInstallments":1,
                   "DataLockCode":0,
                   "OriginalOrderDate":"2021-02-19 12:33:57.097",
                   "RenewalCount":0,
                   "DonorId":142495,
                   "ChangedDate":"2021-02-24 07:29:42",
                   "PaymentStatus":2,
                   "Status":1,
                   "RequestedVersion":"P",
                   "BillingAddressId":408059,
                   "Amount":"23.00",
                   "LastPaymentDate":"2021-02-03",
                   "CreditBalance":"0.00",
                   "Quantity":1,
                   "Term":20,
                   "ShippingAddressId":408067,
                   "EmailAddressId":311457,
                   "OrderDate":"2021-02-19 12:34:00.0",
                   "MarketingClassId":"1",
                   "SubscriptionPaidId":227092,
                   "Receive":1,
                   "AutoRenewalCode":0,
                   "Id":63305,
                   "LastPaymentAmount":"25.13",
                   "IssuesRemaining":20
                }
             ],
             "FirstName":"mileena",
             "Id":142498,
             "LastName":"kitana",
             "Emails":[
                {
                   "HashedEmailAddress":"8d5f20087fd4738ff6c688d7dc59421e04a66e42",
                   "Id":311457,
                   "StatusCode":1,
                   "ChangedDate":"2021-02-24 07:29:40",
                   "EmailAddress":"mileenakitana@omeda.com"
                }
             ]
          }
       ],
       "Customer":"https://ows.omeda.com/webservices/rest/brand/CGM/customer/142495/*"
    }

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No gifts found for customer 12345."
          }
       ]
    }

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No gifts found for customer {customerId}.
    Could not find entry in classDefinitionMap for product {productId}.
    

**Table of Contents**

×

