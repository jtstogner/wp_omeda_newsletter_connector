# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-
deployment-audience-list-ftp

[**Knowledge Base Home**](../omedaclientkb/)

## Summary

Please contact your Omeda representative for FTP credentials.

Deployment List FTP is the means by which deployment lists are made available
in the Email Builder system. Lists are uploaded to a pre-configured, password-
protected FTP folder. Lists should be placed in the appropriate brand level
folder.

### FTP Work Flow:

  1. User drops the audience list into their Omeda password protected FTP folder, inside the appropriate brand folder.

Recipient lists must be appended a timestamp of the format
â_yyyyMMdd_HHmmssâ. Examples of valid file names would be
âsubscriberlist1_20120809_064500.csvâ and
âsubscriber_list_1_20120809_164500.txtâ. When uploading a new file
programmatically, you will need to append the current date and time to your
file name before uploading it to your Email Builder FTP folder. This is a
security measure to insure deployment recipient list accuracy.

  2. 

A. If the file does not exist in the system already (A file with the same name
has not been previously used), then the file will be moved to an internal
location, no longer visible on the userâs FTP folder. An email will be sent
to a pre determined client email address with a notification of the successful
transfer.

B. If the file already exists internally, then the file will be picked up and
deleted. It will no longer be visible on the userâs FTP folder. An email
will be sent to a pre determined client email address with a notification of
the failure.

  3. Once a list is uploaded to the FTP folder and transfers successful, the list can be assigned to a deployment via the 

[Deployment Add Audience API](../omedaclientkb/email-deployment-add-audience).

## Sample folder for Recipient List

CODE

    
    
       Staging: sftp.omeda.com/{brandAbbreviation}/ (Using Staging Credentials)
       Production: sftp.omeda.com/{brandAbbreviation}/ (Using Production Credentials)
    

brandAbbreviation is the abbreviation for the brand to which the data is being
uploaded.

## Technical Requirements

Valid list types are .txt and .csv. At a minimum, the list must contain a
column with email address information. The header for this column must contain
the word âemailâ and cannot contain the word âdomainâ. Some valid
header names for the email address column are : âemail_addressâ, âemail
addressâ, âemailâ, and âemailaddressâ. Please use the ftp username
and ftp account name provided to you by Omeda to access your ftp folder.

**Table of Contents**

×

