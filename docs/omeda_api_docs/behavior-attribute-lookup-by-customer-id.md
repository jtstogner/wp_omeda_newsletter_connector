# Content from: https://knowledgebase.omeda.com/omedaclientkb/behavior-
attribute-lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The behavior attribute lookup API call returns all behavior attributes
(regular and olytics) information for a specified customer, behavior, and date
range. This API will return results paginated, up to 50 visits per page. You
are able to specify the page number in the API (see below).

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Request URI

CODE

    
    
    For Production, use:
    https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/behaviorattribute/startdate/{mmddyyyy}/enddate/{mmddyyyy}/* 
    or with page number:  https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/behaviorattribute/startdate/{mmddyyyy}/enddate/{mmddyyyy}/page/{pagenumber}/*
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/behaviorattribute/startdate/{mmddyyyy}/enddate/{mmddyyyy}/*  
    or with page number: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/behavior/{behaviorId}/behaviorattribute/startdate/{mmddyyyy}/enddate/{mmddyyyy}/page/{pagenumber}/*

brandAbbreviation is the abbreviation of the brand.

customerId is the Omeda Customer Id. (encrypted customer id may also be used).

behaviorId is the Omeda Behavior Id.

startdate is the start of the date range of when a behavior attribute was
collected.

enddate is the end of the date range of when a behavior attribute was
collected. This cannot exceed greater than 16 days from the startdate for
regular behaviors. For Olytics behaviors, the max date range is 3 days.

***** Note â formatting for either date value is MMddyyyy or MMddyyyy_HHmm
with the hours and minutes (on a 24 hour clock) being optional. For example,
January 2, 2023 1:23 PM would be formatted as 01022023_1323.  
If you use MMddyyyy **without** the hours and minutes, then enddate must be
prior to the current date. If using MMddyyyy_HHmm (with hours and minutes),
you can use current date as the enddate but with a time prior to current time.

### HTTP Headers

The HTTP header must contain the following elements:x-omeda-appida unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Content
Types](../omedaclientkb/behavior-lookup-by-customer-id) for more details. If
omitted, the default content type is application/json.

### Content Type

The content type is **application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

### Field Definition

The following tables describe the data elements present on the responses from
the API.

#### Return Elements

Attribute Name| Data Type| Description  
---|---|---  
BehaviorId| Integer| Identifies the behavior Id being returned for the
customer.  
BehaviorName| String| Identifies the behavior name being returned for the
customer.  
Customer| String| Identifies the Customer Id being returned.  
SubmissionId| String| A unique identifier for the web services response. It
can be used to cross-reference the response in Omedaâs database.  
CustomerStatusId| Integer| Status of the customer record: 0=deleted, 1=active,
3=test.  
Behaviors| JSON Array| JSON element containing multiple**Behavior Elements**
elements (see below)  
PageSummary| JSON Array| Defines which page number of results are displayed
out of total number pages of results. Each page will display up to 50 visits.  
  
#### Behavior Return Elements

Attribute Name| Data Type| Description  
---|---|---  
LastOccurrenceDate| Datetime (format: yyyy-mm-dd)| Most recent time the
behavior occurred  
CustomerBehaviorOccurrence| JSON Array| JSON element containing
multiple**Customer Behavior Occurrence Elements** elements (see below)  
FirstOccurrenceDate| Datetime (format: yyyy-mm-dd)| First time the behavior
occurred.  
CreatedDate| Datetime (format: yyyy-mm-dd)| Date the behavior was created.  
NumberOfOccurrences| Integer| Total number of behavioral occurrences  
  
#### Customer Behavior Occurrence Return Elements

Attribute Name| Data Type| Description  
---|---|---  
CustomerBehaviorOccurrenceId| Integer| Identifies the behavior occurrence Id.  
OccurrenceDate| Datetime (format: yyyy-mm-dd)| The date of the behavior
occurrence.  
CustomerBehaviorOccurrenceAttribute| JSON Array| JSON element containing
multiple**Customer Behavior Occurrence Attribute Elements** elements (see
below)  
  
#### Customer Behavior Occurrence Attribute Return Elements

Attribute Name| Data Type| Description  
---|---|---  
AttributeValueName| String| The name of the defined behavior attribute value.
This will only be returned if the attribute has defined values.  
BehaviorAttributeValueId| Integer| The Id of the behavior attribute value.
This will only be returned if the attribute has defined values.  
ValueText| String| The text value of the behavior attribute. This will only be
returned if the attribute is open text.  
AttributeTypeName| String| The name of the behavior attribute type.  
BehaviorAttributeTypeId| Integer| The Id of the behavior attribute type.  
  
#### Page Summary Return Elements

Attribute Name| Data Type| Description  
---|---|---  
TotalPagesAvailable| Integer| Displays the total number of pages in the
response.  
CurrentPageNumber| Integer| Displays the current page that is being viewed.  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See Example Response below.  
400 Bad Request| This response will be returned when Start or End Dates are
invalid. Either future dates are inputted, End Date is prior to Start Date, or
the date range exceeds more than 2 days.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| This response occurs when the customer or behavior submitted
was not found.  
500 Internal Server Error| In the rare case that there is a server-side
problem, this response will be returned. This generally indicates a problem of
a more serious nature, and submitting additional requests may not be
advisable. Please contact your Omeda Account Representative if the issue
continues.  
  
#### Successful Submission

A successful submission will return behavior attribute data from the given
customer, behavior, and date range.

##### JSON Example

CODE

    
    
    {
       "BehaviorId":1000094,
       "BehaviorName":"Registered: 2017 Legal Hold and Data Preservation Benchmark Survey",
       "Customer":"https://ows.omeda.com/webservices/rest/brand/ACME/customer/89879/*",
       "SubmissionId":"F16178C1-D521-40F4-8AAC-4E5F8D1B6329",
       "CustomerStatusId":1,
       "Behaviors":[
          {
             "LastOccurrenceDate":"2017-07-10",
             "CustomerBehaviorOccurrence":[
                {
                   "CustomerBehaviorOccurrenceId":14009,
                   "OccurrenceDate":"2017-07-10"
                },
                {
                   "CustomerBehaviorOccurrenceId":22178,
                   "OccurrenceDate":"2017-07-10",
                   "CustomerBehaviorOccurrenceAttribute":[
                      {
                         "ValueText":"omeda.com/articleabc",
                         "AttributeTypeName":"article",
                         "BehaviorAttributeTypeId":1000003
                      },
                      {
                         "ValueText":"omeda.com",
                         "AttributeTypeName":"webpagevisit",
                         "BehaviorAttributeTypeId":1000001
                      },
                      {
                         "AttributeValueName":"whitepaper abc",
                         "BehaviorAttributeValueId":3,
                         "AttributeTypeName":"whitepaper",
                         "BehaviorAttributeTypeId":3
                      }
                   ]
                }
             ],
             "FirstOccurrenceDate":"2017-07-10",
             "CreatedDate":"2019-05-22",
             "NumberOfOccurrences":2
          },
          {
             "LastOccurrenceDate":"2017-07-10",
             "CustomerBehaviorOccurrence":[
                {
                   "CustomerBehaviorOccurrenceId":22782,
                   "OccurrenceDate":"2017-07-10",
                   "CustomerBehaviorOccurrenceAttribute":[
                      {
                         "ValueText":"omeda.com",
                         "AttributeTypeName":"webpagevisit",
                         "BehaviorAttributeTypeId":1000001
                      }
                   ]
                }
             ],
             "FirstOccurrenceDate":"2017-07-10",
             "CreatedDate":"2019-05-30",
             "NumberOfOccurrences":1
          }
       ],
       "PageSummary":[
          {
             "TotalPagesAvailable":1,
             "CurrentPageNumber":1
          }
       ]
    }

**Table of Contents**

×

