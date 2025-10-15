# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-add-audience

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

The Deployment Add Audience API provides the ability add a previously uploaded
list of customers to a deployment (See [Deployment Audience List
FTP](../omedaclientkb/email-deployment-audience-list-ftp) for information on
uploading a list into the Email Builder system).

Your list must have a unique column containing email addresses. The header for
this column must be one of the following (not case sensitive): âemailâ,
âemail_addressâ, âemail-addressâ, or âemailaddressâ. Multiple
email headers are not allowed. For example, if your file had a column
âemail_addressâ and another column named âemailâ, than you would
receive an error.

Calling this service will trigger a list assignment process and return a
unique âListIdâ, as well as a Url that can be used to monitor the status
of the list assignment process (See [Audience Assignment Status
API](../omedaclientkb/email-audience-assignment-status)).

Recipient List Restrictions:

An audience list name cannot be used more than once for a single deployment.
If you assign an audience list to a deployment, remove the the list, and try
to re-add a new list with the same name, you will get an error. The
appropriate series of events would be to remove the list from the deployment,
change the list appropriately, and then give that list a new unique name
before adding the list to the deployment. This is a security measure to insure
deployment recipient list accuracy.

Recipient lists must be appended a timestamp of the format
â_yyyyMMdd_HHmmssâ. Examples of valid file names would be
âsubscriberlist1_20120809_064500.csvâ and
âsubscriber_list_1_20120809_164500.txtâ. When [uploading a new
file](../omedaclientkb/email-deployment-audience-list-ftp) programmatically,
you will need to append the current date and time to your file name before
uploading it to your Omail FTP folder. This is a security measure to insure
deployment recipient list accuracy.

An HTTP POST request is used when assigning a list to a deployment.

## Base Resource URI

CODE

    
    
    For Production, use: https://ows.omeda.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/add/*
    
    For Testing, use: https://ows.omedastaging.com/webservices/rest/brand/{brandAbbreviation}/omail/deployment/audience/add/*
    

brandAbbreviation is the abbreviation for the brand to which the data is being
posted.

## Technical Requirements

The HTTP header must contain the following elements: x-omeda-appid a unique id
provided to you by Omeda to access your data. The request will fail without a
valid id. content-type a content type supported by this resource. See
[Supported Content Types](../omedaclientkb/email-deployment-add-audience) for
more details. If omitted, the default content type is application/json.

## Supported Content Types

If omitted, the default content type is **application/json**. JSON
application/json

JSON is the preferred data exchange format, because it is lightweight and, in
most cases, faster to process and utilizes less bandwidth. There are many
available open-source JSON libraries available. See
[json.org](http://www.json.org/) for details.

## Supported HTTP Methods

There is one HTTP method supported: POST See [W3Câs POST
specs](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) for
details.

POST method is used when assigning a list to a deployment split that does not
have an existing list attached.

## Field Definition

The following tables describe the hierarchical data elements.

#### List Elements

Attribute Name| Required?| Data Type| Description  
---|---|---|---  
UserId| required| string| UserId of the omail account authorized for this
deployment. This is generally the âOwnerUserIdâ specified in the
[Deployment Api](../omedaclientkb/email-deployment)  
TrackId| required| string| TrackId is the unique identifier for the
deployment. NOTE for testing: the Staging environment has different TrackIds
than the production environment.  
ApplyBrandLevelDefaultSuppressions| optional| byte| 0 = Do not apply default
brand level suppressions. 1 = Apply default brand level suppressions. For
digital deployments the default value is â0â and the field does not need
to be included in your API call. For non-digital deployments the field is
allowed and if it is not specified will default to â1â (on).  
Audience*| conditional| array| List of attributes for each audience list if
multiple Add Audience requests are sent in one call. If specified â none of
following attributes should appear at the top level of JSON data. When using
multiple list API call â all referenced splits must be already created by
the time of call.  
RecipientList| conditional| string| The name of the list to attach to the
deployment. Only lists that have been uploaded into the [Deployment Audience
List FTP](../omedaclientkb/email-deployment-audience-list-ftp) are available.
List must be either .csv or .txt format and must be comma-delimited. Either
RecipientList or QueryName must be specified in your API call.  
OmailOutput| optional| string| The name of the list to attach to the
deployment. Omail Outputs will end with a .csv file extenstion. Either
RecipientList, OmailOutput, or QueryName must be specified in your API call.  
ListNumber| optional| integer| If not specified â uploaded list is the first
and only list on the split designated by SplitNumber attribute. If specified
â uploaded list will be appended to existing list on the split. Valid Values
are 1 to number of existing lists in deployment.  
QueryName| conditional| string| The name of the customer query to attach to
the deployment. Is used in conjunction with OutputCritera to determine what
fields will be made available for each customer in your query. Either
RecipientList or QueryName must be specified in your API call.  
OutputCriteria| conditional| string| The name of the output criteria to be
used in conjunction with the Audience Builder query. If âQueryNameâ is
specified in the api call, and OutputCriteria is not, the default output
criteria will be used. The default output criteria is: âcustomer_idâ,
âemailâ, first_name, last_name, âtitleâ, âcompany_nameâ,
âphoneâ,âstreet_1â², âstreet_2â, âcityâ,
âstate_province_codeâ, âzip_postal_codeâ, âcountryâ,
âemail_address_idâ, and âencrypted_customer_idâ.You can define
reusable custom output criteria in the Audience Builder output screen.  
SplitNumber| required| integer| The split number the list will be assigned to.
Can be one more than number of splits in deployment in which case next split
will be added to deployment automatically.  
RemoveDuplicates| optional| byte| 0 = Do not remove duplicate emails from the
list, 1 = Remove duplicate emails from the list. Not required and ignored when
ListNumber attribute is specified (all lists in combined list have the same
RemoveDuplicates setting).  
SuppressHardBounces| optional| byte| 0 = Do not suppress emails by hard bounce
filter, 1 = Suppress emails in the list by hard bounce filter (default
behavior)  
CallbackURLs| optional| array| You may opt to provide an array of webhook
urls. When your list or query is finished uploading, our system will make an
http request to those URL(s). If the first callback attempt fails, we will
retry an additional 2 more times, each at 2 minute intervals for a total of 3
attempts. The JSON payload we pass to the webhook URL will have the same
format as the [Assignment Status Service](../omedaclientkb/email-audience-
assignment-status). Please note that both DONE and WARNING can be considered
âsuccessfulâ statuses for the upload.  
  
  * âAudienceâ attribute should be used to send multiple Add Audience requests in one API call. All following attributes (âRecipientListâ to âSuppressHardBouncesâ) must be specified separately for each audience then, see examples below.

## Request Examples

### POST JSON Request Example: When adding an a single audience list to a
deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "RecipientList": "customer_list_april_2017_20170418_143500.csv",
        "SplitNumber": 1,
        "RemoveDuplicates": 1
    }
    

