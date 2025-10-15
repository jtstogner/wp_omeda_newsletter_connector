# Content from: https://knowledgebase.omeda.com/omedaclientkb/run-processor

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Run Processor API runs the Processor for an indivdual TransactionID that
is pending processing.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/runprocessor/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/runprocessor/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type application/json.

## Supported Content Types

JSON application/json

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

### Process Element

Attribute Name| Required?| Description  
---|---|---  
Process| required| Top level element that will contain all parameters for what
the processor should run  
  
### Transaction Info Element

Attribute Name| Required?| Description  
---|---|---  
TransactionId| required| The TransactionId or TransactionIds that you would
like the Processor to run  
  
## Restrictions

Transactions that are processed via this resource cannot have Omatch in the
Input process flow. Such cases will result in an error being returned and the
transaction not processing.

## Request Examples

### JSON Example â One TransactionId

CODE

    
    
    {
        "Process": [
            {
                "TransactionId": 789231763
            }
        ]
    }
    

### Multiple Transactions â Either format acceptable

### JSON Example â Many TransactionIds

CODE

    
    
    {
        "Process": [
            {
                "TransactionId": [ 789231763, 276347892, 2378239231 ]
            }
        ]
    }
    

### JSON Example â Many TransactionIds

CODE

    
    
    {  
       "Process":[  
          {  
             "TransactionId":8656984
          },
          {  
             "TransactionId":8656983
          }
       ]
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will return the status of each TransactionId that
was processed. This will require parsing to retrieve the information you
require.

#### JSON Example â One TransactionId

CODE

    
    
    {  
       "BatchStatus":[  
          {  
             "TransactionId":8656981,
             "OmedaCustomerId":100221802,
             "EncryptedCustomerId":"234FE43FGD34FR34",
             "TransactionStatusId":100,
             "Success":"true",
             "TransactionStatusDescription":"Moved to ERD - Complete - New Customer"
          }
       ],
       "SubmissionId":"a3f872cc-6146-4810-a258-d95bd7270901"
    }
    

#### JSON Example â Many TransactionIds

CODE

    
    
    {  
       "BatchStatus":[  
          {  
             "TransactionId":8657207,
             "OmedaCustomerId":100221846,
             "EncryptedCustomerId":"234FE43FGD34FR34",
             "TransactionStatusId":100,
             "Success":"true",
             "TransactionStatusDescription":"Moved to ERD - Complete - New Customer"
          },
          {  
             "TransactionId":8657208,
             "OmedaCustomerId":100221847,
             "EncryptedCustomerId":"234F668FGD34FR34",
             "TransactionStatusId":100,
             "Success":"true",
             "TransactionStatusDescription":"Moved to ERD - Complete - New Customer"
          }
       ],
       "SubmissionId":"4944a8bd-5d18-4819-a2a9-baab5b1ea558"
    }
    

### Error Response

In the event of an error, an error response will be returned. This will result
in an HTTP Status 400 Bad Request/404 Not Found/405 Method Not Allowed.

#### Failed POST submission â Potential Error Messages

CODE

    
    
    400 - This input_id is not allowed to run in Web Services because it has Omatch in the Process Flow. Please contact Omeda Customer Support
    404 - TransactionId {TransactionId} does not exist.
    404 - TransactionId {TransactionId} does not exist. All Transactions that were part of this request were not processed.
    404 - TransactionIds {TransactionId_1,TransactionId_2} does not exist. All Transactions that were part of this request were not processed.
    403 - TransactionId {TransactionId} has already been processed
    403 - TransactionId {TransactionId} has already been processed. All Transactions that were part of this request were not processed.
    403 - TransactionIds {TransactionId_1,TransactionId_2} has already been processed. All Transactions that were part of this request were not processed.
    500 - An exception has taken place while processing TransactionId {TransactionId}. Please contact your Omeda Customer Service Representative for assistance.
    

This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### Failed POST submission â Potential Error Codes

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
  
#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          "Error": "TransactionId {TransactionId} does not exist."
        }
      ]
    }
    

In the rare case that there is a server-side problem, an HTTP 500 (server
error) will be returned. This generally indicates a problem of a more serious
nature, and submitting additional requests may not be advisable. Please
contact Omeda Account Representative.

**Table of Contents**

×

