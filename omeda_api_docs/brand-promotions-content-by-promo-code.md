# Content from: https://knowledgebase.omeda.com/omedaclientkb/brand-
promotions-content-by-promo-code

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

This API provides capabilities to retrieve information about a Brands Single
Promotion Content by Promo Code .

## General Technical Requirements

The following technical requirements apply to all requests for this API.

### Base Resource URI

CODE

    
    
    Production: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/promotioncontent/promocode/{promotionCode}/* 
    
    Testing:    https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/promotioncontent/promocode/{promotionCode}/* 
    

brandAbbreviation is the abbreviation for the brand promocode is the promotion
code

### HTTP Headers

The HTTP header must contain the following element: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.

### Content Type

The content type is **application/xml**. XML application/xml

### Supported HTTP Methods

There is one HTTP method supported:

  1. GET : See [W3Câs GET specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) for details.

## Lookup All Promotion Content

Retrieves all defined Promotion Content for the brand.

### Field Definition

The following table describes the data elements present on the response from
the API. In addition to the below elements, a **SubmissionId** element will
also be returned with all responses. This is a unique identifier for the web
services response. It can be used to cross-reference the response in Omedaâs
database.

#### Brand Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| Integer| The brand identifier.  
Description| Yes| String| The name of the brand.  
BrandAbbrev| Yes| String| The abbreviation for the brand (used in most web
service URLs).  
PromotionContent| Yes| List| List of PromotionContent Elements attached to
this Brand.  
  
##### PromotionContent Elements

Element Name| Always Returnedâ¦| Data Type| Description  
---|---|---|---  
Id| Yes| int| The promotion content identifier.  
ContentKey| Yes| String| Name of the promotion content.  
PromoCode| Yes| String| The Promotion that this content is associated with.  
StatusCode| Yes| int| The status of the promotion content.  
HtmlContent| Yes| String| The HTML content associated with this content Key.  
Layout| Yes| String| The layout of the content on the page. (Top, Bottom,
Left, Right)  
  
### Response

#### HTTP Response Codes

Status| Description  
---|---  
200 OK| The request has succeeded. See **Example Response** below.  
404 Not Found| In the event no Promotions are found, an HTTP 404 (not found)
response will be returned.  
  
#### Example Response

CODE

    
    
    <?xml version="1.0" encoding="UTF-8"?>
    <Brand>
       <SubmissionId>f202c56d-328c-408b-80a1-44806287242d</SubmissionId>
       <Id>12345</Id>
       <Description>XYZ Brand</Description>
       <BrandAbbrev>XYZ</BrandAbbrev>
       <PromotionContents>
          <PromotionContent>
             <Id>1</Id>
             <ContentKey>FREEREG</ContentKey>
             <PromoCode>REG</PromoCode>
             <StatusCode>2</StatusCode>
             <HtmlContent>
                <td> this is the content</td>
             </HtmlContent>
             <ContentLayout>top</ContentLayout>
          </PromotionContent>
          <PromotionContent>
             <Id>2</Id>
             <ContentKey>REGFREE</ContentKey>
             <PromoCode>REG</PromoCode>
             <StatusCode>1</StatusCode>
             <HtmlContent>
                <td> this is the other content</td>
             </HtmlContent>
             <ContentLayout>top</ContentLayout>
          </PromotionContent>
       </PromotionContents>
    </Brand>

**Table of Contents**

×

