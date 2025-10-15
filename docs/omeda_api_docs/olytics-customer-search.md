# Content from: https://knowledgebase.omeda.com/omedaclientkb/olytics-
customer-search

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

An api available to our [olytics](../omedaclientkb/cdp-olytics-overview)
clients.

For a given set of olytics/behavioral search parameters, this API will return
a list of Omeda customer ids matching those parameters.

This api can be used to help in building an external lead gen tool.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/olytics/search/customers/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/olytics/search/customers/*
    
    

brandAbbreviation is the abbreviation for the brand

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/olytics-customer-top-values) for
more details. If omitted, the default content type is application/json.

### Content Type

The content type is **application/json**. JSON application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported: POST See [W3Câs POST
specs](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Look Up Customers By Olytics/Behavioral Search Parameters

Retrieves a list of Omeda customer ids that match the given search parameters

### POST Body

The following tables describe the search parameters available.

Element Name| Optional?| Data Type| Description  
---|---|---|---  
BehaviorId| no| String| The encrypted-behavior-id is passed in the
olytics.fire() function. Usually a client will have a different once for each
of their websites.  
BehaviorAttributeType| no| String| The Behavior Attribute Type (olytics field)
that you wish to search for matching customers. The Behavior Attribute Type is
passed in the olytics.fire() function. Some common attribute type would be
âtagâ, âcategoryâ, âOlytics Tagâ, and âOlytics Categoryâ.  
BehaviorAttributeValue| no| String| The Behavior Attribute Value(olytics valid
value) tied to the Behavior Attribute Type. The Behavior Attribute Value is
the open-text field passed in the olytics.fire() function.  
VisitDateStart| no| Date (yyyy-MM-dd HH:mm:ss)| A required field that searches
for olytics page views that happened *after* this date. So if the user passes
2019-06-01 00:00:00 â the API would only return customers whose visits
happened after June 1st at 12:00AM CST.  
VisitDateEnd| yes| Date (yyyy-MM-dd HH:mm:ss)| An optional field that searches
for olytics page views that happened *before* this date. So if the user passes
2019-09-01 00:00:00 â the API would only return customers whose visits
happened before September 1st at 12:00AM CST.  
MinimumVisitCount| yes| Number| If specified â you can narrow down the
customers returned by the API to only those who have a minimum number of X
visits during the data range in question (and in conjunction with your other
search parameters).  
  
#### Sample Search Post Body:

CODE

    
    
    {
    	BehaviorId: "1906A2455689A3K",
    	VisitDateStart: "2019-06-01 00:00:00",
    	VisitDateEnd: "2019-09-01 00:00:00",
    	BehaviorAttributeType: "Olytics Category",
    	BehaviorAttributeValue: "pure olytics 3.0 category - staging",
            MinimumVisitCount: 5
    }
    

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
Customers| no| array| an array containing omeda customer ids whose behavioral
data matched the search parameters.  
CustomerCount| no| Number| The count of the number of customers returned.  
  
#### Success Response Example

CODE

    
    
    {
    	"Customers": [459271, 459267, 444418, 472066, 450560, 463373],
    	"SubmissionId": "1D7A81B7-120D-411B-86AF-C487137A9D6D",
    	"CustomerCount": 6
    }

**Table of Contents**

×

