# Content from: https://knowledgebase.omeda.com/omedaclientkb/olytics-
comprehensive-lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

An api available to our [olytics](../omedaclientkb/cdp-olytics-overview)
clients.

For a given global database, this API can be used to return all of the active
olytics fields and their valid values on the database. These are the fields /
values that the user has been passing to us in their olytics.fire() API calls.

This api can be used to help in building an external lead gen tool.

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/olytics/comp/*
    
    Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/olytics/comp/*
    
    

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

There is one HTTP method supported: GET See [W3Câs GET
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for
details.

## Look Up Olytics Attribute Types / Attribute Values

Retrieves the olytics fields (BehaviorAttributeTypes) and valid values
(BehaviorAttributeValues) for a given global database

### Field Definition

The following tables describe the hierarchical data elements.

In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

#### Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
BehaviorAttributeTypes| no| array| an array containing the behavior attribute
type elements.  
  
##### BehaviorAttributeTypes Elements

Element Name| Optional?| Data Type| Description  
---|---|---|---  
BehaviorAttributeType| no| string| The name of the field (behavior attribute
type). Some common values would be âOlytics Categoryâ or âOlytics
Tagâ.  
BehaviorAttributeValues| no| array| An array of the open-text fields that have
been passed to us from the olytics.fire() function for the given Behavior
Attribute Type.  
  
#### Success Example

CODE

    
    
    {
    	"BehaviorAttributeTypes": [{
    		"BehaviorAttributeType": "Olytics Category",
    		"BehaviorAttributeValues": [{
    			"BehaviorAttributeValue": "Omeda Website"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article"
    		}, {
    			"BehaviorAttributeValue": "OX1 Registration"
    		}]
    	}, {
    		"BehaviorAttributeType": "Olytics Tag",
    		"BehaviorAttributeValues": [{
    			"BehaviorAttributeValue": "Omeda Website | About Page"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website | Contact Us Page"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website | Services Page"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website | Home Page"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article | Omeda Introduces Olytics"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website | Privacy Policy Page"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article | PCMA chooses Omeda"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article | GEN chooses Omeda"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article | Omeda acquires Sunbelt Fulfillment"
    		}, {
    			"BehaviorAttributeValue": "Omeda Website News Article | Omeda acquires Hallmark"
    		}, {
    			"BehaviorAttributeValue": "Registration Form"
    		}, {
    			"BehaviorAttributeValue": "Registration Form Confirmation Page"
    		}]
    	}],
    	"SubmissionId": "37E470CD-E010-4233-8907-A889ABB387AB"
    }

**Table of Contents**

×

