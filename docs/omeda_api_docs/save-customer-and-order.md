# Content from: https://knowledgebase.omeda.com/omedaclientkb/save-customer-
and-order

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to post a complete set of customer identity,
contact, and demographic information along with order information for data
processing (insert/update). Note that this service deposits data into a queue,
**it does not process data immediately**. Back end processing of the data
happens through a decoupled processing layer and depends on your own
individual database configuration.

**This is the same API you would use for PAID orders, for Paid fields see the
documentation for**[**Save Customer And Order Paid**](../omedaclientkb/save-
customer-and-order-paid)**.**

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/storecustomerandorder/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/storecustomerandorder/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/save-customer-and-order#Supported-
Content-Types) for more details. x-omeda-inputid a unique id with which to
process your request. Contact your Omeda Customer Services Representative to
obtain an inputid.

Note: If âWriteInDescâ is used while sending demographic data then Input
should be OEC enabled. Ask Account Representative for details.

## Supported Content Types

JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store data in the database.

### Customer Elements

Attribute Name| Required?| Description  
---|---|---  
ClientCustomerId (deprecated)| optional| This field is being deprecated,
please use ExternalCustomerId instead. The id of the customer in the
external/interfacing system. This id is assumed to be unique within the brand
and is used to lookup the record in the internal database in order to
determine whether an insert or update is made. If you plan on using
OmedaCustomerId as the way to identify the user, do not pass in this element.  
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
OrderDate| optional| Date of order, If missed, default is todayâs date. Two
formats are available: yyyy-MM-dd and yyyy-MM-dd HH:mm.  
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
ProductUpdates| optional| JSON element containing multiple **Product Updates
Elements** elements (see below)  
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
the 2-character US state or Canadian code used by the postal service. Omeda
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
âNewsletterâ type. If omitted, default is âNONEâ.  
Airmail| optional| If including this entry in the JSON the only valid
submissions are âAâ or an empty stringââ. An empty string is the only
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
demographic ID. This is an integer value.  
OmedaDemographicValue| conditional| Holds the explicit value id that is
associated with the **OmedaDemographicId** provided above. This is a String
value. If **OmedaDemographicId** is present then **OmedaDemographicValue**
must be present.  
WriteInDesc| optional| âOtherâ text description, 100 char max. Only single
response and multi response demographics can have values for the
**WriteInDesc** field and only applicable to demographic values with value
type of âOtherâ.  
  
### Product Elements

