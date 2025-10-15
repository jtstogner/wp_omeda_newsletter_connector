# Content from: https://knowledgebase.omeda.com/omedaclientkb/transaction-
lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Transaction Lookup service is used to check on the submission status of a
particular POST submission from DataQueue. Please note that the data submitted
to the queue will not necessarily be kept available indefinitely.

## Base Resource URI

CODE

    
    
    In Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/transaction/{transactionId}/*
    
    In Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/transaction/{transactionId}/*
    

brandAbbreviationidentifies the brand, typically a short alphanumeric
codetransactionIdthe transaction identifier, handed back by the Save Customer
And Order POST submission.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/transaction-lookup) for more details. If
omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is
**application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:GETSee [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Field Definition

The following table describes the hierarchical data elements.

### ResponseInfo Elements

Element Name| Optional?| Description  
---|---|---  
TransactionId| no| the identifier for a transaction  
Status| no| the status of the transaction. See [Status
Descriptions](../omedaclientkb/transaction-lookup#Additional-Information) for
more details.  
OmedaCustomerId| yes| the internal identifier for a customer  
ClientCustomerId| yes| the external identifier for a customer  
Errors| yes| a list of errors with the transaction  
  
#### Errors Elements

Element Name| Optional?| Description  
---|---|---  
Error| yes| a description of the error  
  
## Response Examples

### JSON Example

Successful Return:  
Note â the Error in this return is not an Error for the API call, but an
error found in the transaction. If there is no error in the transaction, this
error block will not be returned in the API call.

CODE

    
    
     
    { 
     "TransactionId":42355632, 
     "Status":2, 
     "ClientCustomerId":1293401, 
    
    "Errors":[   
    { 
       "Error":"invalid email", 
       "Error":"vulgarity" 
      } 
     ] 
    }
    

Unsuccessful Return:  
Note â the Error for the API would return a 404 if a bad transactionid
exists in the API call. See unsuccessful example below.

CODE

    
    
    {
    "Errors":[
    {
    "Error":
    "No status found for transaction 55973543453."
    }
    ],"SubmissionId":"40078f34-7f99-416b-9635-1d63242ef3fd"
    }
    
    

### Additional Information

### Status Descriptions

Status| Description  
---|---  
0| Loaded into Staging  
1| Customer On Hold  
2| Customer Has Excessive Errors  
3| Paid Customer On Hold  
4| Payment Pending  
5| Payment Succeeded  
6| Payment Failed  
7| API Transaction Loaded into Staging  
8| Verification Rules Ready  
9| Failed Validation Rules  
10| Passed Validation Rules  
19| OEC Ready  
20| OEC On Hold  
21| OEC Done  
28| DupeCheck Ready  
29| DupeCheck Running  
30| DupeCheck New Customer  
31| DupeCheck Existing Customer  
35| DupeCheck Manual Review  
36| DupeCheck Manual Review New Customer  
37| DupeCheck Manual Review Existing Customer  
49| Source Prioritization Ready  
50| Source Prioritization Passed  
51| Source Prioritization Failed  
52| Source Prioritization Partial Failure  
90| Ready to Migrate to ERD New Customer  
91| Ready to Migrate to ERD Existing Customer  
98| Moved to ERD â Failed Source Prioritization  
99| Moved to ERD â Partial  
100| Moved to ERD â Complete New Customer  
101| Moved to ERD â Complete Existing Customer  
110| Discard Record  
  
**Table of Contents**

×

