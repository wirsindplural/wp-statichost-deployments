<?php

/**
 * Plugin Name: statichost Deployments
 * Plugin URI: https://github.com/wirsindplural/wp-statichost-deployments
 * Description: A WordPress plugin for deployments on statichost.eu.
 * Author: Wir sind Plural
 * Author URI: https://www.plural.at
 * Version: 0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_FILE', __FILE__);
define('WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_URL', untrailingslashit(plugin_dir_url(__FILE__)));

require_once(WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_PATH . '/src/App.php');

Wirsindplural\statichostDeployments\App::instance();