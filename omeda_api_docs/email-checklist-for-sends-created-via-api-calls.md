# Content from: https://knowledgebase.omeda.com/omedaclientkb/email-checklist-
for-sends-created-via-api-calls

[**Knowledge Base Home**](../omedaclientkb/)

## [Create the deployment](../omedaclientkb/email-deployment)

  * API call returns a TrackId that will be needed for subsequent calls.

## [Add an audience to the Deployment](../omedaclientkb/email-deployment-add-
audience)

  * This API kicks off the loading of an Audience Builder Query to a deployment. This loading generally takes anywhere from 5-30 seconds. This API call will return immediately, and in the response will be ListId and ListUrl fields. The client will then use that ListURL to poll the Email Audience Assignment Status API (#3 next).*** the actual query name will be provided by your Audience Director. Please reach out to your Account Manager if assistance is needed.

## [Email Audience Status API](../omedaclientkb/email-audience-assignment-
status)

(poll every 5 or 10 seconds until audience has been fully assigned)

  * Use the ListUrl from the Email Deployment Add Audience API and query the API until the AssignmentStatusCode field in the response json is either DONE or WARNING. Please be sure that your tool considers WARNING as a successful status. Often, the API will return a WARNING status if there are a few emails that were deemed invalid or incorrectly formatted.

## [Email Deployment Content API](../omedaclientkb/email-deployment-content)

  * Note that this particular API only accepts XML.Please note in the examples how we wrap the various fields inside cdata nodes.

## [Email Deployment Test](../omedaclientkb/email-deployment-test)

  * Sends a test deployment to those designated as testers or seeds for the deployment type.Testers or seeds can be passed when the deployment is created, or what is more common is that the client maintains the required test and seednames in Email Builder -> Tools -> Deployment Defaults.

## [Email Deployment Schedule](../omedaclientkb/email-deployment-schedule)

  * The schedule date passed in must be in the future and the time zone is always CST.If you read the knowledge base document, you can see that you can pass in [NOW] as the scheduled date if you wish for the deployment to go out immediately.

## [Deployment Unschedule API](../omedaclientkb/email-deployment-unschedule)

  * If you find you need to cancel your deployment, the Deployment Unschedule API is available.

**Table of Contents**

**Table of Contents**

×

