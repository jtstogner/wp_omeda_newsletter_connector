# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-content

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Content API provides the ability to post information to a
deployment. These fields can include the âSubjectâ line of the email, the
âFrom Nameâ of the email, the HTML content, the Text content, etc. Since
we are passing in html data in this resource, xml is the default format for
requests and responses.

An HTTP POST request is used to manage deployment content.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/content/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id.content-typea content type supported by this resource. See [Supported
Content Types](../omedaclientkb/email-deployment-content) for more details. If
omitted, the default content type is text/xml.

Any foreign or Unicode characters are used in submission data UTF encoding
**must** be explicitly specified in headers:

CODE

    
    
    Content-type: application/xml; charset=UTF-8

## Supported Content Types

If omitted, the default content type is
**application/json**.XMLapplication/xml or text/xml

## Supported HTTP Methods

There is one HTTP method supported:POSTSee [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

POST method is used when attaching content to a deployment or updating
content.

## Field Definition

The following tables describe the hierarchical data elements.

#### Deployment Elements

It is recommended for certain fields that may contain xml-valid characters,
such as Ampersands, that you enclose your data inside a <![CDATA[ ]]> tag.
Such characters, if not enclosed in a CDATA tag, result in invalid xml and
will return an error response.

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
UserId| required| string| UserId of the omail account authorized for this
deployment. This is generally the âOwnerUserIdâ specified in the
Deployment API. This can also be the âOwnerâ field returned on the
Deployment Search Resource.  
TrackId| required| string| TrackId is the unique tracking number for the
deployment the user is updating.  
Splits *| conditional| array| List of attributes for each split if multiple
splits are set/updated in one call. If specified none of following attributes
should appear at the top level.  
SplitNumber| required| integer| The sequential number of split to be
set/updated. The split has its own HTML content, Text content, From Name, etc.  
HtmlContent| conditional| html| The HTML code to assign to the deployment
split.  
TextContent| optional| string| The text to assign to the deployment split.  
HtmlContentUrl| conditional| html| The API will pull the html content from the
url at the time of the API call and assign it to the deployment.  
TextContentUrl| conditional| string| The API will pull the text content from
the url at the time of the API call and assign it to the deployment.  
FromName| conditional: If this element is not passed in with the api call â
you must have arranged a default value in Omail Deployment Defaults with your
omail representative.| string| The text that the displays in the âFromâ
section of the recipientâs inbox.  
MailBox| conditional: If this element is not passed in with the api call â
you must have arranged a default value in Omail Deployment Defaults with your
omail representative.| string| The mailbox portion or the handler of the From
email address that the recipient will see. If your mailbox is
[example@abc.com](mailto:example@abc.com), you would enter example.  
Subject| conditional: If this element is not passed in with the api call â
you must have arranged a default value in Omail Deployment Defaults with your
omail representative.| string| The subject line of the deployment split. This
is what the recipient of the deployment will see in the subject line of the
email.  
ReplyTo| optional: If this element is not passed in with the api call, this
will be populated by the default value set in Omail Deployment Defaults.|
string| The email address where emails are sent when the recipient clicks
âReplyâ to the email.  
Preheader| optional| string| The short summary text that follows the subject
line when viewing an email from the inbox.  
  
  * â âSplitsâtag should be used when itâs desired to set/update content for multiple splits in one API call. All following attributes (âSplitNumberâ to âReplyToâ) must be specified for each split then, see examples below.

### XML Request Example, single split

CODE

    
    
    <?xml version="1.0" encoding="UTF-8"?>
    <Deployment>
       <TrackId>FOO020300102</TrackId>
       <UserId>testaccount</UserId>
       <SplitNumber>1</SplitNumber>
       <FromName><![CDATA[Your Magazine Publisher]]></FromName>
       <Mailbox>publisher</Mailbox>
       <Subject><![CDATA[Renew Today!]]></Subject>
       <ReplyTo>incomingemails@publisher.com</ReplyTo>
       <HtmlContent><![CDATA[<html><body><h1>Subscriber Today!</h1><div>View Our Offers</div></body></html>]]></HtmlContent>
       <TextContent>
           <![CDATA[
           Subscibe Today!
    
            View Our Offers
    
            ]]>
       </TextContent>
    </Deployment>
    
    

### XML Request Example using URLs, multiple splits in one call

CODE

    
    
    <?xml version="1.0" encoding="UTF-8"?>
    <Deployment>
       <TrackId>FOO020300102</TrackId>
       <UserId>testaccount</UserId>
       <Splits>
    	<Split>
              <SplitNumber>1</SplitNumber>
              <FromName><![CDATA[Your Magazine Publisher]]></FromName>
              <Mailbox>publisher</Mailbox>
              <Subject><![CDATA[Renew Today!]]></Subject>
              <ReplyTo>incomingemails@publisher.com</ReplyTo>
              <HtmlContentUrl><![CDATA[https://my.omedastaging.com/portal/verification_test_html.html?param=1&param=2]]></HtmlContentUrl>
              <TextContentUrl><![CDATA[https://my.omedastaging.com/portal/verification_test_text.txt?param=1&param=2]]></TextContentUrl>
            </Split>
    	<Split>
               <SplitNumber>2</SplitNumber>
               <FromName><![CDATA[Your Magazine Publisher]]></FromName>
               <Mailbox>publisher</Mailbox>
               <Subject><![CDATA[Renew Now!]]></Subject>
               <ReplyTo>incomingemails@publisher.com</ReplyTo>
               <HtmlContentUrl><![CDATA[https://my.omedastaging.com/portal/verification_test_html2.html?param=3&param=4]]></HtmlContentUrl>
               <TextContentUrl><![CDATA[https://my.omedastaging.com/portal/verification_test_text2.txt?param=3&param=4]]></TextContentUrl>
    	</Split>
        </Splits>
    </Deployment>
    
    

## Response Examples

Responses possible: a successful POST/PUT (200 OK Status) or a failed POST/PUT
(400 Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses).
See [W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful submission will update the deployment content for the request
deployment split.

Status| Description  
---|---  
200 OK| Please be sure to check the response xml for warnings. A typical
warning would be an invalid link or a missing unsubscribe link.  
  
#### XML Example

CODE

    
    
    <?xml version="1.0" encoding="UTF-8"?>
    <ResponseInfo>
       <TrackId>FOO020300102</TrackId>
       <Url>https://ows.omedastaging.com/webservices/rest/brand/FOO/omail/deployment/lookup/FOO0200300112/*</Url>
       <SubmissionId>C95AE90C-BEC6-41F2-91E2-2BA9168D1D1G</SubmissionId>
       <Warnings>
          <Warning>Invalid link found: 'test.cmo'</Warning>
          <Warning>Invalid link found: 'ww.aol.com'</Warning>
          <Warning>Invalid link found: 'link2'</Warning>
          <Warning>Missing Unsubscribe Link for split 1 in HTML</Warning>
       </Warnings>
    </ResponseInfo>
    
    

### Failed Submission

Potential errors:

CODE

    
    
    Invalid xml. Please validate your xml and verify you have used CDATA tags where necessary.
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    The field '{fieldName}' is required.
    One of the following fields must be set: 'HtmlContent' or 'TextContent'.
    No deployment was found matching trackId '{trackId}'.
    Deployment '{trackId}' cannot be edited. Sent, Scheduled , Approved, or Cancelled deployments cannot be edited.
    UserId '{userId}' is not authorized to edit deployment '{trackId}'"
    Deployment '{trackId}' has been edited from the Omail portal and is not eligible for API access. Last edited by {account} on 2012-02-04 22:15:00.
    Deployment '{trackId}'  was created within the Omail portal and is not eligible for API access.
    TextContent should not contain html.
    HtmlContent must have open and closed html and body tags.
    

A failed submission may be due to several factors:

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
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    <?xml version="1.0" encoding="UTF-8"?>
    <ResponseInfo>
       <SubmissionId>C95AE90C-BEC6-41F2-91E2-2BA9168D1D1G</SubmissionId>
       <Errors>
          <Error>'SplitNumber' is a required field.</Error>
          <Error>'FromName' is a required field.</Error>
       </Errors>
    </ResponseInfo>
    

**Table of Contents**

×

