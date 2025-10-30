# Content from: https://knowledgebase.omeda.com/omedaclientkb/demographic-
lookup-by-customer-id

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This service returns all available customer demographics stored for a given
customer by using the **Customer ID**.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/demographic/*
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/demographic/*
    

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

## Lookup Demograhics By Customer Id

Retrieves a record containing all demographic information about the customer.

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### CustomerDemographics Elements

Element Name| Description  
---|---  
Customer| Element containing an http reference to the owning customer
resource.  
CustomerDemographic| each **CustomerDemographic** element contains all
demographic information.  
  
##### CustomerDemographic Elements

Element Name| Always Returnedâ¦| Data type| Description  
---|---|---|---  
Id| No|  | unique customer demographic identifier  
DemographicId| No|  | integer that defines the demographic the responses are gathered for  
DemographicType| No|  | integer â see Additional Information for the list of values and their descriptions  
DemographicAge| No|  | integer â The number of years since the demographic was collected for this customer. The âyearâ is calculated based on the productâs audit cycle.  
ValueId| Yes|  | the individual response value, if the response/data was for a single-response or a multiple-response demographic.  
ValueText| Yes|  | the individual response value, if the response/data was for a text-response demographic.  
ValueDate| Yes|  | the individual response value, if the response/data was for a date-response demographic.  
WriteInDesc| Yes| String| If the Demographics Value âTypeâ is an
âOtherâ response, and a text answer is being stored for this âOtherâ
value, then the text answer will be returned.  
AlternateId| No| String| An id that can be used to uniquely identify this
demographic(perhaps in your content management system).  
ChangedDate| no|  | Date & time record last changed. yyyy-MM-dd HH:mm:ss format. Example: 2010-03-08 21:23:34.  
  
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
       "CustomerDemographics":[
          {
             "Id":4201512,
             "DemographicId":100,
             "DemographicType":1,
             "ValueId":192,
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":4201513,
             "DemographicId":101,
             "DemographicType":3,
             "ValueText":"Turquoise",
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":4201514,
             "DemographicId":114,
             "DemographicType":1,
             "ValueId":196,
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":4201515,
             "DemographicId":115,
             "DemographicType":2,
             "ValueId":197,
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":4201516,
             "DemographicId":115,
             "DemographicType":1,
             "WriteInDesc":"write in text",
             "ChangedDate": "2015-03-08 21:23:34"
          },
          {
             "Id":4201517,
             "DemographicId":116,
             "DemographicType":6,
             "ValueDate":"2015-04-19 11:33:17",
             "ChangedDate": "2015-03-08 21:23:34"
          }
       ]
       "SubmissionId" : "24B9BF6F-0677-462B-942A-D87EEBD10F77"
    }
    

#### Failure

CODE

    
    
    {
       "SubmissionId":"ec0c2ba6-13f4-4934-8efa-74c2ccb33f1d",
       "Errors":[
          {
             "Error":"No demographics found for customer 12345."
          }
       ]
    }
    

###### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

CODE

    
    
    No demographics found for customer {customerId}.
    

### Additional Information

#### Demographics â DemographicType

StatusCode| Description  
---|---  
1| a single-choice-allowed type of question. (Two or more choices â one
answers)  
2| a multiple-choice-allowed type of question. (Two or more choices â many
answers)  
3| an open-form short answer question. (No choices â text input)  
5| a yes/no answer  
6| a date answer  
7| a whole number answer  
8| a decimal answer  
  
**Table of Contents**

×

