# Content from: https://knowledgebase.omeda.com/omedaclientkb/customer-
olytics-data

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

An api available to our [olytics](../omedaclientkb/cdp-olytics-overview)
clients.

For a given omeda customer id and set of olytics/behavioral search parameters,
this API will return olytics behavioral data matching those parameters.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/olytics/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/customer/{customerId}/olytics/*
    
    

brandAbbreviationis the abbreviation for the brandcustomerIdis the Omeda
customerId of the customer in question

### HTTP Headers

The HTTP header must contain the following elements:x-omeda-appida unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/olytics-customer-top-values) for more details.
If omitted, the default content type is application/json.

### Content Type

The content type is **application/json**.JSONapplication/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Look Up Olytics data for a given customer and search parameters

Retrieves an array where each element represents a page view recorded by
olytics.fire().

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
  
#### Sample Search Post Body:

CODE

    
    
    {
    	BehaviorId: "1906A2455689A3K",
    	VisitDateStart: "2019-06-01 00:00:00",
    	VisitDateEnd: "2019-09-01 00:00:00",
    	BehaviorAttributeType: "Olytics Category",
    	BehaviorAttributeValue: "pure olytics 3.0 category - staging"
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
Data| no| array| An array of olytics page view data points.  
  
#### Data Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
OperatingSystem| yes| String| The operating system of the visitors device when
they viewed the page.  
DeviceType| yes| String|  The device type of the visitors device when they
viewed the page.  
Browser| yes| String|  The browser the visitor was using when they visited the
page.  
ReferringDomain| yes| String| The URL the visitor was on previously that lead
them to your page. If referring domain is no found â this generally
indicates the visitor made it to the page via a click on an email client
(outlook for example).  
PageTitle| yes| String| The page title for the given page view.  
IpAddress| yes| String| The IP Address of the visitor.  
VisitDate| yes| Date| The date/time of the page view (Central Standard Time
â yyyy-MM-dd HH:mm:ss).  
Url| no| String| The URL or the page view, excludes parameters.  
  
#### Success Response Example

CODE

    
    
    {
    	"SubmissionId": "7E9980DD-0447-4C22-B1FE-9DF4C10648C3",
    	"Data": [{
    		"OperatingSystem": "Windows",
    		"DeviceType": "Personal computer",
    		"ReferringDomain": "",
    		"PageTitle": "Personalized Content - Omail Publishing - olytics 3.0",
    		"IpAddress": "10.3.14.25",
    		"VisitDate": "2019-01-10 11:35:51",
    		"Url": "https://olytics.omedastaging.com/olytics/staging/omp/example2/index.html",
    		"Browser": "Firefox"
    	},{
    		"OperatingSystem": "Apple",
    		"DeviceType": "Personal computer",
    		"ReferringDomain": "facebook.com",
    		"PageTitle": "Personalized Content - Omail Publishing - olytics 3.0",
    		"IpAddress": "10.3.14.22",
    		"VisitDate": "2019-02-10 11:35:51",
    		"Url": "https://olytics.omedastaging.com/olytics/staging/omp/example2/index.html",
    		"Browser": "Internet Explorer"
    	}]
    }
    

**Table of Contents**

×

