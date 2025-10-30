# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-change-
lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service returns a list of Customer Ids for Customers that were changed
within a given date range. **The date range cannot exceed 90 days.**

A **change** is defined as:

  * When the customer is first created a **change** is logged for the customer at that point.

  * Any modification to a customer record via any process.

A change can occur when customer data (contact, subscription, behavior) is
changed via

  * file processing

  * API call (included webforms submissions)

  * field update

  * Customer View

  * bulk load

  * issue close (paid is changed to expired/suspended, controlled active/inactive)

  * merge process

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/change/startdate/{startDate}/enddate/{endDate}/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/change/startdate/{startDate}/enddate/{endDate}/*
    

brandAbbreviation is the abbreviation for the brand startDate is the beginning
of the date range (inclusive) endDate is the end of the date range (inclusive)

***** Note â formatting for either date value is MMddyyyy or MMddyyyy_HHmm
with the hours and minutes (on a 24 hour clock) being optional. For example,
January 2, 2013 1:23 PM would be formatted as 01022013_1323

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

## Lookup Customer Ids of Customers changed within a specific date range

Retrieves an array containing all customer ids of customers that were changed
within the given dates and the date that the customer was last modified.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Customer Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| the internal customer identifier  
Url| Yes| Link| a link reference to the customer data as a resource.  
DateChanged| Yes| Date| the last date the customer was changed or when the
customer was first created.  
CreatedDate| Yes| Date| when the customer was first created.  
CustomerStatusId| Yes| Integer| the status of the customer. 0 = Inactive, 1 =
Active.  
  
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
       "Customers":[
          {
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/CCB/customer/122234562/*",
             "Id":122234562,
             "DateChanged":"2018-07-04 00:00:00.000",
             "CreatedDate":"2018-03-14 15:25:43.210",
             "CustomerStatusId":1
          }, 
          {
             "Url":"https://ows.omedastaging.com/webservices/rest/brand/CCB/customer/23456789/*",
             "Id":23456789,
             "DateChanged":"2018-12-17 16:49:35.573",
             "CreatedDate":"2018-09-04 11:25:52.057",
             "CustomerStatusId":0
          }
       ],
       "SubmissionId":"4085DDD2-22FF-4FFE-22CB-962CCAE603B6"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No customers changed within that date range."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No start date provided.
    No end date provided.
    Invalid start date.
    Invalid end date.
    Start date must be before end date
    No customers changed within that date range.

**Table of Contents**

×

