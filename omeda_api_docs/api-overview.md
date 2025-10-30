# Content from: https://knowledgebase.omeda.com/omedaclientkb/api-overview

[**Knowledge Base Home**](../omedaclientkb/)

## Customer Lookup

The following APIs allow you to lookup various types of customer data on your
database.

[ Behavior Attribute Lookup by Customer Id â The behavior attribute lookup
API call returns all behavior attributes (regular and olytics) information for
a specified customer, behavior, and date range. ](../omedaclientkb/behavior-
attribute-lookup-by-customer-id "Behavior Attribute Lookup by Customer Id")

[ Behavior Lookup By Customer Id â The behavior lookup API call returns
behavior information for a specified customer. Behavior information can be
requested for a specific behavior OR for behaviors associated with a specific
product OR all behaviors ](../omedaclientkb/behavior-lookup-by-customer-id
"Behavior Lookup By Customer Id")

[ Customer Change Lookup â This service returns a list of Customer Ids for
Customers that were changed within a given date range. The date range cannot
exceed 90 days. ](../omedaclientkb/customer-change-lookup "Customer Change
Lookup")

[ Customer Comprehensive Lookup By Customer Id â This API provides
capabilities to retrieve the comprehensive information about a single customer
using the Customer Id. ](../omedaclientkb/customer-comprehensive-lookup-by-
customer-id "Customer Comprehensive Lookup By Customer Id")

[ Customer Lookup By Customer Id â This API provides the ability look up a
Customer by the Customer id. The response will include basic Customer
information and various links to look up additional Customer information such
as Demographics, Addresses, etc for a single Customer record.
](../omedaclientkb/customer-lookup-by-customer-id "Customer Lookup By Customer
Id")

[ Customer Lookup By Email Address â This API provides the ability look up
customers using Email Address and an optional Product Id. The response will
include a list of customer records including the Customer Id(s) and the
Customer Lookup URL(s). ](../omedaclientkb/customer-lookup-by-email-address
"Customer Lookup By Email Address")

[ Customer Lookup By EncryptedCustomerId â This API provides the ability
look up a Customer by the Encrypted Customer id. The response will include
basic Customer information and various links to look up additional Customer
information such as Demographics, Addresses, etc for a single Customer record.
](../omedaclientkb/customer-lookup-by-encryptedcustomerid "Customer Lookup By
EncryptedCustomerId")

[ Customer Lookup By External ID â This API provides the ability look up a
Customer by the External Customer Id. The response will include Customer Id
and various links to look up additional Customer information such as
Demographics, Addresses, etc for a single Customer record.
](../omedaclientkb/customer-lookup-by-external-id "Customer Lookup By External
ID")

[ Customer Lookup by Hashed Email Address â This API provides the ability
look up customers using their Hashed Email Address. The response will include
a list of customer records including the Customer Id(s) and the Customer
Lookup URL(s). ](../omedaclientkb/customer-lookup-by-hashed-email-address
"Customer Lookup by Hashed Email Address")

[ Customer Lookup By Name and Country â This API provides the ability look
up customers using First Name (or initial), Last Name, Country and an Optional
Postal Code. The response will include a list of customer records including
the Customer Id(s) and the Customer Lookup URL(s).
](../omedaclientkb/customer-lookup-by-name-and-country "Customer Lookup By
Name and Country")

[ Customer Lookup By PostalAddressId â This API provides the ability look up
customers using their Postal Address Id (this is the ID on the magazine
mailing labels). The response will include Customer Id and various links to
look up additional Customer information such as Demographics, Addresses, etc
for a single Customer record. ](../omedaclientkb/customer-lookup-by-
postaladdressid "Customer Lookup By PostalAddressId")

[ Customer Merge History Lookup â This API provides capabilities to retrieve
the merge history for the requested Customer Id. ](../omedaclientkb/customer-
merge-history-lookup "Customer Merge History Lookup")

[ Demographic Lookup By Customer Id â This service returns all available
customer demographics stored for a given customer by using the Customer ID.
](../omedaclientkb/demographic-lookup-by-customer-id "Demographic Lookup By
Customer Id")

[ Email Address Lookup By Customer Id â This API provides the ability look
up a Customerâs Email Addresses by the Customer Id. This service returns all
active email address information stored for the given customer.
](../omedaclientkb/email-address-lookup-by-customer-id "Email Address Lookup
By Customer Id")

[ Email Validity Lookup API â This API provides the ability look up the
validity of an email address using Email Address. The response will include
the information regarding the email validity provided by the email validity
vendor for a given email address. ](../omedaclientkb/email-validity-lookup-api
"Email Validity Lookup API")

[ External ID Lookup By Customer Id â This API provides the ability look up
a Customerâs External Ids by the Customer Id. ](../omedaclientkb/external-
id-lookup-by-customer-id "External ID Lookup By Customer Id")

[ Gift Lookup by Donor ID â This service returns all available gift
information where a given Customer Id is the Donor ID and it can be filtered
with optional Product Id. ](../omedaclientkb/gift-lookup-by-donor-id "Gift
Lookup by Donor ID")

[ Order History Lookup by Customer Id â This API provides the ability look
up all available Order History information for a customer by the Customer id
or for a specific product if the Product Id is included. The response will
include the http reference to the owning customer resource and Order History
details with all purchase information. ](../omedaclientkb/order-history-
lookup-by-customer-id "Order History Lookup by Customer Id")

[ Phone Lookup By Customer Id â This API provides the ability look up a
Customerâs Phone Numbers by the Customer id. ](../omedaclientkb/phone-
lookup-by-customer-id "Phone Lookup By Customer Id")

[ Postal Address Lookup By Customer Id â This API provides the ability look
up a Customerâs Address by the Customer Id. The response will return all
active addresses stored for a given customer. ](../omedaclientkb/postal-
address-lookup-by-customer-id "Postal Address Lookup By Customer Id")

[ Subscription Lookup By Customer Id â This service returns all available
subscription information stored for a given Customer Id and optional Product
Id. Note, this includes both current subscription and deactivated
subscriptions (see below to determine the differences).
](../omedaclientkb/subscription-lookup-by-customer-id "Subscription Lookup By
Customer Id")

[ Subscription Lookup By Email â This service returns all subscription
information stored for all customers with the given Email Address and optional
Product Id. Note, this includes both current subscription and deactivated
subscriptions (see below to determine the differences).
](../omedaclientkb/subscription-lookup-by-email "Subscription Lookup By
Email")

[ Order Issue History Lookup by Customer Id â This API allows retrieval of
Order History information by Customer id or Product Id, providing details like
purchase history and issue details. ](../omedaclientkb/order-issue-history-
lookup-by-customer-id "Order Issue History Lookup by Customer Id")

## Store Customer Info

These services allow you to store customer and orders in your database.

[ Assign Behavior â The Assign Behavior API provides the ability to add or
update behavior information for an existing customer.
](../omedaclientkb/assign-behavior "Assign Behavior")

[ Assign Behavior Bulk â This API provides capabilities to create many
Behaviors defined for a given brand to multiple customers.
](../omedaclientkb/assign-behavior-bulk "Assign Behavior Bulk")

[ Save Customer and Order â This API provides the ability to post a complete
set of customer identity, contact, and demographic information along with
order information for data processing (insert/update).
](../omedaclientkb/save-customer-and-order "Save Customer and Order")

[ Save Customer and Order Paid â This API provides the ability to post a
complete set of customer identity, contact, and demographic information along
with order (paid or controlled) information for data processing
(insert/update). ](../omedaclientkb/save-customer-and-order-paid "Save
Customer and Order Paid")

[ Transaction Lookup â The Transaction Lookup service is used to check on
the submission status of a particular POST submission from DataQueue.
](../omedaclientkb/transaction-lookup "Transaction Lookup")

[ Update Billing Info â This API provides the ability to update the Billing
Information for a Single Customer and a Single Paid Product. This service will
look up all the active, pending, and graced Paid Subscription records for the
given customer and product. For each one, it will update the Billing Info
associated. ](../omedaclientkb/update-billing-info "Update Billing Info")

## Brand Lookup

The following APIs allow you to lookup various types of brand level data on
your database.

[ Behavior Actions Lookup â This API provides capabilities to retrieve
Behavior âActionsâ defined for a given brand. ](../omedaclientkb/behavior-
actions-lookup "Behavior Actions Lookup")

[ Behavior Categories Lookup â This API provides capabilities to retrieve
the Behavior Categories defined for a given brand.
](../omedaclientkb/behavior-categories-lookup "Behavior Categories Lookup")

[ Behavior Lookup â This API provides capabilities to retrieve the Behaviors
defined for a given brand. For example, you might use it to keep your
marketing database at Omeda in sync with your content taxonomy category.
](../omedaclientkb/behavior-lookup "Behavior Lookup")

[ Behavior Lookup By Id â This API provides capabilities to retrieve a
single Behavior. You would use this service to look up a behavior using the
Behavior Id. ](../omedaclientkb/behavior-lookup-by-id "Behavior Lookup By Id")

[ Behavior Lookup Grouped By Product â This API provides capabilities to
retrieve the Behaviors for the associated Brand grouped by Product. For
example, you might use it to keep your marketing database at Omeda in sync
with your content taxonomy category. ](../omedaclientkb/behavior-lookup-
grouped-by-product "Behavior Lookup Grouped By Product")

[ Brand Comprehensive Lookup Service â This API provides capabilities to
retrieve information about a single brand, including its defined products,
demographics, deployment types, and other cross referencing information. This
service is useful for building your own data mapping service when reading or
writing from/to other Omeda services. Results from this API should be cached
and then refreshed at an interval by the user. This API is not intended to be
hit real time by web traffic or otherwise hit with a great frequency.
](../omedaclientkb/brand-comprehensive-lookup-service "Brand Comprehensive
Lookup Service")

[ Brand Group Lookup â This API provides capabilities to retrieve the
Information defined for a given GroupId. ](../omedaclientkb/brand-group-lookup
"Brand Group Lookup")

[ Brand Promotions By Promo Code â This API provides capabilities to
retrieve information about a single Brandâs Promotions. Including the
Promotion Products, Price Group and Price Code Information.
](../omedaclientkb/brand-promotions-by-promo-code "Brand Promotions By Promo
Code")

[ Brand Promotions Content By Promo Code â This API provides capabilities to
retrieve information about a Brands Single Promotion Content by Promo Code .
](../omedaclientkb/brand-promotions-content-by-promo-code "Brand Promotions
Content By Promo Code")

[ Brand Promotions Quantity â This API provides capabilities to retrieve
information about a specified quantity of Brandâs Promotions. Including the
Promotion Products, Price Group and Price Code Information.
](../omedaclientkb/brand-promotions-quantity "Brand Promotions Quantity")

[ Demographic Lookup by Brand API â This API provides capabilities to
retrieve the defined demographics of a single brand. This service is useful
for building your own data mapping service when reading or writing from/to
other Omeda services. ](../omedaclientkb/demographic-lookup-by-brand-api
"Demographic Lookup by Brand API")

[ Deployment Type Lookup by Brand API â This API provides capabilities to
retrieve the defined deployment types of a single brand. This service is
useful for building your own data mapping service when reading or writing
from/to other Omeda services. ](../omedaclientkb/deployment-type-lookup-by-
brand-api "Deployment Type Lookup by Brand API")

[ Product Lookup by Brand API â This API provides capabilities to retrieve
the defined products of a single brand. This service is useful for building
your own data mapping service when reading or writing from/to other Omeda
services. ](../omedaclientkb/product-lookup-by-brand-api "Product Lookup by
Brand API")

## Store Brand Info

These services allow you to store brand level data in your database.

[ Assign Behavior To Category â This API provides the ability to assign a
Behavior to a Behavior Category. ](../omedaclientkb/assign-behavior-to-
category "Assign Behavior To Category")

[ Store Behavior â This API provides capabilities to add and update
Behaviors defined for a given brand. ](../omedaclientkb/store-behavior "Store
Behavior")

[ Store Behavior Action â This API provides capabilities to retrieve and add
Behavior âActionsâ defined for a given brand. ](../omedaclientkb/store-
behavior-action "Store Behavior Action")

[ Store Behavior Attribute â This API provides capabilities to create
Behavior Attributes as well as add Defined values for existing Behaviors
Attributives. ](../omedaclientkb/store-behavior-attribute "Store Behavior
Attribute")

[ Store Behavior Category â This API provides capabilities to retrieve and
add the Behavior Categories defined for a given brand.
](../omedaclientkb/store-behavior-category "Store Behavior Category")

## Authentication

[ Activate Authentication â This API provides the ability to activate a
userâs status code for the customer id and namespace.
](../omedaclientkb/activate-authentication "Activate Authentication")

[ Add Authentication â This API provides the ability to post a username and
password to an existing customer for authentication. This can be used to
capture the log in credentials to be used for logging into a gated site.
](../omedaclientkb/add-authentication "Add Authentication")

[ Reset Authentication â This API provides the ability to reset the password
for an existing customer for authentication. This will update the current
password for the user and update it to a temporary random password that should
be changed upon log in. ](../omedaclientkb/reset-authentication "Reset
Authentication")

[ Update Authentication â This API provides the ability to post an update to
username and/or password for an existing customer for authentication.
](../omedaclientkb/update-authentication "Update Authentication")

[ Validate Authentication â This API provides the ability to validate a
username and password for authentication. This can be used to authenticate a
user and get the Omeda Customer Id for the authenticated user. It will only
validate usernames of active customers. ](../omedaclientkb/validate-
authentication "Validate Authentication")

## Email

If you are using [Email Builder](https://training.omeda.com/knowledge-
base/email-builder-overview/), or are using your database to store opt-in and
opt-out information by email addresses, these APIs are available for your use.

[ Email Audience Assignment Status â The List Assignment Status API provides
the ability to get the status of a customer list that is currently being
assigned from the Omail FTP Site to a deployment. ](../omedaclientkb/email-
audience-assignment-status "Email Audience Assignment Status")

[ Email Clicks â This service retrieves Omail data related to clicks on
links in emails using various parameters. ](../omedaclientkb/email-clicks
"Email Clicks")

[ Email Deployment â The Deployment Service API provides the ability to
post/put deployment information to Email Builder. This information is used to
either create a new Email Builder deployment, or update an existing Email
Builder deployment. Deployment information is validated for basic information.
](../omedaclientkb/email-deployment "Email Deployment")

[ Email Deployment Add Audience â The Deployment Add Audience API provides
the ability add a previously uploaded list of customers to a deployment.
](../omedaclientkb/email-deployment-add-audience "Email Deployment Add
Audience")

[ Email Deployment Approval Lookup â The Deployment Approval Lookup API
provides the ability to retrieve the approval queue information such as tests,
users and comments. ](../omedaclientkb/email-deployment-approval-lookup "Email
Deployment Approval Lookup")

[ Email Deployment Audience List FTP â Deployment List FTP is the means by
which deployment lists are made available in the Email Builder system.
](../omedaclientkb/email-deployment-audience-list-ftp "Email Deployment
Audience List FTP")

[ Email Deployment Cancel â The Deployment Cancel API provides the ability
to cancel a deployment. ](../omedaclientkb/email-deployment-cancel "Email
Deployment Cancel")

[ Email Deployment Clone â The Clone Deployment Service API provides the
ability to post deployment information to Omail. This information is used to
clone existing Omail deployment. Deployment information is validated for basic
information. ](../omedaclientkb/email-deployment-clone "Email Deployment
Clone")

[ Email Deployment Content â The Deployment Content API provides the ability
to post information to a deployment. These fields can include the
âSubjectâ line of the email, the âFrom Nameâ of the email, the HTML
content, the Text content, etc. Since we are passing in html data in this
resource, xml is the default format for requests and responses.
](../omedaclientkb/email-deployment-content "Email Deployment Content")

[ Email Deployment Content Lookup â An api available to our Email Builder
clients. For a given set of url parameters â the API will return the text or
html content for a given Email Builder deployment and specified split.
](../omedaclientkb/email-deployment-content-lookup "Email Deployment Content
Lookup")

[ Email Deployment Lookup â The Deployment Lookup API provides the ability
to retrieve deployment information such as link tracking, delivery statistics,
deployment status, history, etc. via an HTTP GET request.
](../omedaclientkb/email-deployment-lookup "Email Deployment Lookup")

[ Email Deployment Remove Audience â The Deployment Remove Audience API
provides the ability to remove a list that is currently assigned to a
deployment. ](../omedaclientkb/email-deployment-remove-audience "Email
Deployment Remove Audience")

[ Email Deployment Schedule â The Deployment Schedule API provides the
ability to schedule a deployment for sending. ](../omedaclientkb/email-
deployment-schedule "Email Deployment Schedule")

[ Email Deployment Search â This service retrieves a list of most recent
deployments for a given brand based on search parameters.
](../omedaclientkb/email-deployment-search "Email Deployment Search")

[ Email Deployment Test â The Deployment Test API provides the ability to
send test copies of your deployment to the test recipients that were specified
when the deployment was created. ](../omedaclientkb/email-deployment-test
"Email Deployment Test")

[ Email Deployment Unschedule â The Deployment Unschedule API provides the
ability to unschedule a deployment, perhaps to allow further editing.
](../omedaclientkb/email-deployment-unschedule "Email Deployment Unschedule")

[ Email Flag Email As Invalid â The Invalid Email API allows our client to
mark a customer Email Address as invalid for a brand.
](../omedaclientkb/email-flag-email-as-invalid "Email Flag Email As Invalid")

[ Email On Demand Send â The Deployment API allows our clients to send a
single Omail email deployment. ](../omedaclientkb/email-on-demand-send "Email
On Demand Send")

[ Email Opt In/Out Lookup â This service returns Opt In/Out information
stored for a given customer. ](../omedaclientkb/email-opt-in-out-lookup "Email
Opt In/Out Lookup")

[ Email Optin Queue â The OptIn Queue API allows our client to OptIn their
subscribers or customers to their email deployments at the client, brand, and
deployment type level. All 3 OptIn levels can be submitted in one OptIn Queue
API call. ](../omedaclientkb/email-optin-queue "Email Optin Queue")

[ Email Optout Queue â The OptOut Queue API allows our client to OptOut
their subscribers or customers to their email deployments at the client,
brand, and deployment type level. All 3 OptOut levels can be submitted in one
OptOut Queue API call. ](../omedaclientkb/email-optout-queue "Email Optout
Queue")

[ Email â Checklist for Sends Created via API Calls â Checklist for Sends
Created via API Calls. ](../omedaclientkb/email-checklist-for-sends-created-
via-api-calls "Email â Checklist for Sends Created via API Calls")

## Utility

These âdictionaryâ APIs provide decode capabilities for your value
ids/descriptions used for demographics, products, postal information, etc.

[ Form Submission API â The Form Submission API returns all transactions
from a given form and specified date range. Data including billing info,
authentication, and gift recipients will be excluded. ](../omedaclientkb/form-
submission-api "Form Submission API")

[ Postal Info Lookup â The Postal Information API returns postal information
for a given postal code. The postal data is updated as available from the
USPS, and field definitions can be found here: ](../omedaclientkb/postal-info-
lookup "Postal Info Lookup")

[ Run Processor â The Run Processor API runs the Processor for an indivdual
TransactionID that is pending processing. ](../omedaclientkb/run-processor
"Run Processor")

[ Tax Rate Lookup â The Tax Rate Lookup API returns the tax rate that should
be applied to an order based on their location and product id being ordered.
The lookup will also take into consideration tax exempt customers if the
Customer Id is passed in. ](../omedaclientkb/tax-rate-lookup "Tax Rate
Lookup")

## olytics

[ Customer Olytics Data â For a given omeda customer id and set of
olytics/behavioral search parameters, this API will return olytics behavioral
data matching those parameters. ](../omedaclientkb/customer-olytics-data
"Customer Olytics Data")

[ Olytics Comprehensive Lookup â For a given global database, this API can
be used to return all of the active olytics fields and their valid values on
the database. These are the fields / values that the user has been passing to
us in their olytics.fire() API calls. ](../omedaclientkb/olytics-
comprehensive-lookup "Olytics Comprehensive Lookup")

[ Olytics Customer Search â For a given set of olytics/behavioral search
parameters, this API will return a list of Omeda customer ids matching those
parameters. This api can be used to help in building an external lead gen
tool. ](../omedaclientkb/olytics-customer-search "Olytics Customer Search")

[ Olytics Customer Top Values â For a given customer, this service returns
the top 3 olytics tag values for each field you are using as part of your
olytics setup. ](../omedaclientkb/olytics-customer-top-values "Olytics
Customer Top Values")

**Table of Contents**

×

