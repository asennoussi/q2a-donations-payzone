<?php
/*
  Plugin Name: Payzone donation widget
  Plugin URI: https://github.com/Sshuichi/q2a-donations-payzone
  Plugin Description: Allows donations VIA payzone
  Plugin Version: 1.0
  Plugin Date: 2017-01-01
  Plugin Author: Sshuicchi
  Plugin Author URI:
  Plugin License: GPLv2
  Plugin Minimum Question2Answer Version: 1.7
  Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

qa_register_plugin_module(
    'widget', // type of module
    'qa-donations-payzone-widget.php', // PHP file containing module class
    'qa_donations_payzone_widget', // module class name in that PHP file
    'Payzone widget payments' // human-readable name of module
);

qa_register_plugin_layer(
    'qa-donations-payzone-layer.php', // PHP file containing layer
    'Donations Layer' // human-readable name of layer
);

qa_register_plugin_module(
    'page', // type of module
    'qa-donations-payzone-page.php', // PHP file containing module class
    'qa_donations_payzone_page', // name of module class
    'Pre donation page.' // human-readable name of module
);

qa_register_plugin_module(
    'page', // type of module
    'qa-donations-payzone-process.php', // PHP file containing module class
    'qa_donations_payzone_process', // name of module class
    'Process call back of plugin' // human-readable name of module
);
qa_register_plugin_module(
    'page', // type of module
    'qa-donations-payzone-thank.php', // PHP file containing module class
    'qa_donations_payzone_thank', // name of module class
    'Thank you page' // human-readable name of module
);
qa_register_plugin_phrases(
    'qa-donations-payzone-lang-*.php', // pattern for language files
    'plugin_donations_payzone' // prefix to retrieve phrases
);