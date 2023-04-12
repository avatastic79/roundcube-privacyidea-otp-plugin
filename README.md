miskatonic_cymru/privacyidea_otp
================================

This is a quick and dirty plugin for Roundcube Webmail, which adds input fields for a PIN and OTP to the Roundcube login page, and adds a verification check against a PrivacyIdea server.

This requires an existing PrivacyIdea installation, and HOTP/TOTP tokens to already be created, and a PHP installation with cURL support enabled.


Configuration
-------------

Set the url of your PrivacyIdea server in config.inc.php

    $rcmail_config['privacyidea_api_url'] = 'https://privacyidea.example.org';

Optionally, set the realm the tokens have been created in.

    $rcmail_config['privacyidea_api_realm'] = 'roundcube';

Optionally, set to not use the PIN tokens, default to true

    $rcmail_config['privacyidea_use_pin'] = false;


Installation
------------

Clone the repository in to your roundcube plugins directory

    git clone https://github.com/avatastic79/roundcube-privacyidea-otp-plugin privacyidea_otp

Activate the plugin in the roundcube/config/config.inc.php

    $config['plugins'] = array('privacyidea_otp');


Sample
------
![](images/screenshot.png)
