<?php

/**
 * miskatonic_cymru/privacyidea_otp config options 
 */

// PrivacyIdea server url
$rcmail_config['privacyidea_api_url'] = '**REQUIRED**';

// PrivacyIdea token realm.
// If set the specified realm will be queried for the tokens
// otherwise the default realm 
// will be used
// $rcmail_config['privacyidea_api_realm'] = '';

// PrivacyIdea use PIN (true) or not (false) 
$rcmail_config['privacyidea_use_pin'] = true;

?>
