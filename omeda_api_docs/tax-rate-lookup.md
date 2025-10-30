# Content from: https://knowledgebase.omeda.com/omedaclientkb/tax-rate-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Tax Rate Lookup API returns the tax rate that should be applied to an
order based on their location and product id being ordered. The lookup will
also take into consideration tax exempt customers if the Customer Id is passed
in.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/taxrate/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/taxrate/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

## Technical Requirements

The HTTP header must contain the following elements: content-type a content
type supported by this resource. See [Supported Content
Types](../omedaclientkb/tax-rate-lookup) for more details. If omitted, the
default content type is application/json.

## Supported Content Types

There are three content types supported. If omitted, the default content type
is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported: POST See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to lookup data in the database.

### Tax Rate Lookup Element

Attribute Name| Required?| Description  
---|---|---  
PostalCode| required| ZIP code or postal code.  
RegionCode| required| For country_code=âUSAâ or âCANâ, this must be
the 2-character US state or Canadian code used by the postal service. Omeda
also has region codes for other countries of the world.  
CountryCode| required| 3-character [country code](../omedaclientkb/api-
standard-constants-and-codes)  
OmedaProductId| required| Explicit Omeda product id for the product being for
which tax is being requested.  
Term| optional| The number of issues for the subscription.  
OmedaCustomerId| optional| The internal id of the customer. This id is unique.  
  
## Request Examples

### JSON Example

CODE

    
    
    {  
       "PostalCode":"60062",
       "OmedaProductId":"19",
       "CountryCode":"USA",
       "RegionCode":"IL",
       "Term": 12,
       "OmedaCustomerId":12345
    }
    

## Response â Success

Upon successful update of a behavior, an HTTP 200 will be issued.

### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded.  
  
### Example Responses

CODE

    
    
    {  
       "ResponseInfo":[  
          {  
             "TaxRate": .1025
          }
       ],
        "SubmissionId":"11111111-22bf-3f33-4444-55ef55d55555"
    }
    

If no tax should be charged based on supplied info then the following will be
returned with a âTaxRateâ of 0.

CODE

    
    
    {  
       "ResponseInfo":[  
          {  
             "TaxRate":0
          }
       ],
        "SubmissionId":"11111111-22bf-3f33-4444-55ef55d55555"
    }
    

## Response â Failure

If an error occurs repeatedly, please contact your Omeda representative.

### HTTP Response Codes

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
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact Omeda Account Representative.  
  
### Example Response

CODE

    
    
    {  
       "Errors":[  
          {  
             "Error":"OmedaProductId 3 is not a valid."
          }
       ],
       "SubmissionId":"11111111-22bf-3f33-4444-55ef55d55555"
    }
    

### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    OmedaProductId {OmedaProductId} is not a valid.
    OmedaCustomerId not found

**Table of Contents**

×

