# Content from: https://knowledgebase.omeda.com/omedaclientkb/external-id-
lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

### Summary

This API provides the ability look up a Customerâs External Ids by the
**Customer Id**.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/externalid/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/externalid/*
    

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

## Lookup External Ids By Customer Id

Retrieves all available External IDs stored for a given customer id.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### External Ids Elements

Element Name| Description  
---|---  
Customer| Element containing an HTTP reference to the owning customer
resource.  
ExternalIds| Each ExternalId element contains Id and Namespace information for
each external ID that is stored.  
  
##### External Id Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Long| The external identifier  
Namespace| Yes| String| This is the acronym used to differentiate the
different kinds of external ids. For example, RDRNUM for the Omeda Legacy Id.  
  
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
       "ExternalIds":[
          {
             "Id":"478928",
             "Namespace":"SALESFORCE"
          },
          {
             "Id":"GH1GG4D56J211",
             "Namespace":"LINKEDIN"
          }
       ]
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"89ac9b95-cdd6-4152-b42b-14658a2be743",
       "Errors":[
          {
             "Error":"No External Id found for customer 12345."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No External Id found for customer {customerId}.
    Error looking up customer by customer id {customerId} for brand {brandId}.

**Table of Contents**

×

