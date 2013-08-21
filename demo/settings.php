<?php
/**
 * SAMPLE Code to demonstrate how provide SAML settings.
 *
 * The settings are contained within a OneLogin_Saml_Settings object. You need to
 * provide, at a minimum, the following things:
 *
 *  - idpSingleSignOnUrl
 *    This is the URL to forward to for auth requests.
 *    It will be provided by your IdP.
 *
 *  - idpPublicCertificate
 *    This is a certificate required to authenticate your request.
 *    This certificate should be provided by your IdP.
 * 
 *  - spReturnUrl
 *    The URL that the IdP should redirect to once the authorization is complete.
 *    You must provide this, and it should point to the consume.php script or its equivalent.
 */

define('XMLSECLIBS_DIR', './../ext/xmlseclibs/');
require_once XMLSECLIBS_DIR . 'xmlseclibs.php';

define('ONELOGIN_SAML_DIR', './../src/OneLogin/Saml/');
require_once ONELOGIN_SAML_DIR . 'AuthRequest.php';
require_once ONELOGIN_SAML_DIR . 'Response.php';
require_once ONELOGIN_SAML_DIR . 'Settings.php';
require_once ONELOGIN_SAML_DIR . 'XmlSec.php';

$settings = new OneLogin_Saml_Settings();

// Replace YOUR-HORIZON-GATEWAY with your horizon workspace gateway name and 
// obtain APPID from Application Page (Catalog->Application->Configuration->Launch URL

$settings->idpSingleSignOnUrl = 'https://YOUR-HORIZON-GATEWAY/SAAS/API/1.0/GET/apps/launch/app/APPID';

// The certificate for the users account in the IdP
$settings->idpPublicCertificate = <<<CERTIFICATE
-----BEGIN CERTIFICATE-----
<place your idP certificate here>
-----END CERTIFICATE-----
CERTIFICATE;

// The URL where to the SAML Response/SAML Assertion will be posted
$settings->spReturnUrl = 'http://samldemo.example.com/php-saml/demo/samldispatch.php';

// Name of this application
$settings->spIssuer = 'php-saml';

// Tells the IdP to return the email address of the current user
$settings->requestedNameIdFormat = OneLogin_Saml_Settings::NAMEID_EMAIL_ADDRESS;

$settings->dsn = 'sqlite:/usr/share/nginx/www/samldemo/database.db';

return $settings;
