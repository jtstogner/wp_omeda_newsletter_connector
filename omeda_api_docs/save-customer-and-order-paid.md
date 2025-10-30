# Content from: https://knowledgebase.omeda.com/omedaclientkb/save-customer-
and-order-paid

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to post a complete set of customer identity,
contact, and demographic information along with order (paid or controlled)
information for data processing (insert/update). Note that this service
deposits data into a queue, **it does not process data immediately**. Back end
processing of the data happens through a decoupled processing layer and
depends on your own individual database configuration.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/storecustomerandorder/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/storecustomerandorder/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/save-customer-and-order-paid) for more
details.x-omeda-inputida unique id with which to process your request.Contact
your Omeda Customer Services Representative to obtain an inputid.

Note: If âWriteInDescâ is used while sending demographic data then Input
should be OEC enabled. Ask Account Representative for details.

## Supported Content Types

JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store data in the database.

### Customer Elements

Attribute Name| Required?| Description  
---|---|---  
ClientCustomerId| optional| the id of the customer in the external/interfacing
system. This id is assumed to be unique within the brand and is used to lookup
the record in the internal database in order to determine whether an insert or
update is made. If you plan on using OmedaCustomerId as the way to identify
the user, do not pass in this element.  
OmedaCustomerId| optional| the internal id of the customer. This id is unique.
Providing this guarantees that identityresolution processing will be bypassed.
You should provide this id only if you are certain that the data provided in
this call is definitely associated with the individual specified by the id. If
you plan on using ClientCustomerId as the way to identify the user, do not
pass in this element.  
ExternalCustomerId| optional| the external customer id that can be used to
identity a customer.  
ExternalCustomerIdNamespace| optional| the namespace that is associated with
which the ExternalCustomerId will be associated with. Please contact your
Omeda Account Manager to determine what needs to be sent in here. It is
recommended if the ExternalCustomerId is on the request, that this field is
also set, otherwise the default will be used, if there is no default an error
will be returned.  
CustomerStatusId| optional| the status of the submitted customer. 0 =
Inactive, 1 = Active, 3 = Test  
Salutation| optional| salutation (Ms., Mr. , Dr. etc) of customer, up 10
characters long  
FirstName| optional| first name of customer, up to 100 characters long  
MiddleName| optional| middle name of customer, up to 100 characters long  
LastName| optional| last name of customer, up to 100 characters long  
Suffix| optional| suffix of customer (Jr. Sr., III, etc.) , up to 10
characters long  
Title| optional| job title, up to 100 characters long  
Gender| optional| (M,F) male=M, female=F  
SignupDate| optional| Two formats are available: yyyy-MM-dd and yyyy-MM-dd
HH:mm. Date the person signed up  
ClientOrderId| optional| Client assigned order ID.This is an integer value.  
PromoCode| optional| Promocode for all product orders in this call,up to 50
char long.  
OrderDate| optional| Date of order, If missed, default is todayâs date  
Addresses| optional| JSON element containing multiple **Address** elements
(see below)  
Phones| optional| JSON element containing multiple **Phone** elements (see
below)  
Emails| optional| JSON element containing multiple **Email** elements (see
below)  
CustomerDemographics| optional| JSON element containing multiple
**CustomerDemographic** elements (see below)  
Products| optional| JSON element containing multiple **Product Elements**
elements (see below)  
Telemarketing| optional| JSON element containing multiple **Telemarketing
Elements** elements (see below)  
DonorId| optional| This field is used for Gift Subscriptions. DonorId is the
OmedaCustomerId of a different existing customer who is giving gift
subscription(s). If provided, all subscriptions for this transaction will be
considered Gift Subscriptions given by the DonorId customer.  
MergeCode| optional| This field is used to specify whether the customer record
should be mergeable or non-mergeable. When a mergeable record is processed, if
another customer record sufficiently matches the merge criteria, the data from
both records will be merged into one record. Valid values are â1â,
indicating the record is mergeable, and â0â, indicating the record is non-
mergeable. If no value is provided, the default value is â1â.  
  