### POST JSON Request Example: When adding an a single Omail Output to a
deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "OmailOutput": "customer_list.csv",
        "SplitNumber": 1,
        "RemoveDuplicates": 1
    }
    

### POST JSON Request Example: When adding an a single Query to a deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "QueryName": "My Audience Builder Query",
        "OutputCriteria": "OmailOutput1",
        "SplitNumber": 1,
        "RemoveDuplicates": 1,
        "SuppressHardBounces": 1
    }

### POST JSON Request Example: When adding an a single Query to a deployment
and specifying a callback url

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "QueryName": "My Audience Builder Query",
        "OutputCriteria": "OmailOutput1",
        "SplitNumber": 1,
        "RemoveDuplicates": 1,
        "SuppressHardBounces": 1,
        "CallbackURLs": ["https://mycallbackurl.com/test/whatever/"]
    }

### POST JSON Request Example: When adding multiple lists to a deployment

CODE

    
    
    {
        "UserId" : "omailAccount1",
        "TrackId": "FOO0102003002",
        "Audience" : [
    	{
    	    "RecipientList": "customer_list_april_2012.csv",
    	    "SplitNumber": 1,
    	    "RemoveDuplicates": 1,
                "SuppressHardBounces": 0
    	},
    	{
    	    "QueryName": "My Audience Builder Query",
    	    "OutputCriteria": "OmailOutput1",
    	    "SplitNumber": 2,
    	    "RemoveDuplicates": 0
    	},
    	{
    	    "RecipientList": "customer_list_april_2012_more.csv",
    	    âListNumberâ: 1,
    	    "SplitNumber": 1
    	}
        ]
    }
    

## Response Examples

