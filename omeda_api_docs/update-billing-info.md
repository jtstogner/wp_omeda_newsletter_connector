# Content from: https://knowledgebase.omeda.com/omedaclientkb/update-billing-
info

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability to update the Billing Information for a Single
Customer and a Single Paid Product. This service will look up all the active,
pending, and graced Paid Subscription records for the given customer and
product. For each one, it will update the Billing Info associated.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/updatebillinginfo/*

CODE

    
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/updatebillinginfo/*

### brandAbbreviation

the abbreviation for the brand to which the data is being posted.

## Technical Requirements

The HTTP header must contain the following elements:

**x-omeda-appid** : a unique id provided to you by Omeda to access your data.
The request will fail without a valid id.

**x-omeda-inputid** : a unique id with which to process your request. If
absent, the x-omeda-appidâs default inputid will be used. Contact your Omeda
Customer Services Representative to obtain an inputid.

**content-type** : The default content type is application/json.

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[http://www.json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:

**POST** : See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to store data in the database.

## Customer Elements

**Attribute Name**| **Required?**| **Description**  
---|---|---  
OmedaCustomerID| Required| The internal ID of the customer.  
CustomerStatusID| Optional| The status of the submitted customer. 0 =
Inactive, 1 = Active, 3 = Test  
OmedaProductID| Required| The product ID for the Paid Subscription that should
be updated.  
BillingInformation| Required| JSON element containing the âBilling
Informationâ Elements.  
EmailAddress| Optional| The Email Address of the customer.  
  
##  
BillingInformation Elements

**Attribute Name**| **Required?**| **Description**  
---|---|---  
CreditCardNumber| Optional| New Credit Card Number that will replace the
Credit Card Number on file that is related to all Paid Subscriptions for the
Customer Id and Product Id.  
CreditCardType| Optional| Required field if CreditCardNumber is submitted. The
code associated with the Credit Card Type Valid Values.  
ExpirationDate| Optional| Required field if CreditCardNumber is submitted. The
Expiration Date for the Credit Card Number passed in on the request  
CardSecurityCode| Optional| Required field if CreditCardNumber is submitted.
The Card Security Code for the Credit Card Number passed in on the request.  
Comment1| Optional| The fist line of a Comment that can be used on the Credit
Card processing transactions.  
Comment2| Optional| The second line of a Comment that can be used on the
Credit Card processing transactions.  
NameOnCard| Optional| Required field if CreditCardNumber is submitted. The
Full Name associated with the Credit Card Number passed in on the request.  
BillingCompany| Optional| Billing Address company name, if any, that needs to
be updated. Up to 255 characters long.  
BillingStreet| Optional| First line of billing street address that needs to be
updated. Up to 255 characters long  
BillingApartmentMailStop| Optional| Billing apartment, mail stop or suite
number that needs to be updated. Up to 255 characters long  
BillingCity| Optional| Billing city name that needs to be updated. Up to 100
characters long  
BillingRegion| Optional| Name of billing region (Country) that needs to be
updated. Up to 100 characters long.  
BillingPostalCode| Optional| Billing ZIP code or billing postal code.  
BillingCountryCode| Optional| Billing 3-character country code.  
RenewalCode| Optional| Valid values: 0 â None, 5 â auto-charge, 6 â auto
invoice.  
DoCharge| Optional| âTrueâ (DEFAULT if not provided) if credit card should
be charged immediately, âFalseâ if the customer will be invoiced later or
if a 3rd party payment has already been made.  
  
##  
Request Examples

## JSON Example

CODE

    
    
    {
        "OmedaCustomerId":123456,
        "OmedaProductId":1,
        "BillingInformation":{
           "CreditCardNumber":"5555555555554444",
           "CreditCardType":2,
           "ExpirationDate":"1215",
           "CardSecurityCode":"123",
           "Comment1":"Acme Company Sales",
           "Comment2":"(555) 555-5555",
           "NameOnCard":"John Smith",
           "BillingCompany":"Acme Corp.",
           "BillingStreet":"123 Example St.",
           "BillingApartmentMailStop":"Ste. 3",
           "BillingCity":"Anywhere",
           "BillingRegion":"IL",
           "BillingPostalCode":"60062",
           "BillingCountryCode":"USA",
           "RenewalCode":5
        }
     }

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

## Successful Submission

A successful POST submission will create a Transaction in the data queue. The
response has a ââResponseInfoââ element with sub-elements, a
TransactionId element, the Id for the transaction, a Url element, the URL that
allows you to check the status of your transaction (See [Transaction Lookup
Service](../omedaclientkb/transaction-lookup) for more details), a CustomerId,
the customer that was updated, a CustomerURL, the URL that allows you to
lookup the customer and EncryptedCustomerId, the encrypted Customer Id for the
customer that was updated (See [Customer Lookup
Service](../omedaclientkb/customer-lookup-by-customer-id) for more details).

## JSON Example

CODE

    
    
    {
        "ResponseInfo":[
           {
              "CustomerUrl":"https://ows.omedastaging.com/webservices/rest/brand/CGM/customer/1234/",
              "TransactionId":11111,
              "EncryptedCustomerId":"1111C8015245A7E",
              "Url":"https://ows.omedastaging.com/webservices/rest/brand/CGM/transaction/54097/",
              "CustomerId":1234
           },
           {
              "CustomerUrl":"https://ows.omedastaging.com/webservices/rest/brand/CGM/customer/1234/",
              "TransactionId":11112,
              "EncryptedCustomerId":"1111C8015245A7E",
              "Url":"https://ows.omedastaging.com/webservices/rest/brand/CGM/transaction/54098/",
              "CustomerId":1234
           }
        ],
        "SubmissionId":"a70b396c-32d1-6e54-98f7-1234567gh890"
     }

## Failed Submission

A failed POST submission may be due to several factors:

**Status**| **Description**  
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

## JSON Example

CODE

    
    
    {
        "SubmissionId":"d31234ab-1a1a-123a-1ab3-aca4a0a8ddb3",
        "Errors":[
           {
              "Error":"Your card could not be authorized."
           }
        ]
     }

**Table of Contents**

×

