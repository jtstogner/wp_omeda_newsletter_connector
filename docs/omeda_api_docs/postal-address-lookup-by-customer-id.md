# Content from: https://knowledgebase.omeda.com/omedaclientkb/postal-address-
lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides the ability look up a Customerâs Address by the **Customer
Id**. The response will return all active addresses stored for a given
customer.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/address/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/address/*
    

brandAbbreviation is the abbreviation for the brand customerId is the internal
customer id (encrypted customer id may also be used)

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

## Lookup Addresses By Customer Id

Retrieves a record containing all available addresses information about a
customer.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Addresses Elements

Element Name| Description  
---|---  
Customer| Element containing an HTTP reference to the owning customer
resource.  
Addresses| each Address element contains all address information.  
  
##### Address Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| unique address identifier  
AddressContactType| No| Integer| that defines the type of address (see
[Standard API Constants and Codes](../omedaclientkb/api-standard-constants-
and-codes))  
Company| No| String| company name  
Street| No| String| street mailing address  
ApartmentMailStop| No| String| apartment / mail stop / suite information  
ExtraAddress| No| String| any additional mailing address information pertinent
to delivery that isnât included in the **Company** , **Street** , or
**ApartmentMailStop** elements  
City| No| String| city name  
RegionCode| No| String| the state, province or region code  
Region| No| String| when the **RegionCode** is not available, the descriptive
state, province or regional information  
PostalCode| No| String| ZIP (USA) or postal code. For USA addresses, this will
contain the full ZIP+4 code, if it is available  
CountryCode| No| String| the ISO 3166-1 alpha-3 country code  
Country| No| String| the full country description  
ChangedDate| Yes| DateTime| Date & time record last changed. yyyy-MM-dd
HH:mm:ss format. Example: 2010-03-08 21:23:34.  
StatusCode| Yes| Byte| Status of the postal address: 1 is the primary address,
2 is an active address.  
  
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
       "Addresses":[
          {
             "Id":478928,
             "AddressContactType":100,
             "Company":"Omeda",
             "Street":"555 Huehl Road",
             "ApartmentMailStop":"2nd Floor",
             "ExtraAddress":"ATTN: John Doe",
             "City":"Northbrook",
             "RegionCode":"IL",
             "Region":"Illinois",
             "PostalCode":"60062",
             "CountryCode":"USA",
             "Country":"United States of America",
             "StatusCode": 1,
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":589129,
             "AddressContactType":110,
             "Street":"123 Walters Avenue",
             "City":"Northbrook",
             "RegionCode":"IL",
             "PostalCode":"60062",
             "CountryCode":"USA",
             "StatusCode": 2,
             "ChangedDate": "2015-03-08 21:23:34"
          }
       ],
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No postal addresses found for customer 12345"
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No postal addresses found for customer {customerId}.

**Table of Contents**

×

