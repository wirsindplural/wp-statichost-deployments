<?php

namespace Wirsindplural\statichostDeployments;

class Settings
{
    /**
     * Setup required hooks for the Settings
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'register']);
    }

    /**
     * Register settings & fields
     *
     * @return void
     */
    public static function register()
    {
        $key = WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_OPTIONS_KEY;

        register_setting($key, $key, [__CLASS__, 'sanitize']);
        add_settings_section('general', __( 'General', 'wp-statichost-deployments'), '__return_empty_string', $key);
        
        // ...

        $option = statichost_deployments_get_options();

        add_settings_field('site_name', __( 'Site Name', 'wp-statichost-deployments' ), ['Wirsindplural\statichostDeployments\Field', 'text'], $key, 'general', [
            'name' => "{$key}[site_name]",
            'value' => statichost_deployments_get_site_name(),
            'description' => sprintf( __( 'Your statichost.eu site name, find it in your <a href="https://builder.statichost.eu" target="_blank" rel="noopener noreferrer">Dashboard</a>.') )
        ]); 
    }

    /**
     * Get the badge image URL, has fallback to old option name
     *
     * @param array $option
     * @return string
     */
    protected static function getBadgeImageUrl($option)
    {
        if (!empty($option['deployment_badge_url'])) {
            return $option['deployment_badge_url'];
        }

        return !empty($option['netlify_badge_url']) ? $option['netlify_badge_url'] : '';
    }

}