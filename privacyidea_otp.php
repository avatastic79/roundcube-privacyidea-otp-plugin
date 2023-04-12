<?php

/**
 * Privacy Idea One-time Password Authentication Plugin v1.0
 *
 * Allows to integrate two-factor authentication into RoundCube Webmail.
 * Before use, you must enrole the HOTP/TOTP in PrivacyIdea.
 * Detailed information:
 * https://privacyidea.readthedocs.io/en/latest/firststeps/index.html/
 *
 * Copyright 2019 Andrew Wiles 
 * <https://miskatonic.cymru/people/935745900a9e01374fcc7eff00003914>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice (including the next
 * paragraph) shall be included in all copies or substantial portions of the
 * Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class privacyidea_otp extends rcube_plugin
{

 public $task = 'login';

 function init()
 {
  $this->rcmail = rcmail::get_instance();
  $this->load_config();
  $this->add_texts('localization/', true);
  $this->add_hook('template_object_loginform', array( $this, 'template_object_loginform'));
  $this->add_hook('authenticate', array($this, 'authenticate'));
 }

 function template_object_loginform($loginform = array())
 {
 if ($this->rcmail->config->get('privacyidea_use_pin', true))
  {
    $input_pin = '<input type="password" name="_pin" id="rcmloginpin" required="required" autocomplete="off"/>';
  }
  $input_otp = '<input type="text" name="_otp" id="rcmloginotp" required="required" autocomplete="off"/>';
  if ($this->rcmail->config->get('privacyidea_use_pin', true))
  {
    $label_pin = '<label for="rcmloginpin">'. html::quote($this->gettext('privacyidea_otp_pin')).'</label>';
  }
  $label_otp = '<label for="rcmloginotp">'. html::quote($this->gettext('privacyidea_otp_otp')).'</label>';
  if ($this->rcmail->config->get('privacyidea_use_pin', true))
  {
    $form_additions = '<tr><td class="title">'.$label_pin.'</td>';
    $form_additions .= '<td class="input">'.$input_pin.'</td></tr>';
  }
  $form_additions .= '<tr><td class="title">'.$label_otp.'</td>';
  $form_additions .= '<td class="input">'.$input_otp.'</td></tr>';
  $form_additions .= '</tbody>';
  $loginform['content'] = str_ireplace('</tbody>',$form_additions,$loginform['content']);

  return $loginform;          
 }

 function authenticate($args = array())
 {
  if ($this->rcmail->config->get('privacyidea_use_pin', true))
  {
    $params = array("user" => $args['user'],
    "pass" => filter_input(INPUT_POST,'_pin').filter_input(INPUT_POST,'_otp')
    );
  }
  else
  {
    $params = array("user" => $args['user'],
    "pass" => filter_input(INPUT_POST,'_otp')
    );
  }

  if ($this->rcmail->config->get('privacyidea_api_realm')) {
   $params["realm"] = $this->rcmail->config->get('privacyidea_api_realm');
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $this->rcmail->config->get('privacyidea_api_url'));
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $res = curl_exec($ch);

  if ( $res === false ) {
   $args['abort'] = true;
   $args['error'] = curl_error($ch);
   curl_close($ch);
   return $args;
  } 

  curl_close($ch);
  $js = json_decode($res);

  if ( $js === null && json_last_error() !== JSON_ERROR_NONE) {
   $args['abort'] = true;
   $args['error'] = json_last_error();
   return $args;
  }

  if ( $js->{result}->{status} === TRUE && $js->{result}->{value} === TRUE) {
   return $args;
  } else {
   $args['abort'] = true;
   $args['error'] = $js->{detail}->{message};
   return $args;
  }
 }
}

?>
