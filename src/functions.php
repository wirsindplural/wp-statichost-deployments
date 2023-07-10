<?php

if (!function_exists('statichost_deployments_get_options')) {
    /**
     * Return the plugin settings/options
     *
     * @return array
     */
    function statichost_deployments_get_options() {
        return get_option(WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_OPTIONS_KEY, []);
    }
}

if (!function_exists('statichost_deployments_get_site_url')) {
    /**
     * Return the webhook url
     *
     * @return string|null
     */
    function statichost_deployments_get_site_url() {
        $options = statichost_deployments_get_options();
        return isset($options['site_url']) ? $options['site_url'] : null;
    }
}

if (!function_exists('statichost_deployments_get_site_name')) {
    /**
     * Return the webhook url
     *
     * @return string|null
     */
    function statichost_deployments_get_site_name() {
        $options = statichost_deployments_get_options();
        return isset($options['site_name']) ? $options['site_name'] : null;
    }
}

if (!function_exists('statichost_deployments_get_webhook_url')) {
    /**
     * Return the webhook url
     *
     * @return string|null
     */
    function statichost_deployments_get_webhook_url() {
        $site_name = statichost_deployments_get_site_name();
        return 'https://builder.statichost.eu/' . $site_name;
    }
}

if (!function_exists('statichost_deployments_get_badge_url')) {
    /**
     * Return the webhook url
     *
     * @return string|null
     */
    function statichost_deployments_get_badge_url() {
        $site_name = statichost_deployments_get_site_name();
        return 'https://builder.statichost.eu/' . $site_name . '/status.svg';
    }
}

if (!function_exists('statichost_deployments_fire_webhook')) {
    /**
     * Fire a request to the webhook.
     *
     * @return void
     */
    function statichost_deployments_fire_webhook() {
        \Wirsindplural\statichostDeployments\WebhookTrigger::fireWebhook();
    }
}