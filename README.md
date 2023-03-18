# Vtiger and Telcom integration

Module for Vtiger 7.5, integration with Telcom voip provider (https://telcom.pro)
Enhance Communication with Vtiger Phone Calls Integration.

## Appendix

Phone calls are an integral part of sales and marketing. That's why businesses need to streamline their telephony processes and keep them simple.

## The Telcom module in Vtiger provides integration between your telcom provider and the CRM.

When you install and integrate the Telcom module, you can:
- Communicate with your customers directly from your CRM screen.
- Contact customers directly from your desktop or laptop via different touchpoints - calls, SMS’s, or faxes.
- Incoming Call Notifications. Match incoming calls with lead and contact records in Vtiger CRM. Review information available in a contact record, including call logs p. Provide personalized service and create deals or tickets from the incoming call pop-up.
- Single-Click Dialing. Do not spend extra time dialing phone numbers - just a click on the Phone icon next to lead or contact name will get you connected. The click-to-call feature enables you to make calls directly from Vtiger Leads, Contacts, Deals, Organizations, and Phone Calls modules.
- Call Records. Maintain up-to-date records of customer conversations that can be accessed by sales team members. Listen to these calls at a convenient time and review important information that you may have missed.


## How Your Business Can Benefit by Using Vtiger Telcom Integration
- Smooth Internal Collaboration. Vtiger telephony integration makes it easier for salespeople to communicate and share information. You can organize team conferences to discuss deals and save time by collaborating on essential cases.
- Reduced Overhead Costs. Integrated telephony with Vtiger CRM can prove extremely cost-effective. Integrating telephony with CRM is economical in hardware, equipment maintenance, and call charges.
- Better Accessibility. Telephony integration with Vtiger gives you mobility and allows you to call from anywhere, anytime.  You can work on the move and remain connected with customers around the clock!
- Call Automation. You can boost efficiency and save time by automating repetitive tasks such as follow-ups and post-call activities. Vtiger phone calls module enables you to handle more phone lines and automate call distribution to sales reps.
- Increased Productivity. Boost team productivity exponentially and streamline communication by using Vtiger phone calls integration. A unified platform makes it convenient to store customer details, track call records and increases visibility across the organization.
- Enhanced Control and Coordination. Telcom integration gives you a bird’s eye view of your team members call schedules so that you can plan your day better. You do not need to switch applications and can manage all customer calls on a single device. This leads to higher control, better responsiveness, and team coordination.

## Setup instruction
To setup integration with telcom provider, you neet to go to vtiger crm settings, choose "Telcom" menu page. After clicking on this menu, you can see Telcom settings page. By clicking on "Edit" button, you can open edit settings page.
After installing Telcom module, two fields will be added in user profile page: Telcom ID and Telcom Password. You can create user directly in Telcom Admin Panel. After filling out this two fields, you need to press Sync button.
To receive information from Telcom about new calls, you need to set following callback: `modules/Telcom/controllers/CallsController.php`

To setup integration of Telcom provider with Vtiger CRM, do following steps:
* Go to user settings page, in additional information block enter your login in "Telcom ID" firld and your password in "Telcom Password" field
* In CRM settings, go to "Telcom", to open module settings page and enter username and password data. Also you can change Telcom realm address.

As a result of all settings you will be able to receive incoming calls in SalesPlatform CRM system and make outgoing calls by pressing on the client's phone number. If you want, you can record the conversation between the employee and the customer, as well as view the history of calls.

Note, that in modules/Telcom/controllers folder there are .htaccess file, which manages redirect policy for webhook
```bash
DirectorySlash Off
RewriteEngine on
RewriteCond %{REQUEST_METHOD} ^(POST|PUT)$
RewriteRule ^call(.*)$ CallsController.php$1 [L]
RewriteRule ^user(.*)$ CallsController.php$1 [L]
```

If you are using nginx, make sure to setup on your server redirects from call, user and client endpoints to CallsController


## Installation

Download zip installation archive from releases tab and install in vtiger module manager.


## Feedback

If you have any feedback, please reach out to us at se@sergeyem.ru


## Support

For support, email se@sergeyem.ru or write me via telegram: @sergeyem.



