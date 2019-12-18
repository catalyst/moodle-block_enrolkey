[![Build Status](https://travis-ci.org/catalyst/moodle-block_enrolkey.svg?branch=master)](https://travis-ci.org/catalyst/moodle-block_enrolkey)

# moodle-block_enrolkey

An easy way to enrol using an enrolkey from anywhere within moodle

* [Branches](#branches)
* [Installation](#installation)
* [Setup](#setup)
* [Client Usage](#client-usage)
* [Support](#support)

Branches
--------

For all current moodle installations, use the master branch

Installation
------------ 

Add the plugin to /blocks/enrolkey/

Run the Moodle upgrade.

Setup
-----
First enable the block_enrolkey plugin for use.

`Site administration > Appearance > Default Dashboard page > Manage Authentication`

Add the block:

 - `Blocks editing on`
 - `Add a block`
 - select *`Enrol key`*
 - `Blocks editing off`
 - `Reset Dashboard for all users`

Client Usage
------------

When a user logs in the *Enrol key* block should be displayed. The user can enter a token into the input field in the form. 

If the token matches **any** valid instance of self enrolment, then the user will be enrolled to those courses. 

Support
-------
For any issue with the plugin, please log the in the github repository here:

https://github.com/catalyst/moodle-block_enrolkey/issues

Please note our time is limited, so if you need urgent support or want to
sponsor a new feature then please contact Catalyst IT Australia:

https://www.catalyst-au.net/contact-us



This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

<a href="https://www.catalyst-au.net/"><img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400"></a>
