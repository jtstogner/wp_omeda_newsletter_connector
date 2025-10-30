# Content from: https://knowledgebase.omeda.com/omedaclientkb/postal-info-
lookup

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Postal Information API returns postal information for a given postal code.
The postal data is updated as available from the USPS, and field definitions
can be found here:

<http://www.zip-codes.com/zip_database_fields.asp>

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/postalinfo/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/postalinfo/*
    

brandAbbreviationis the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/postal-info-lookup) for more details. If
omitted, the default content type is application/json.

## Supported Content Types

There are three content types supported. If omitted, the default content type
is **application/json**.

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

## Field Definition

The following tables describe the data elements that can be included in the
POST method to lookup data in the database.

### PostalInfo Element

Attribute Name| Required?| Description  
---|---|---  
PostalInfo| required| Array element containing one postal code element (see
[below](../omedaclientkb/postal-info-lookup)).  
  
### Postal Info Element

Attribute Name| Required?| Description  
---|---|---  
ZipCode| required| The zip code for which you are requesting information
about. Field definitions found here: <http://www.zip-
codes.com/zip_database_fields.asp>  
  
## Request Examples

### JSON Example

CODE

    
    
    {
        "PostalInfo": [
            {
                "ZipCode": 60062
            } 
        ]
    }
    

## Response Examples

Two responses are possible: a successful POST (200 OK Status) or a failed POST
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful POST submission will return postal information for the given zip
code. This will require parsing to retrieve the information you require.

#### JSON Example

CODE

    
    
    {
       "PostalInformation":[
          {
             "zipCode":"60062",
             "primaryRecord":"P",
             "population":40392,
             "householdsPerZipcode":15445,
             "whitePopulation":35220,
             "blackPopulation":283,
             "hispanicPopulation":873,
             "asianPopulation":4237,
             "hawaiianPopulation":3,
             "indianPopulation":15,
             "otherPopulation":203,
             "malePopulation":19503,
             "femalePopulation":20889,
             "personsPerHousehold":2.63,
             "averageHouseValue":361100,
             "incomePerHousehold":89164,
             "latitude":42.11997400,
             "longitude":-87.84092200,
             "elevation":596,
             "state":"IL",
             "stateFullName":"Illinois",
             "cityType":"P",
             "cityAliasAbbreviation":"",
             "areaCode":"224/312/630/708/847",
             "city":"NORTHBROOK",
             "cityAliasName":"NORTHBROOK",
             "county":"COOK",
             "countyFips":"031  ",
             "stateFips":"17",
             "timeZone":"6 ",
             "dayLightSaving":"Y",
             "msa":"1602",
             "pmsa":"1600",
             "csa":"176",
             "cbsa":"16980",
             "cbsaDiv":"16974",
             "cbsaType":"Metro",
             "cbsaName":"Chicago-Naperville-Joliet IL-IN-WI",
             "msaName":"Chicago-Gary-Kenosha IL-IN-WI CMSA",
             "pmsaName":"Chicago IL PMSA",
             "region":"Midwest",
             "division":"East North Central",
             "mailingName":"Y",
             "preferredLastLineKey":"W14201",
             "classificationCode":" ",
             "multiCounty":" ",
             "csaname":"Chicago-Naperville-Michigan City, IL-IN-WI",
             "cbsaDivName":"Chicago-Joliet-Naperville, IL",
             "cityStateKey":"W14201",
             "cityAliasCode":"",
             "cityMixedCase":"Northbrook",
             "cityAliasMixedCase":"Northbrook",
             "stateAnsi":"17",
             "countyAnsi":"031"
          }
       ]
    }

**Table of Contents**

×

