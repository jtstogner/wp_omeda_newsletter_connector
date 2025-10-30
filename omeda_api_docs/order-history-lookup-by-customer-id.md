# Content from: https://knowledgebase.omeda.com/omedaclientkb/order-history-
lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up all available Order History information
for a customer by the **Customer id** or for a specific product if the
**Product Id** is included. The response will include the http reference to
the owning customer resource and Order History details with all purchase
information.

Note:

  * This service does not return Products that create a Subscription. Use the [Subscription Lookup API ](../omedaclientkb/subscription-lookup-by-customer-id)for Magazine (productType=1) and Newsletter (productType=2) type products.

  * Use the [OptLookup API](../omedaclientkb/email-opt-in-out-lookup) for opt in/out status for Email Deployment type products (productType=5)

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/orderhistory/*
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/orderhistory/product/{productId}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/orderhistory/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/orderhistory/product/{productId}/*

**brandAbbreviation**

is the abbreviation for the brand

**customerId**

is the internal customer id (encrypted customer id may also be used.)

**productId (optional)**

is the product id

### HTTP Headers

The HTTP header must contain the following elements:

**x-omeda-appid**

a unique id provided to you by Omeda to access your data. The request will
fail without a valid id.

**content-type**

a content type supported by this resource. See [Supported Content
Types](../omedaclientkb/subscription-lookup-by-customer-id) for more details.
If omitted, the default content type is application/json.

### Content Type

The content type is always **application/json**.

**application/json**

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup Order History By Customer Id

Retrieves all Order History information about the customer or for a specific
Product if one is included.

### Field Definition

The following table describes the hierarchical data elements present on the
response from the API.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Purchase History Elements

Element Name| Description  
---|---  
Customer| Element containing an http reference for the product being
requested.  
Order History| Each Order element contains the details for the specific order  
  
#### Order History Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
ProductId| Yes| Integer| Explicit Omeda product id for the product being
requested  
Orders| Yes| Array| Each Order element contains the details for the specific
order  
  
#### Orders Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
ShippingAddressId| No| Integer| Internal ID of the postal address associated
with this order.  
EmailAddressId| No| Integer| Internal ID of the email address associated with
this order.  
RequestedVersion| Yes| String| âPâ for print, âDâ for digital, âBâ
for both  
RequestedVersionCode| Yes| String| âPâ for print, âDâ for digital,
âBâ for both  
ActualVersionCode| No| String| âPâ for print, âDâ for digital, âBâ
for both  
Quantity| No| Integer| the number of orders requested  
SKU| Yes| String| The SKU used during order entry  
Receive| Yes| Short (boolean)| 0 = order not received, 1 = order received
NOTE: this is the primary way of determining whether a customer is or is not
CURRRENTLY receiving a product. Customers actively receiving the product
currently will have a â1â. Someone who is no longer currently receiving
the product (but has in the past) will have a â0â.  
MarketingClassId| No| String| Indicates whether the subscription is active,
controlled, paid, killed etc. This is related to the Marketing Class
Description.  
MarketingClassDescription| No| String| Marketing Class description.  
BillingAddressId| No| Integer| Internal ID of the billing address associated
with this order.  
ChangedDate| Yes| Date| Date & time record last changed. yyyy-MM-dd HH:mm:ss
format. Example: 2010-03-08 21:23:34.  
PaymentStatus| No| Integer| Payment status of the order, if it is a PAID
order. This element will be omitted from response if there is no payment
status associated with the order. [Payment Status
Codes](../omedaclientkb/subscription-lookup-by-customer-id#Additional-
Information)  
Amount| No| Decimal| Amount, in USD, the customer paid for the order. Field
will be omitted from response if there is no amount associated with the order.  
Term| No| Integer| Length of the order using the productâs unit of measure.
Field will be omitted from response if there is no term associated with the
order.  
OrderDate| No| Date| The date that the most recent order was entered for this
order. The Date & time format: yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08
21:23:34. Field will be omitted from response if there is no order date
associated with the order.  
AutoRenewalCode| No| Integer| The code for if the order is an Auto Renewal.
Valid Values are : 0 = Not Auto Renewal, 5 = Auto Charge, 6 = Auto Bill Me on
Invoice  
NumberOfInstallments| No| Integer| The number of installments on a Paid Order.
Default is always 1, meaning the account is paid all at once. Anything greater
that 1 is the number of expected payments.  
InstallmentCode| No| Integer| The type of Installment Billing Code. Valid
Values: 1=Installment Bill Me, 2=Installment Auto Charge)  
DonorId| No| Integer| The Omeda Customer Id of the Donor who purchased the
order, this will only be returned if the order was a Gift Order  
GiftMessage| No| String| The Gift Message if it was included at time of gift
purchase, this will only be returned if the order was a Gift Order  
GiftSentDate| No| Date| The Date the gift was sent, this will only be returned
if the order  
was a Gift Order  
VerificationDate| No| Date| Verification Date of order.  
SourceId| No| Integer| SourceId represents the BPA Source (e.g. 23 = Personal
direct request electronic)  
PremiumCode| No| Integer| Premium code id  
PremiumCodeDescription| No| String| Description of Premium code  
  
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
  
#### Success

CODE

    
    
    {
       "Customer":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/12345/*",
       "OrderHistory":[
          {
             "ProductId":7,
             "Orders":[
                {
                   "Id":111111,
                   "ShippingAddressId":123673467,
                   "EmailAddressId":22176763,
                   "RequestedVersion":"P",
                   "Quantity":1,
                   "Receive":1,
                   "MarketingClassId":28114,
                   "MarketingClassDescription":"Active Non-Qualified",
                   "ChangedDate":"2012-09-28 21:23:34",
                   "PaymentStatus":2,
                   "VerificationDate":"2012-03-07"
                },
                {
                   "Id":111222,
                   "ShippingAddressId":123673467,
                   "EmailAddressId":22176763,
                   "RequestedVersion":"P",
                   "Quantity":1,
                   "Receive":1,
                   "MarketingClassId":28114,
                   "MarketingClassDescription":"Active Non-Qualified",
                   "ChangedDate":"2012-09-28 21:23:34",
                   "PaymentStatus":2,
                   "VerificationDate":"2012-03-07"
                }
             ]
          },
          {
             "ProductId":22,
             "Orders":[
                {
                   "Id":999999,
                   "ShippingAddressId":123673467,
                   "EmailAddressId":22176763,
                   "RequestedVersion":"P",
                   "Quantity":1,
                   "Receive":1,
                   "MarketingClassId":28114,
                   "MarketingClassDescription":"Active Non-Qualified",
                   "ChangedDate":"2012-09-28 21:23:34",
                   "PaymentStatus":2,
                   "VerificationDate":"2012-03-07"
                },
                {
                   "Id":999888,
                   "ShippingAddressId":123673467,
                   "EmailAddressId":22176763,
                   "RequestedVersion":"P",
                   "Quantity":1,
                   "Receive":1,
                   "MarketingClassId":28114,
                   "MarketingClassDescription":"Active",
                   "ChangedDate":"2012-09-28 21:23:34",
                   "PaymentStatus":2,
                   "VerificationDate":"2012-03-07"
                }
             ]
          }
       ],
       "SubmissionId":"24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No purchases found for customer 12345."
          }
       ]
    }

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No Purchases found for customer {customerId}.

### Additional Information

#### Payment Status Codes

**value**| **description**| **what it means**  
---|---|---  
1| Paid on invoice.| Customer paid after being invoiced.  
2| Paid with order.| Customer paid at the time of his order.  
3| Credit.| Customer owes an outstanding balance on the subscription.  
5| Grace.| Customer subscription is in grace.  
6| Free.| Customer is being granted a free subscription, but isnât
necessarily qualified by the publisher.  
7| Controlled.| Customer was selected by publisher to receive subscription for
free.  
8| Free Term.| Customer was granted a paid subscription at no cost. (free with
expire date, formerly known as Comp)  
  
#### Marketing Class Codes

**value**| **Description**| **Considered Active?**| **applies to**  
---|---|---|---  
1| Active. For controlled magazine products, 1 denotes Active Controlled.|
yes| All product types.  
2| Active Non-Qualified| yes| Only products with paid circulation.  
3| Qualified Reserve| no| Only products with controlled circulation.  
8| Soft controlled kills| no| Only products with controlled circulation.  
9| Controlled kills| no| Only products with controlled circulation.  
10| ACS kills (Address Correction Service)| no| Only products having
subscription address delivery.  
20| Expire suspends| no| Subscription based products having an expiration
date.  
21| Future starts| no| Subscription based products having an expiration date.  
22| Postal suspends| no| Only products having subscription address delivery.  
23| Credit Suspends| no| Subscription based paid products.  
24| Requested Suspends| no| Any product type.  
25| Kill/Refunds| no| Subscription based paid products.  
50| Passalong| no| Any product type.  
  
**Table of Contents**

×