Send in âProductâ elements for Magazine and Newsletter type Products to
create a subscription for that Product. You can use the [Optin
API](https://training.omeda.com/knowledge-base/api-email-optin-queue-service/)
or [Optout API](https://training.omeda.com/knowledge-base/api-email-optout-
queue-service/) to send in âEmail Deploymentâ type Products.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
OmedaProductId| conditional| integer| Explicit Omeda product id for the
product being requested. Note: If any product information is provided then
**OmedaProductId** must be given.Only Magazines (productType=1) and
Newsletters (productType=2) can be updated via this API. Use the
[OptIn](../omedaclientkb/email-optin-queue)/[Optout](../omedaclientkb/email-
optout-queue) APIs forEmail Deployment updates (productType=5) and the [Assign
Behavior](../omedaclientkb/assign-behavior) API for Event updates
(prouductType=3). For a full list of types-queue see [Product
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
Sku| conditional| string| Product code or tracking number. Required for Single
Copy Sales products.  
  
### Product Updates Elements

Send in âProductUpdateâ elements for Magazine and Newsletter type Products
to update the requested version on a subscription for that Product without
changing the verification_date.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
OmedaProductIdUpdate| required| integer| Explicit Omeda product id for the
product being requested. Note: If any product information is provided then
**OmedaProductIdUpdate** must be given.Only Magazines (productType=1) and
Newsletters (productType=2) can be updated via this API. Use the
[OptIn](../omedaclientkb/email-optin-queue)/[Optout](../omedaclientkb/email-
optout-queue) APIs forEmail Deployment updates (productType=5) and the [Assign
Behavior](../omedaclientkb/assign-behavior) API for Event updates
(prouductType=3). For a full list of types see [Product
Types](../omedaclientkb/api-standard-constants-and-codes).  
RequestedVersionUpdate| required| char| Applicable only for products that have
different versions (âPâ for print, âDâ for digital, âBâ for both).  
  
### Marketing Product Elements

Send in âMarketingProductsâ elements for Marketing type Products to create
a Marketing Product data for the customer and product.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
OmedaProductId| required| integer| Explicit Omeda product id for the product
being requested.Only Marketing Products (productType=11) can be updated via
this element.  
ShippingAddressId| optional| integer| Explicit Omeda postal address id for the
customer. Note: if submitted, OmedaCustomerId must be given.  
EmailAddressId| optional| integer| Explicit Omeda email address id for the
customer. Note: if submitted, OmedaCustomerId must be given.  
ProductFieldResponses| optional| JSON Array| JSON element containing multiple
Product Field Response Elements (see below)  
  
### Product Field Response Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
ProductFieldId| required| integer| Explicit Omeda product field id for the
marketing product field being requested.  
ProductFieldResponseValue| required| string, integer, or date| The response
value for the product field specified. The data type is dependent upon the
product field.If itâs a date field, two formats are available: yyyy-MM-dd
and yyyy-MM-dd HH:mm  
  
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
BehaviorId| Required| Omeda Behavior ID (Integer value)  
BehaviorDate| Required| Date the behavior occurred (yyyy-MM-dd HH:mm:ss )  
BehaviorPromoCode| optional| Promocode  
BehaviorAttributes| optional| JSON element containing multiple
BehaviorAttributes Elements elements (see below)  
  
### Customer Behavior Attributes Element

Attribute Name| Required?| Description  
---|---|---  
BehaviorAttributeTypeId| Required| Omeda Behavior Attribute Type ID (Integer
value)  
BehaviorAttributeValue| Conditional| attribute value â is required for all
BehaviorAttributes unless it is a type 1 and BehaviorAttributeValueId is
present on the request  
BehaviorAttributeValueId| Conditional| attribute value Id â can only be
present for Type 1 BehaviorAttributes and can only be present if
BehaviorAttributeValue is not present  
  
## Request Examples

### JSON Example

CODE

    
    
    {
       "OmedaCustomerId":1234,
       "Salutation":"Mr.",
       "FirstName":"James",
       "MiddleName":"J",
       "LastName":"Smith",
       "Suffix":"Sr",
       "Title":"Orthopaedic Surgeon",
       "Gender":"M",
       "SignupDate":"2010-04-18",
       "ClientOrderId":"ORD19520",
       "PromoCode":"ZZZ2010A23",
       "OrderDate":"2010-04-18",
       "Addresses":[
          {
             "AddressContactType":"100",
             "Company":"Smith Orthopaedics",
             "ApartmentMailStop":"2nd Floor",
             "Street":"555 Huehl Road",
             "ExtraAddress":"Room 34",
             "City":"Northbrook",
             "RegionCode":"IL",
             "PostalCode":"60062-0123",
             "CountryCode":"USA",
             "AddressProducts": "12,13,14"
             "Airmail": "A"
          }
       ],
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
          },
          {
             "PhoneContactType":"220",
             "Number":"847-555-6573"
          },
          {
             "PhoneContactType":"210",
             "Number":"847-555-1351"
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
             "OmedaProductId":14,
             "Receive":1,
             "Quantity":1,
             "RequestedVersion":"D",
             "PersonalIdentifier":"test",
             "ShippingAddressId":112,
             "EmailAddressId":1
          }
          {
             "OmedaProductId":24, 
             "Receive":1,
           }
       ],
       "ProductUpdates":[
          {
             "OmedaProductIdUpdate":15,
             "RequestedVersionUpdate":"D"
          }
       ],
      "Telemarketing":[
          {
             "TelemarketingCompany":"Tele company Name",
             "TelemarketingRecordingId":"12345",
             "TelemarketingAgent":"Smith",
             "PersonalIdQuestion":"Eye color",
             "TimeOfCall":"2011-10-10 12:15",
             "EndOfCall":"2011-10-10 12;30",
             "LengthOfCall":15,
             "SpokeToName":"Jessica",
             "SpokeToTitle":"Manager",
             "AuthorizedAssistantResponse":false,
             "GeneralTeleInfo":"Additional Information"
          }
       ],
      "CustomerBehaviors" :[
        {
          "BehaviorId":"104",
          "BehaviorDate":"2011-07-20 12:12:12",
          "BehaviorPromoCode":"123"
        },
        {
          "BehaviorId":"105",
          "BehaviorDate":"2011-07-20 12:12:12",
          "BehaviorPromoCode":"456",
          "BehaviorAttributes": [
             {
              "BehaviorAttributeTypeId":222,
              "BehaviorAttributeValue":"Article Name Something"
              },
              {
              "BehaviorAttributeTypeId":3,
              "BehaviorAttributeValueId":60
              }  
          ]
        }
      ]
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
correct HTTP Method (POST) for this request.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact Omeda Account Representative.  
  
### Success

A successful POST submission will create a Transaction in the data queue. The
response has a **ResponseInfo** element with two sub-elements, a TransactionId
element, the Id for the transaction, and a Url element, the URL that allows
you to check the status of your transaction. See [Transaction Lookup
Service](../omedaclientkb/transaction-lookup) for more details.

CODE

    
    
    {
      "ResponseInfo":[
        {
          "TransactionId":8907512,
          "Url":"https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/transaction/8907512/*"
        }
      ]
    }
    

### Failure

##### Standard Error Message

CODE

    
    
    {
      "Errors" : [
        {
          "Error": "The ShippingAddressId 112 does not belong to the Customer submitted"
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
    

##### Possible Error Messages

This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

CODE

    
    
    Customer id {customerId} is valid but not active. Please use {mergedIntoCustomerId}.
    OmedaCustomerId {customerId} is pending deactivation. Please try again later.
    ClientCustomerId {clientCustomerId} is not mapped to an existing OmedaCustomerId.
    ClientCustomerId {clientCustomerId} is mapped to more than one OmedaCustomerId.
    OmedaCustomerId {omedaCustomerId} is not a valid customer.
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
    OmedaProductId {productId} cannot be included more than once in a submission.
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
    BehaviorAttributes must have at least one set of BehaviorAttributeTypeId and BehaviorAttributeValue.
    BehaviorAttributeTypeId is missing.
    BehaviorAttributeValue  is missing.
    BehaviorAttributeTypeId {behaviorAttributeTypeId} is not valid.
    BehaviorId: {behaviorId}, BehaviorAttributeTypeId: {behaviorAttributeTypeId} - External Behavior Tag did not match any attribute values.
    BehaviorId: {behaviorId}, BehaviorAttributeTypeId: {behaviorAttributeTypeId} - Open Text Number must be a number value.
    BehaviorId: {behaviorId}, BehaviorAttributeTypeId: {behaviorAttributeTypeId} - External Behavior Tag matched an inactive attribute values.
    

**Table of Contents**

×