### Addresses Elements

Attribute Name| Required?| Description  
---|---|---  
AddressContactType| optional| see [Address Contact
Types](../omedaclientkb/api-standard-constants-and-codes). If none is
provided, 100 (business) is assumed.  
Company| optional| if the address contains a company name put it here, up to
255 characters long. **Do not use the Street, ApartmentMailStop, or
ExtraAddress fields to store company name information.**  
Street| optional| first line of street address, up to 255 characters long  
ApartmentMailStop| optional| apartment, mail stop or suite number, up to 255
characters long  
ExtraAddress| optional| last line of street address, only if necessary, up to
255 characters long  
City| optional| city name, up to 100 characters long  
RegionCode| optional| For country_code=âUSAâ or âCANâ, this must be
the 2-character US state or canadian code used by the postal service. Omeda
also has region codes for other countries of the world  
Region| optional| name of region. Only used when region_code cannot be given.
Should contain the region, up to 100 characters long.  
PostalCode| optional| ZIP code or postal code.  
CountryCode| optional| 3-character [country code](../omedaclientkb/api-
standard-constants-and-codes)  
Country| optional| Name of country. Only used when country code cannot be
provided. Should contain the country, up to 100 characters long.  
AddressProducts| optional| Comma-separated list of product ids to associate
this address with or one of the following keywords; âALLâ (All products),
âNONEâ (No Products), âALL_PRINTâ (All Products that are of
âMagazineâ type), âALL_NEWSLETTERâ (All Products that are of
âNewsletterâ type. If ommitted, default is âNONEâ.  
Airmail| optional| If including this entry in the JSON the only valid
submissions are âAâ or an empty string ââ. An empty string is the only
way to clear an existing entry of âAâ.  
  
### Emails Elements

Attribute Name| Required?| Description  
---|---|---  
EmailContactType| optional| see [Email Contact Types](../omedaclientkb/api-
standard-constants-and-codes). If none is provided, 300 (primary/business) is
assumed.  
EmailAddress| required| email address, must be properly formatted  
EmailProducts| optional| Comma-separated list of product ids to associate this
email address with or one of the following keywords; âALLâ (All products),
âNONEâ (No Products), âALL_PRINTâ (All Products that are of
âMagazineâ type), âALL_NEWSLETTERâ (All Products that are of
âNewsletterâ type. If ommitted, default is âNONEâ.  
  
### Phones Elements

Attribute Name| Required?| Description  
---|---|---  
PhoneContactType| optional| see [Phone Contact Types](../omedaclientkb/api-
standard-constants-and-codes). If none is provided, 200 (business) is assumed.  
Number| required| phone number  
Extension| optional| separate extension digits, if known  
  
### CustomerDemographics Elements

Attribute Name| Required?| Description  
---|---|---  
OmedaDemographicId| conditional| Identifier that specifies the explicit omeda
demographic ID. This is an integer value. Either the **ClientDemographicId**
or **OmedaDemographicId** is required.  
OmedaDemographicValue| conditional| Holds the explicit value id that is
associated with the **OmedaDemographicId** provided above. This is a String
value. If **OmedaDemographicId** is present then **OmedaDemographicValue**
must be present but either the **ClientDemographicValue** or
**OmedaDemographicValue** is required.  
WriteInDesc| optional| âOtherâ text description, 100 char max. Only single
response and multi response demographics can have values for the
**WriteInDesc** field and only applicable to demographic values with value
type of âOtherâ.  
  
### Product Elements

Send in âProductâ elements for Magazine and Newsletter type Products to
create a subscription for that Product. If the Product is of type âEmail
Deploymentâ, you can use the Product Element and include a âFilterâ sub-
element to create an Opt-In or Opt-Out entry. Alternately, you can use the
[Optin API](../omedaclientkb/email-optin-queue) or [Optout
API](../omedaclientkb/email-optout-queue) to send in âEmail Deploymentâ
type Products.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
OmedaProductId| conditional| integer| Explicit Omeda product id for the
product being requested. Note: If any product information is provided then
**OmedaProductId** must be given.Only Magazines (productType=1) and
Newsletters (productType=2) can be updated via this API. Use the
[OptIn](../omedaclientkb/email-optin-queue)/[Optout](../omedaclientkb/email-
optout-queue) APIs forEmail Deployment updates (productType=5) and the [Assign
Behavior](../omedaclientkb/assign-behavior) API for Event updates
(prouductType=3). For a full list of types see [Product
Types](../omedaclientkb/api-standard-constants-and-codes).  
Receive| optional| short| 1 = opt-in, 0 = opt-out. Assumed to be 1 if not
given. Explicitly allows this order service to capture opt-out behaviors as
part of the order transaction.  
Quantity| optional| integer| Quantity requested. Assumed to be 1 if not given.  
PersonalIdentifier| optional| string| A Personal Identifier for the product.
For example, âWhat is your Eye Color?â Maximum of 50 characters.  
RequestedVersion| optional| char| Applicable only for products that have
different versions (âPâ for print, âDâ for digital, âBâ for both).  
ShippingAddressId| optional| integer| Explicit Omeda postal address id for the
customer. Note: if submitted, **OmedaCustomerId** must be given.  
EmailAddressId| optional| integer| Explicit Omeda email address id for the
customer. Note: if submitted, **OmedaCustomerId** must be given.  
Term| conditional| integer| Term (duration) of the subscription. You must
specify Term or OrderExpirationDate per paid product.  
PricePerQuantity| optional| decimal| Price per quantity.  
DiscountPercentage| optional| decimal| Percentage Discount for order.  
PaymentStatusId| optional| integer| Payment status of the subscription. Omit
this element if there is no payment being made. [Payment Status
Codes](../omedaclientkb/subscription-lookup-by-customer-id)  
Amount| optional| decimal| Total amount that will be billed or charged for
this transaction (not including shipping or tax).  
AmountPaid| optional| decimal| Amount that will be paid immediately as part of
this transaction.  
SalesTax| optional| decimal| Sales Tax that will be paid as part of this
transaction.  
Postage| optional| decimal| Postage that will be paid as part of this
transaction.  
PurchaseOrderNumber| optional| string| A number generated by the client in
order to authorize a purchase transaction  
StartIssueDate| optional| date| The starting date of the first issue (yyyy-MM-
dd)  
OrderExpirationDate| conditional| date| Expiration date of the order (yyyy-MM-
dd). You must specify Term or OrderExpirationDate per paid product.  
AutoRenewalCode| optional| integer| Code used to indicate automatic
subscription renewal type: 0 = No Renewal, 5 = On Expire â Auto-Charge, 6 =
On Expire â Send Invoice.For new subscriptions, AutoRenewalCode should be
excluded if no auto renewal is needed.  
NumberOfInstallments| optional| integer| Total number of installments to
receive.  
Sku| conditional| string| Product code or tracking number. Required for Single
Copy Sales products.  
  
### Product Updates Elements

Send in âProductUpdateâ elements for Magazine and Newsletter type Products
to update the requested version on a subscription for that Product without
changing the verification_date.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
OmedaProductIdUpdate| conditional| integer| Explicit Omeda product id for the
product being requested. Note: If any product information is provided then
**OmedaProductIdUpdate** must be given.Only Magazines (productType=1) and
Newsletters (productType=2) can be updated via this API. Use the
[OptIn](../omedaclientkb/email-optin-queue)/[Optout](../omedaclientkb/email-
optout-queue) APIs forEmail Deployment updates (productType=5) and the [Assign
Behavior](../omedaclientkb/assign-behavior) API for Event updates
(prouductType=3). For a full list of types see [Product
Types](../omedaclientkb/api-standard-constants-and-codes).  
RequestedVersionUpdate| optional| char| Applicable only for products that have
different versions (âPâ for print, âDâ for digital, âBâ for both).  
  
### Billing Information Elements

Billing information is only required for paid products.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
CreditCardNumber| optional| string| **Only to be used for 3rd party payment.**  
New Credit Card Number that will replace the Credit Card Number on file that
is related to all Paid Subscriptions for the Customer Id and Product Id. Omeda
suggests using [Test Credit Cards](../omedaclientkb/save-customer-and-order-
paid#Test-Credit-Cards) when testing in our Staging environment  
CreditCardType| conditional| integer| **Only to be used for 3rd party
payment.**  
Required field if CreditCardNumber is submitted. The code associated with the
Credit Card Type Valid Values: see [Credit Card Type
Codes](../omedaclientkb/api-standard-constants-and-codes).  
ExpirationDate| conditional| string| **Only to be used for 3rd party
payment.**  
Required field if CreditCardNumber is submitted. The Expiration Date for the
Credit Card Number passed in on the request (MMYY)  
CardSecurityCode| conditional| string| **Only to be used for 3rd party
payment.**  
Required field if CreditCardNumber is submitted. The Card Security Code for
the Credit Card Number passed in on the request  
NameOnCard| conditional| string| Required field if CreditCardNumber is
submitted. The Full Name associated with the Credit Card Number passed in on
the request.  
DoCharge| required| string| This value should always be âFalseâ.  
DepositDate| conditional| date| Date that payment was made (yyyy-MM-dd).
DepositDate is required for 3rd party payment.  
AuthCode| conditional| string| Authorization code. AuthCode is required for
3rd party payment.  
Comment1| optional| string| Optional comment which may display on merchant
account reports (depending on merchant account). Omeda recommends passing the
Magazine name or Brand name into this field.  
Comment2| optional| string| Optional comment which may display on merchant
account reports (depending on merchant account). Omeda recommends passing the
OmedaCustomerId into this field for existing customers, or passing the first
and last name into this field for new customers.  
BillingCompany| optional| string| Billing Address company name, if any, that
needs to be updated. Up to 255 characters long.  
BillingStreet| required| string| First line of billing street address that
needs to be updated. Up to 255 characters long  
BillingApartmentMailStop| optional| string| Billing apartment, mail stop or
suite number that needs to be updated. Up to 255 characters long  
BillingExtraAddress| optional| string| Last line of street address (only if
necessary) associated with the credit card/payment method  
BillingCity| required| string| Billing city name that needs to be updated. Up
to 100 characters long  
BillingRegion| conditional| string| Region associated with the credit
card/payment method. For country_code=âUSAâ or âCANâ, this must be the
2-character US state or Canadian code used by the postal service. Omeda also
has region codes for other countries of the world.BillingRegion is required if
BillingCountryCode is USA or Canada.  
BillingPostalCode| conditional| string| Billing ZIP code or billing postal
code. BillingPostalCode is required if BillingCountryCode is USA or Canada.  
BillingCountryCode| required| string| 3-character [country
code](../omedaclientkb/api-standard-constants-and-codes) associated with the
credit card/payment method  
PayPalPaymentId| conditional| string| Required if PayPalPayerId is submitted.
This is the unique paymentId associated with a PayPal payment. **Note:**
PayPal payment credentials must be set up in Portal for this field to be used.
Please speak with your account manager for further information. (coming soon
7/5/18)  
PayPalPayerId| conditional| string| Required if PayPalPaymentId is submitted.
This is the payerId (an id which belongs to a PayPal account) associated with
a PayPal payment. **Note:** PayPal payment credentials must be set up in
Portal for this field to be used. Please speak with your account manager for
further information. (coming soon 7/5/18)  
  
### Telemarketing Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
TelemarketingCompany| required| string| Name of Telemarketing Company, 100
char max  
TelemarketingRecordingId| required| string| ID needed to locate recording, 50
char max  
TelemarketingAgent| required| string| Name/ID of telemarketing agent, 20 char
max  
PersonalIdQuestion| required| string| Personal ID value provided by customer,
100 char max  
TimeOfCall| optional| date| Time Call occurred (date/time).Two formats are
available: yyyy-MM-dd and yyyy-MM-dd HH:mm.  
EndOfCall| optional| date| Time Call ended (date/time).Two formats are
available: yyyy-MM-dd and yyyy-MM-dd HH:mm.  
LengthOfCall| optional| integer| Length of call in minutes  
SpokeToName| required| string| Name of person the agent spoke to, 100 char max  
SpokeToTitle| required| string| Title of person the agent spoke to, 100 char
max  
AuthorizedAssistantResponse| optional| boolean(true/false)| Was person spoke
to authorized to make a product request (true/false)  
GeneralTeleInfo| optional| string| Miscellaneous information, 255 char max  
  
### Customer Behavior Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorId| Required| Omeda Event ID (Integer value)  
BehaviorDate| Required| Date the behavior occurred (yyyy-MM-dd HH:mm:ss )  
BehaviorPromoCode| optional| Promocode  
  
## Test Credit Cards

Depending on your payment vendor, various test credit card numbers are
available to simulate credit card transactions in a testing environment. These
cards will fail on Production but can be used for testing in Staging.

### Paytrace

Credit Card Type| Credit Card Number| Security Code| Expire Date  
---|---|---|---  
MasterCard| 5454545454545454| 998| Future Date  
Visa| 4111111111111111| 999| Future Date  
  
### PayPal

Credit Card Type| Credit Card Number| Security Code| Expire Date  
---|---|---|---  
MasterCard| 5555555555554444| Any| Future Date  
MasterCard| 5105105105105100| Any| Future Date  
Visa| 4111111111111111| Any| Future Date  
Visa| 4012888888881881| Any| Future Date  
American Express| 378282246310005| Any| Future Date  
American Express| 371449635398431| Any| Future Date  
Discover| 6011111111111117| Any| Future Date  
Discover| 6011000990139424| Any| Future Date  
  
  * Reference and more examples: [Paypal Developers â Testing](https://developer.paypal.com/docs/payflow/payflow-pro/payflow-pro-testing/)

### <http://Authorize.net>

Credit Card Type| Credit Card Number| Security Code| Expire Date  
---|---|---|---  
MasterCard| 5424000000000015| Any 3-digit| Future Date  
Visa| 4111111111111111| Any 3-digit| Future Date  
Discover| 6011000000000012| Any 3-digit| Future Date  
American Express| 370000000000002| Any 4-digit| Future Date  
Diners Club/ Carte Blanch| 38000000000006| Any 3-digit| Future Date  
JCB| 3088000000000017| Any 3-digit| Future Date  
  
  * Reference: <https://developer.authorize.net/hello_world/testing_guide/>

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will create a Transaction in the data queue. The
response has a **ResponseInfo** element with two sub-elements, a TransactionId
element, the Id for the transaction, and a Url element, the URL that allows
you to check the status of your transaction. See [Transaction Lookup
Service](../omedaclientkb/transaction-lookup) for more details.

#### JSON Example: Comp submission

CODE

    
    
    {
      "CustomerDemographics": [
        {
          "OmedaDemographicId": "3",
          "OmedaDemographicValue": ["37"]
        },
        {
          "OmedaDemographicId": "4",
          "OmedaDemographicValue": ["52"]
        }
      ],
      "Emails": [{
        "EmailProducts": "2",
        "EmailContactType": "300",
        "EmailAddress": "jdoeomeda@mailinator.com"
      }],
      "Addresses": [{
        "Street": "123 Fake St",
        "PostalCode": "60707",
        "AddressContactType": "100",
        "AddressProducts": "2",
        "CountryCode": "USA",
        "Company": "Omeda",
        "City": "Northbrook",
        "RegionCode": "IL"
      }],
      "Products": [{
        "Amount": "0.00",
        "AmountPaid": "0.00",
        "OmedaProductId": 2,
        "Receive": "1",
        "RequestedVersion": "B",
        "Term": "12"
      }],
      "PromoCode": "free",
      "FirstName": "Jane",
      "LastName": "Doe",
      "Title": "Tester"
    }
    

#### JSON Example: Bill me

For a Bill me transaction, the Credit Card fields are removed from the
BillingInformation section, DoCharge is set to âFalseâ in the
BillingInformation section, and AmountPaid is removed from the Products
section.

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "FirstName":"James",
       "LastName":"Smith",
       "Title":"Orthopaedic Surgeon",
       "PromoCode":"ZZZ2010A23",
       "Addresses":[
          {
             "AddressContactType":"100",
             "Company":"Smith Orthopedics",
             "ApartmentMailStop":"2nd Floor",
             "Street":"555 Huehl Road",
             "ExtraAddress":"Room 34",
             "City":"Northbrook",
             "RegionCode":"IL",
             "PostalCode":"60062-0123",
             "CountryCode":"USA",
             "AddressProducts": "12,13,14"
          }
       ],
       "BillingInformation": {
          "NameOnCard": "James Smith",
          "DoCharge": "False",
          "BillingCompany": "Smith Orthopedics",
          "BillingStreet": "555 Huehl Road",
          "BillingApartmentMailStop": "2nd Floor",
          "BillingCity": "Northbrook",
          "BillingRegion": "IL",
          "BillingPostalCode": "60062",
          "BillingCountryCode": "USA",
          "Comment1": "James Smith",
          "Comment2": "1234"
       },
       "Emails":[
          {
             "EmailContactType":"310",
             "EmailAddress":"jsmith@domain.com",
             "EmailProducts": "12,13,14"
          },
          {
             "EmailContactType":"300",
             "EmailAddress":"owner@smithortho.domain.com",
             "EmailProducts": "12,13,14"
          }
       ],
       "Phones":[
          {
             "PhoneContactType":"200",
             "Number":"847-555-7527",
             "Extension":"72"
          }
       ],
       "CustomerDemographics":[
          {
             "OmedaDemographicId":10001,
             "OmedaDemographicValue":[10002,10001]
          },
          {
             "OmedaDemographicId":10002,
             "OmedaDemographicValue":10003,
             "WriteInDesc":"Other OEC value"
          }
       ],
       "Products":[
          {
             "Amount": "65.00",
             "SalesTax": "6.50",
             "Term": "12",
             "OmedaProductId":14,
             "Receive":1,
             "RequestedVersion":"D",
             "PersonalIdentifier":"test",
             "ShippingAddressId":112,
             "EmailAddressId":1
          }
       ]
    }
    

#### JSON Example: 3rd party payment

For 3rd party payment, **Credit Card information is required** , but DoCharge
should be false. DepositDate and AuthCode will also be required.

Since the payment should have already been made prior to the order getting to
Omeda, if you donât have (or want) to pass the real CC# to Omeda, a [test
credit card](../omedaclientkb/save-customer-and-order-paid#Test-Credit-Cards)
can be used since no charge will ever actually be made (but credit card
information is still currently required to meet the standard format of our
API).

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "FirstName":"James",
       "LastName":"Smith",
       "Title":"Orthopaedic Surgeon",
       "PromoCode":"ZZZ2010A23",
       "Addresses":[
          {
             "AddressContactType":"100",
             "Company":"Smith Orthopedics",
             "ApartmentMailStop":"2nd Floor",
             "Street":"555 Huehl Road",
             "ExtraAddress":"Room 34",
             "City":"Northbrook",
             "RegionCode":"IL",
             "PostalCode":"60062-0123",
             "CountryCode":"USA",
             "AddressProducts": "12,13,14"
          }
       ],
       "BillingInformation": {
          "CreditCardType": "1",
          "CreditCardNumber": "4111111111111111",
          "ExpirationDate": "0226",
          "CardSecurityCode": "111",
          "NameOnCard": "James Smith",
          "DoCharge": "False",
          "BillingCompany": "Smith Orthopedics",
          "BillingStreet": "555 Huehl Road",
          "BillingApartmentMailStop": "2nd Floor",
          "BillingCity": "Northbrook",
          "BillingRegion": "IL",
          "BillingPostalCode": "60062",
          "BillingCountryCode": "USA",
          "Comment1": "James Smith",
          "Comment2": "1234",
          "DepositDate": "2016-09-09",
          "AuthCode": "393472480"
       },
       "Emails":[
          {
             "EmailContactType":"310",
             "EmailAddress":"jsmith@domain.com",
             "EmailProducts": "12,13,14"
          },
          {
             "EmailContactType":"300",
             "EmailAddress":"owner@smithortho.domain.com",
             "EmailProducts": "12,13,14"
          }
       ],
       "Phones":[
          {
             "PhoneContactType":"200",
             "Number":"847-555-7527",
             "Extension":"72"
          }
       ],
       "CustomerDemographics":[
          {
             "OmedaDemographicId":10001,
             "OmedaDemographicValue":[10002,10001]
          },
          {
             "OmedaDemographicId":10002,
             "OmedaDemographicValue":10003,
             "WriteInDesc":"Other OEC value"
          }
       ],
       "Products":[
          {
             "Amount": "65.00",
             "AmountPaid": "71.50",
             "SalesTax": "6.50",
             "Term": "12",
             "OmedaProductId":14,
             "Receive":1,
             "RequestedVersion":"D",
             "PersonalIdentifier":"test",
             "ShippingAddressId":112,
             "EmailAddressId":1
          }
       ]
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

Potential errors:

CODE

    
    
    ClientCustomerId {clientCustomerId} is not mapped to an existing OmedaCustomerId.
    ClientCustomerId {clientCustomerId} is mapped to more than one OmedaCustomerId.
    OmedaCustomerId {omedaCustomerId} is not a valid customer.
    CustomerStatusId has an invalid value.
    The submission contained an invalid AddressContactType {AddressContactType}
    The submission contained an invalid EmailContactType {EmailContactType}
    The submission contained an invalid PhoneContactType {PhoneContactType}
    EmailAddress is not valid {EmailAddress}
    Number must be set.
    Can't submit more than one of the following: OmedaDemographicId, ClientDemographicId.
    OmedaDemographicValue is missing for OmedaDemographicId:{OmedaDemographicId}
    ClientDemographicValue is missing for ClientDemographicId: {ClientDemographicId}
    OmedaDemographicValue {OmedaDemographicValue} is not a valid value for OmedaDemographicId {OmedaDemographicId}
    OmedaDemographicId {omedaDemographicId} is not a valid value.
    OmedaProductId is missing in Products submission
    Your submission contained an invalid date
    To set a ShippingAddressId, your submission must contain an {OmedaCustomerId}.
    The ShippingAddressId  {ShippingAddressId} is invalid.
    The ShippingAddressId  {ShippingAddressId} does not belong to the Customer submitted.
    The ShippingAddressId  {ShippingAddressId} submitted is not Active.
    To set an EmailAddressId ,your submission must contain an {OmedaCustomerId}.
    The EmailAddressId {EmailAddressId} is invalid.
    The EmailAddressId {EmailAddressId} does not belong to the Customer submitted.
    The EmailAddressId {EmailAddressId} submitted is not Active.
    TelemarketingCompany is missing.
    TelemarketingRecordingId is missing.
    TelemarketingAgent is missing.
    PersonalIdQuestion is missing.
    SpokeToName is missing.
    SpokeToTitle is missing.
    Billing address is incomplete
    BillingRegion and BillingPostalCode are required for USA and Canada.
    Payment Error: Invalid account number
    Amount cannot be less than 0
    AmountPaid cannot be less than 0
    AmountPaid cannot be greater than total order amount
    ExpirationDate should be in the future
    The CreditCardNumber and CreditCardType do not match.
    Must specify Term or OrderExpirationDate per product.
    

A failed POST submission error codes:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          "Error": "The ShippingAddressId 112 does not belong to the Customer submitted"
        }
      ]
    }
    

In the rare case that there is a server-side problem, an HTTP 500 (server
error) will be returned. This generally indicates a problem of a more serious
nature, and submitting additional requests may not be advisable.Please contact
Omeda Account Representative.

**Table of Contents**

×