Responses possible: a successful POST (200 OK Status) or a failed POST (400
Bad Request/403 Forbidden/404 Not Found/405 Method Not Allowed Statuses). See
[W3Câs Status
Codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

### Successful Submission

A successful submission will trigger a either list assignment process, for
both a recipient list and a query. In the case of a recipient list, the list
assignment process will retrieve the list from the Omail [Deployment List
FTP](../omedaclientkb/email-deployment-audience-list-ftp) site, process it
according to specified JSON submitted fields, and assign it to the appropriate
deployment split. In the case of a query, the list assignment process will
output the fields specified in âOutputCriteriaâ and assign those customers
to your deployment. A successful POST submission will return a url to call the
[Audience Assignment Status API](../omedaclientkb/email-audience-assignment-
status), the unique ListId for the list being assigned, a unique SubmissionId,
and the TrackId for the deployment specified. In case of multiple lists
submission call â the return url and unique ListId for each list being
assigned and returned. In case of multiple lists submission â the url to
check assignment status and unique ListId for each list being assigned are
returned.

#### JSON Example, single list submission

CODE

    
    
    {
       "TrackId": "FOO0200300112",
       "ListId": "1000343",
       "Url": "https://ows.omeda.com/webservices/rest/brand/FOO/omail/deployment/audience/status/1000343/*",
       "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E"
    }
    

#### JSON Example, multiple list submission

CODE

    
    
    {
       "TrackId": "FOO0200300112",
       "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1E",
       "ResponseInfo": [
         {
           "ListId":"1000343",
           "Url": "https://ows.omeda.com/webservices/rest/brand/FOO/omail/deployment/audience/status/1000343/*",
         },
         {
           "ListId":"1000347",
           "Url": "https://ows.omeda.com/webservices/rest/brand/FOO/omail/deployment/audience/status/1000347/*",
         }
       ]
    }
    

### Failed Submission

Potential errors:

CODE

    
    
    The value '{stringField}' for field '{fieldName}' exceeded a max length of {maximumAllowed}.
    '{RequiredFieldName}' is a required field.
    The Duplicate value '{emailAddress}' submitted for Testers array, field 'EmailAddress'. Tester emails must be unique.
    No deployment was found matching trackId '{trackId}'.
    Deployment '{trackId}' cannot be edited. Sent, Scheduled , Approved, or Cancelled deployments cannot be edited.
    UserId '{ownerUserId}' is not authorized to edit deployment '{trackId}'"
    Deployment 'FOO09030021' has been edited from the Omail portal and is not eligible for API access. Last edited by omailAccount2 on 2012-02-04 22:15:00.
    Deployment 'FOO09030021'  was created within the Omail portal and is not eligible for API access."
    Recipient list {listname} is not a valid file type. Valid file types are .csv and .txt
    The following brand subdirectory : '\\{brandAbbreviation}' does not exist in your Omail ftp folder. Files must be placed in the appropriate brand subdirectory in order to be processed.
    We could not find the following file : '"\\{brandAbbreviation}\\{fileName} in your Omail ftp folder.
    Please verify that your file has been placed in the proper brand folder in your Omail FTP folder.
    Split {splitNumber} does not exist for deployment {trackId}. Deployment '{trackId}' has only 1 split.
    Split 1 already has recipient list '{listName}' assigned on {assignmentDate}. You must first remove '{listName}' before assigning a list to split 1.
    A recipient list with the name '{listName}' has been used previously for this deployment on {assignmentDate}.
    Recipient list '{listName}' does not have a valid email header. Valid headers are 'email', 'email_address', 'email-address', and 'emailaddress'.
    Recipient list '{listName}' has more than one email header defined: '{firstEmailHeader}','{secondEmailHeader}'.
    Value '1' for field 'ApplyBrandLevelDefaultSuppressions' is not allowed for digital deployments.
    
    

A failed submission may be due to several factors:

Status| Description  
---|---  
400 Bad Request| Typically, this error occurs when the request does not follow
the specifications. An example would be if a call is made to add a list to a
split that already has a list assigned.  
403 Forbidden| Typically, this error occurs when the credentials are
erroneous. Potentially, an incorrect x-omeda-appid.  
404 Not Found| Typically, this error occurs with a malformed URL or the
resource that is searched for is not found. This can occur if a TrackId
submitted is not found in our system or the requested list is not found on the
Email Builder FTP site in the appropriate folder.  
405 Method Not Allowed| Typically, this error occurs when the resource
accessed is not allowed by the HTTP Method utilized. Make sure you employ the
appropriate HTTP Method (POST or PUT) for this request.  
  
This is not an exhaustive list of errors, but common ones. If an error occurs
repeatedly, please contact your Omeda representative.

#### JSON Example

CODE

    
    
    {
      "Errors" : [
        {
          Error": "You must first remove 'customerlist2.csv' before a new recipient list can be added to split 1." 
        },
        {
          Error":"Recipient list 'zde_emails1.csv' does not have a valid email header. Valid headers are 'email', 'email_address', 'email-address', and 'emailaddress'."
        },
        {
          "Error": "Recipient list 'customerlist1.csv' was not found in brand folder 'FOO' in the Email Builder FTP site."
        },
        {
          "Error": "Recipient list 'customerlist1.xls' is not a valid file type. Valid file types are .csv and .txt"
        }
      ],
      "SubmissionId" : "C95AE90C-BEC6-41F2-91E2-2BA9168D1D1F"	
    }

**Table of Contents**

×

