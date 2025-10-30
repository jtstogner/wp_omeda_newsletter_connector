# Content from: https://knowledgebase.omeda.com/omedaclientkb/store-behavior-
attribute

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to create Behavior Attributes as well as add
Defined values for existing Behaviors Attributives.

## General Technical Requirements

### Request URI

CODE

    
    
    For Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/behavior/attribute/*
    
    For Testing: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/behavior/attribute/*
    

### HTTP Headers

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/store-behavior-attribute) for more
details. If omitted, the default content type is application/json.

### Content Type

If omitted, the default content type is **application/json**. JSON
application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

### Supported HTTP Methods

  1. POST : For creating a new Behavior Attribute.

  2. PUT : For adding valid values to an existing Behavior Attribute.

## Field Definition

The following tables describe the data elements that can be sent to this API.
In addition to the below elements, a **SubmissionId** element will also be
returned with all responses. This is a unique identifier for the web services
response. It can be used to cross-reference the response in Omedaâs
database.

### Behavior Elements

Element Name| Required onâ¦| Data Type| Description|  
---|---|---|---|---  
| | | POST req.| PUT req.  
Id| Not allowed| Yes| Integer| Behavior Attribute Identifier  
Description| Yes| Not allowed| String| Description of the Behavior Attribute.  
Type| Yes| Optional| Integer| Type of Attribute. Valid values are 1 (Defined
Values), 2 (Open Text), or 3 (Open Text Number)  
DefinedValues| optional| Yes| Array| Values of Defined Values for Behavior
Attribute (only allowed for Type 1 Behavior Attributes)  
  
#### Defined Values Element

Attribute Name| Required?| Description  
---|---|---  
ValueDescription| Required| Description of value  
  
## Create Behavior Attribute

An HTTP POST **creates** a new Behavior Attribute with optional Defined Values
for a given brand. See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

### Request

POST requests, by their nature, will not have an **Id** element, since POST is
reserved for creating new elements, and the service governs the allocation of
ids.

#### Example POST Request

CODE

    
    
    { 
       "Description":"Article Name",
       "Type":1,
       "DefinedValues":[
           {"ValueDescription":"New Widgets in the Workplace"},
           {"ValueDescription":"Interesting Articles about Widgets"}
        ]
    }
    

#### Example PUT Request2

A PUT request must have an **Id** element, since PUT is reserved for updating
existing Behaviors Attributes.

##### Add New Value Descriptions to an existing Behavior Attribute

CODE

    
    
    { 
       "Id":"2",
       "DefinedValues":[
           {"ValueDescription":"New Widgets in the Workplace"},
           {"ValueDescription":"Interesting Articles about Widgets"}
        ]
    }
    

##### Update the existing Value Descriptions for an existing Behavior
Attributeâs existing Defined Value

CODE

    
    
    { 
       "Id":"2",
       "DefinedValues":[
           {"DefinedValueId":100,
            "ValueDescription":"New Widgets in the Office "
           },
           {"DefinedValueId":101,
             "ValueDescription":"Interesting Articles about Office Widgets"
           }
        ]
    }
    

### Response â Success

Upon successful creation of a behavior attribute, an HTTP 200 will be issued.

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded.  
  
#### Example Response

The response will only return the Items that have been added or updated.

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "ResponseInfo":[
        {
          "Id":2,
          "DefinedValues":[
             {"DefinedValueId":100,
              "ValueDescription":"New Widgets in the Workplace"
             },
             {"DefinedValueId":101,
              "ValueDescription":"Interesting Articles about Widgets"
             }
          ]
        }
      ]
    }
    

### Response â Failure

If an error occurs repeatedly, please contact your Omeda representative.

#### HTTP Response Codes

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
  
#### Example Response

CODE

    
    
    {
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F",
      "Errors" : [
        {
          "Error": "A Behavior Attribute with that name already exists"
        }
      ]
    }
    

#### Possible Error Messages

In the event of an error, an error response will be returned. Here are some of
the possible responses you might receive.

POST Error Messages

CODE

    
    
    The Description Element is required to create a new Behavior Attribute.
    Behavior Attribute {id} with that description already exists.
    The Type Element is required to create a new Behavior Attribute.
    {type} is not a valid value for Type. Valid values are: 1,2,3.
    A Defined Value with that description already exists.
    At least one ValueDescription is required.
    The DefinedValues Array is not allowed for this behavior attribute type.
    

PUT Error Messages

CODE

    
    
    The Description Element cannot be used for an update request.
    BehaviorAttributeId {behaviorAttributeId} was not found.
    The BehaviorAttributeId Element is required to update a Behavior Attribute.
    The DefinedValues Array is required to update a Behavior Attribute.
    Defined Value {id} with that description already exists.
    At least one ValueDescription is required.
    The DefinedValues Array is not allowed for this behavior attribute type.

**Table of Contents**

×

