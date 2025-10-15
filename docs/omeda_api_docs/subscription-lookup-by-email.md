# Content from: https://knowledgebase.omeda.com/omedaclientkb/subscription-
lookup-by-email

[**Knowledge Base Home**](../omedaclientkb/)

### Summary

This service returns all subscription information stored for all customers
with the given **Email Address and optional Product Id**. Note, this includes
both current subscription and deactivated subscriptions (see below to
determine the differences).

**_Note_**

  * This service only returns Product types that create a Subscription. Currently only Magazine (productType=1) and Newsletter (productType=2) type products create Customer Subscriptions

  * Use the [OptLookup API](../omedaclientkb/email-opt-in-out-lookup) for opt in/out status for Email Deployment type products (productType=5).

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

**The following lookup offers the ability to lookup Subscription Data based
solely on the email address being present on the Active Customer (the email
used in the search may not be directly associated to the subscription
returned).**

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/*
    
    

**The ability to lookup Product specific Subscription Data based solely on the
email address being present on the Active Customer (not necessarily to any
product specifically).**

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/product/{productId}/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/product/{productId}/*
    
    

**The ability to lookup Product specific Subscription Data based on the email
address being associated to the specified product for the Active Customer.**

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/product/{productId}/associated/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/email/{emailAddress}/subscription/product/{productId}/associated/*
    
    

brandAbbreviationis the abbreviation for the brandemailAddressis the email
address for which we are requesting customer informationproductIdis the
product id

### HTTP Headers

The HTTP header must contain the following elements :x-omeda-appid, a unique
id provided to you by Omeda to access your data. The request will fail without
a valid id.content-typea content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-opt-in-out-lookup) for more
details. If omitted, the default content type is application/json.

### Content Type

The content type is **application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:GETSee [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Lookup Subscription By Customer and Product Id

Retrieves a record containing all subscription information for the customer
and specific product.

### Response Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Subscription Elements

Element Name| Description  
---|---  
Customers| each Customer element contains the customerâs specific data and
the subscription information.  
  
#### Customers Elements

Element Name| Description  
---|---  
OmedaCustomerId| the internal customer identifier  
Subscriptions| each Subscription element contains all requested subscription
information.  
Url| a link reference to the customer data as a resource.  
  
#### Subscription Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
Id| no| integer| unique subscription identifier  
ProductId| no| integer| Explicit Omeda product id for the product being
requested. **Only Magazines (productType=1) and Newsletters (productType=2)
will be returned via this API. Use the**[**OptLookup
API**](../omedaclientkb/email-opt-in-out-lookup)**for Email Deployment updates
(productType=5).**  
RequestedVersion| no| String| âPâ for print, âDâ for digital, âBâ
for both  
RequestedVersionCode| no| String| âPâ for print, âDâ for digital,
âBâ for both  
ActualVersionCode| no| String| âPâ for print, âDâ for digital, âBâ
for both  
Quantity| yes| integer| the number of subscriptions requested  
DataLockCode| yes| Integer| 0 = standard / not locked, 1 = locked
(subscription cannot be updated while locked)  
Receive| no| short (boolean)| 0 = subscription not received, 1 = subscription
received NOTE: this is the primary way of determining whether a customer is or
is not CURRRENTLY receiving a product. Customers actively receiving the
product currently will have a â1â. Someone who is no longer currently
receiving the product (but has in the past) will have a â0â.  
MarketingClassId| yes| String| Subscription class code â indicating whether
the subscription is active, controlled, paid, killed etc. This is related to
the Marketing Class Description. Generally the developer will want to use this
class in preference to the Marketing Class ID field because these codes tend
to be invariant across products. [Marketing Class
Codes](../omedaclientkb/subscription-lookup-by-customer-id#Additional-
Information)  
MarketingClassDescription| yes| String| Marketing Class description.  
DeploymentTypes| yes| array| each DeploymentType element contains all
deployment type and opt-in/opt-out information.  
ShippingAddressId| yes| Integer| Internal ID of the postal address associated
with this subscription.  
BillingAddressId| yes| Integer| Internal ID of the billing address associated
with this subscription.  
BillingName| yes| String| Name on the credit card associated with this
subscription.  
EmailAddressId| yes| Integer| Internal ID of the email address associated with
this subscription.  
ChangedDate| no| Date| Date & time record last changed. yyyy-MM-dd HH:mm:ss
format. Example: 2010-03-08 21:23:34.  
Relationships| yes| Integer| Each Relationship element contains the ID and
Product Id associated to the complimentary products that were included with
the subscription as part of a promotion or default offering.  
PaymentStatus| yes| Integer| Payment status of the subscription, if it is a
PAID subscription. This element will be omitted from response if there is no
payment status associated with the subscription. [Payment Status
Codes](../omedaclientkb/subscription-lookup-by-customer-id#Additional-
Information)  
CreditBalance| yes| Decimal| Amount, in USD, of remaining balance the
subscriber owes for the subscription. Field will be omitted from response if
there is no credit balance associated with the subscription.  
Amount| yes| Decimal| Amount, in USD, the subscriber paid for the
subscription. Field will be omitted from response if there is no amount
associated with the subscription.  
LastPaymentDate| yes| Date| Returns the date of the most recent payment that
was received for a subscription.  
LastPaymentAmount| yes| Decimal| Returns amount in USD of the most recent
payment for a subscription.  
LastIssueEarnedDescription| yes| String| Short description of the issue. This
is sometimes represented as a short date, sometimes as a number. Field will be
omitted from response if there is no issue description associated with the
subscription.  
LastIssueEarnedDate| yes| Date| Last issue date.  
FirstIssueEarnedDescription| yes| String| Short description of the issue. This
is sometimes represented as a short date, sometimes as a number. Field will be
omitted from response if there is no issue description associated with the
subscription.  
FirstIssueEarnedDate| yes| Date| Date of the first issue.  
Term| yes| Integer| Length of the subscription using the productâs unit of
measure. Field will be omitted from response if there is no term associated
with the subscription, or the subscription Status is not Active.  
IssuesRemaining| yes| Integer| Projected number of issues remaining. Field
will be omitted from response if there are no issues remaining associated with
the subscription.  
CopiesRemaining| yes| Integer| Projected number of copies remaining. Field
will be omitted from response if there are no copies remaining associated with
the subscription.  
IssueExpirationDate| yes| Date| The projected expiration date for issue-based
(print/digital) products. Date & time record last changed. yyyy-MM-dd HH:mm:ss
format. Example: 2010-03-08 21:23:34. Field will be omitted from response if
there is no projected expiration date associated with the subscription.  
OrderDate| yes| Date| The date that the most recent order was entered for this
subscription. The Date & time format: yyyy-MM-dd HH:mm:ss format. Example:
2010-03-08 21:23:34. Field will be omitted from response if there is no order
date associated with the subscription.  
OriginalOrderDate| yes| Date| The date that the first order was entered for
this subscription. The Date & time format: yyyy-MM-dd HH:mm:ss format.
Example: 2010-03-08 21:23:34. Field will be omitted from response if there is
no order date associated with the subscription.  
ExpirationDate| yes| Date| The expiration date for continuous-access (i.e.
website, online access etc.) products. Date & time record last changed. yyyy-
MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34. Field will be omitted
from response if there is no expiration date associated with the subscription.  
DeactivationDate| yes| Date| This date can be used to determine if a Paid
subscription has been given extended temporary access. The Date & time format:
yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34. Field will be
omitted from response if there is no deactivation date associated with the
subscription.  
AutoRenewalCode| yes| int| The code for if the subscription is an Auto
Renewal. Valid Values are : 0 = Not Auto Renewal, 5 = Auto Charge, 6 = Auto
Bill Me on Invoice  
NumberOfInstallments| yes| Integer| The number of installments on a Paid
Subscription. Default is always 1, meaning the account is paid all at once.
Anything greater that 1 is the number of expected payments.  
InstallmentCode| yes| Integer| The type of Installment Billing Code. Valid
Values: 1=Installment Bill Me, 2=Installment Auto Charge)  
Voucher| no| string| this will only be returned if the product was paid for
with a Voucher.  
DonorId| no| Integer| The Omeda Customer Id of the Donor who purchased the
this Subscription, this will only be returned if the subscription was a Gift
Subscription  
GiftMessage| no| String| The Gift Message if it was included at time of gift
purchase, this will only be returned if the subscription was a Gift
Subscription  
GiftSentDate| no| Date| The Date the gift was sent, this will only be returned
if the subscription was a Gift Subscription  
VerificationDate| yes| Date| Verification Date of subscription.  
VerificationAge| no| Integer| Verification Age of subscription. (1, 2, 3, or 4
years. 4 years indicates 4 or more.) 1 is returned if the verification date is
in the future or less than 1 year from the audit issue. 4 is return if
verification date is 4 *or more* years away from the audit issue.  
Status| no| Integer| The Status represent the current state of the
subscription (1 â Active, 2 â Pending, 3 â Expired, 4 â Cancelled, 5
â Graced, 6 â Standing Order)  
SourceId| yes| Integer| SourceId represents the BPA Source (e.g. 23 = Personal
direct request electronic [see DataSourceConstants.java])  
ClientOrderId| no| String| Client transaction id for the order  
RenewalCount| yes| Integer| Number of times a subscription has been renewed  
PremiumCode| no| Integer| Premium code id  
PremiumCodeDescription| no| String| Description of Premium code  
  
##### DeploymentType Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
Id| no| integer| unique deployment type identifier  
In| no| integer| For this deployment type, 0 = opt-in absent, 1 = opt-in
present  
Out| no| integer| For this deployment type, 0 = opt-out absent, 1 = opt-out
present  
  
##### Relationship Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
Id| no| integer| unique relationship identifier  
Product Id| no| integer| internal id that identifies the complimentary product  
  
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

Sample response showing all subscriptions for various customers with the same
email address (1 with subscriptions, 2 without any subscriptions).

CODE

    
    
    {  
       "Customers":[  
          {  
             "Subscriptions":[  
                {  
                   "VerificationDate":"2016-01-06",
                   "VerificationAge" : 1,
                   "RequestedVersion":"D",
                   "PromoCode":"ptest",
                   "SourceId":60,
                   "ActualVersionCode":"D",
                   "Quantity":1,
                   "RequestedVersionCode":"D",
                   "ProductId":1111,
                   "MarketingClassDescription":"Active",
                   "DeploymentType":[  
                      {  
                         "In":1,
                         "Id":9999,
                         "Out":0
                      }
                   ],
                   "ShippingAddressId":9991,
                   "DataLockCode":0,
                   "OrderDate":"2016-01-06 11:47:00.0",
                   "EmailAddressId":8881,
                   "MarketingClassId":"1",
                   "Receive":1,
                   "AutoRenewalCode":0,
                   "Id":2221,
                   "ChangedDate":"2016-01-06 11:47:24",
                   "PaymentStatus":6
                },
                {  
                   "VerificationDate":"2016-01-04",
                   "VerificationAge" : 1,
                   "PromoCode":"none",
                   "IssueExpirationDate":"2017-12-01 00:00:00.0",
                   "SourceId":12,
                   "ActualVersionCode":"P",
                   "RequestedVersionCode":"P",
                   "CopiesRemaining":12,
                   "ProductId":1234,
                   "MarketingClassDescription":"Active Qualified",
                   "NumberOfInstallments":1,
                   "DataLockCode":0,
                   "ChangedDate":"2016-01-06 11:44:40",
                   "PaymentStatus":2,
                   "Status":1,
                   "RequestedVersion":"P",
                   "BillingAddressId":88888888,
                   "CreditBalance":"0.00",
                   "Quantity":1,
                   "ShippingAddressId":99999999,
                   "OrderDate":"2016-01-04 13:56:00.0",
                   "EmailAddressId":5555,
                   "MarketingClassId":"1",
                   "SubscriptionPaidId":9999,
                   "Receive":1,
                   "AutoRenewalCode":0,
                   "Id":33333333,
                   "IssuesRemaining":12
                },
                {  
                   "VerificationDate":"2016-01-06",
                   "VerificationAge" : 1,
                   "RequestedVersion":"D",
                   "PromoCode":"pweb",
                   "SourceId":60,
                   "ActualVersionCode":"D",
                   "Quantity":1,
                   "RequestedVersionCode":"D",
                   "ProductId":2,
                   "MarketingClassDescription":"Active",
                   "DeploymentType":[  
                      {  
                         "In":1,
                         "Id":6666,
                         "Out":0
                      }
                   ],
                   "ShippingAddressId":222222,
                   "DataLockCode":0,
                   "OrderDate":"2016-01-06 11:45:00.0",
                   "EmailAddressId":333333,
                   "MarketingClassId":"1",
                   "Receive":1,
                   "AutoRenewalCode":0,
                   "Id":444444,
                   "ChangedDate":"2016-01-06 11:47:24",
                   "PaymentStatus":6
                }
             ],
             "OmedaCustomerId":1000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/1000000000/*"
          },
          {  
             "Subscriptions":[  ],
             "OmedaCustomerId":2000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/2000000000/*"
          }
       ],
       "SubmissionId":"03668922-5555-6666-7777-DD9E3FD5C014"
    }
    

Sample response showing all subscriptions for various customers with the same
email address with a specific product requested (product id 1234)

CODE

    
    
    {  
       "Customers":[  
          {  
             "Subscriptions":[  
                {  
                   "VerificationDate":"2016-01-04",
                   "VerificationAge" : 1,
                   "PromoCode":"none",
                   "IssueExpirationDate":"2017-12-01 00:00:00.0",
                   "SourceId":12,
                   "ActualVersionCode":"P",
                   "RequestedVersionCode":"P",
                   "CopiesRemaining":12,
                   "ProductId":1234,
                   "MarketingClassDescription":"Active Qualified",
                   "NumberOfInstallments":1,
                   "DataLockCode":0,
                   "ChangedDate":"2016-01-06 11:44:40",
                   "PaymentStatus":2,
                   "Status":1,
                   "RequestedVersion":"P",
                   "BillingAddressId":88888888,
                   "CreditBalance":"0.00",
                   "Quantity":1,
                   "ShippingAddressId":99999999,
                   "OrderDate":"2016-01-04 13:56:00.0",
                   "EmailAddressId":5555,
                   "MarketingClassId":"1",
                   "SubscriptionPaidId":9999,
                   "Receive":1,
                   "AutoRenewalCode":0,
                   "Id":33333333,
                   "IssuesRemaining":12
                }
             ],
             "OmedaCustomerId":1000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/1000000000/*"
          },
          {  
             "Subscriptions":[  ],
             "OmedaCustomerId":2000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/2000000000/*"
          }
       ],
       "SubmissionId":"03668922-5555-6666-7777-DD9E3FD5C014"
    }
    

Sample response showing all subscriptions for various customers with the same
email address with a specific product requested associated to the email
address(product id 1234)

CODE

    
    
    {  
       "Customers":[  
          {  
             "Subscriptions":[  ],
             "OmedaCustomerId":1000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/1000000000/*"
          },
          {  
             "Subscriptions":[  ],
             "OmedaCustomerId":2000000000,
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/FOO/customer/2000000000/*"
          }
       ],
       "SubmissionId":"03668922-5555-6666-7777-DD9E3FD5C014"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No subscriptions found for email address jane@doe.com."
          }
       ]
    }
    

### Additional Information

#### Payment Status Codes

value| description| what it means  
---|---|---  
1| Paid on invoice.| Customer paid after being invoiced.  
2| Paid with order.| Customer paid at the time of his order.  
3| Credit.| Customer owes an outstanding balance on the subscription.  
6| Free.| Customer is being granted a free subscription, but isnât
necessarily qualified by the publisher.  
7| Controlled.| Customer was selected by publisher to receive subscription for
free.  
  
#### Marketing Class Codes

value| Description| Considered Active?| applies to  
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
  
#### DeploymentType element â Explained

We have a table that stores the ProductId and DeploymentTypeId relationship.
This is a 1-to-1 relationship, so you will only see one **DeploymentType**
element under the **DeploymentTypes** element. We query this table using
âwhere ProductId={ProductId}.â We then query our OPT (Opt-In/Opt-Out)
table to retrieve the presence of an Opt-In and of an Opt-Out.If no âINâ
entries are returned thenâInâ:0If one or many âINâ entries are
returned thenâInâ:1If no âOUTâ entries are returned thenâOutâ:0If
one or many âOUTâ entries are returned thenâOutâ:1

**Table of Contents**

×

