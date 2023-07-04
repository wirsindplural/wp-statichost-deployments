<?php

namespace Wirsindplural\statichostDeployments;

class WebhookTrigger
{
    /**
     * Setup hooks for triggering the webhook
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'trigger']);
        add_action('admin_bar_menu', [__CLASS__, 'adminBarTriggerButton']);

        add_action('admin_footer', [__CLASS__, 'adminBarCssAndJs']);
        add_action('wp_footer', [__CLASS__, 'adminBarCssAndJs']);
        
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueScripts']);

        add_action('wp_ajax_wp_statichost_deployments_manual_trigger', [__CLASS__, 'ajaxTrigger']);
    }

    /**
     * Show the admin bar css & js
     * 
     * @todo move this somewhere else
     * @return void
     */
    public static function adminBarCssAndJs()
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        ?><style>

        #wpadminbar .wp-statichost-deployments-button > a {
            background-color: rgba(255, 255, 255, .2) !important;
            color: #FFFFFF !important;
            display: flex;
            align-items: center;
        }
        #wpadminbar .wp-statichost-deployments-button > a:hover,
        #wpadminbar .wp-statichost-deployments-button > a:focus {
            background-color: rgba(255, 255, 255, .25) !important;
        }

        #wpadminbar .wp-statichost-deployments-button svg {
            width: 12px;
            height: 12px;
            margin-left: 5px;
        }

        #wpadminbar .wp-statichost-deployments-badge > .ab-item {
            display: flex;
            align-items: center;
        }

        #wpadminbar .wp-statichost-deployments-button img {
            height: 1.25rem;
            width: auto;
            margin-left: .5rem;
        }

        </style><?php
    }

    /**
     * Enqueue js to the admin & frontend
     * 
     * @return void
     */
    public static function enqueueScripts()
    {
        wp_enqueue_script(
            'wp-statichost-deployments-adminbar',
            WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_URL.'/assets/admin.js',
            ['jquery'],
            filemtime(WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_PATH.'/assets/admin.js')
        );

        $button_nonce = wp_create_nonce('wp-statichost-deployments-button-nonce');

        wp_localize_script('wp-statichost-deployments-adminbar', 'wpjd', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'deployment_button_nonce' => $button_nonce,
        ]);
    }

    /**
     * Add a "trigger webhook" button to the admin bar
     *
     * @param object $bar
     * @return void
     */
    public static function adminBarTriggerButton($bar)
    {
        $bar->add_node([
            'id' => 'wp-statichost-deployments',
            'title' => 'Deploy Website <img src="' . statichost_deployments_get_badge_url() . ' " width="225" height="30" alt="statichost.eu status" />',
            'parent' => 'top-secondary',
            'href' => 'javascript:void(0)',
            'meta' => [
                'class' => 'wp-statichost-deployments-button'
            ]
        ]);
    }

    /**
     * Trigger a request manually from the admin settings
     *
     * @return void
     */
    public static function trigger()
    {
        if (!isset($_GET['action']) || 'jamstack-deployment-trigger' !== $_GET['action']) {
            return;
        }
        
        check_admin_referer('WIRSINDPLURAL_STATICHOST_deployment_trigger', 'WIRSINDPLURAL_STATICHOST_deployment_trigger');

        self::fireWebhook();

        wp_redirect(admin_url('admin.php?page=wp-statichost-deployments-settings'));
        exit;
    }

    /**
     * Trigger a request manually from the admin settings
     *
     * @return void
     */
    public static function ajaxTrigger()
    {
        check_ajax_referer('wp-statichost-deployments-button-nonce', 'security');

        self::fireWebhook();

        echo 1;
        exit;
    }

    /**
     * Fire off a request to the webhook
     *
     * @return WP_Error|array
     */
    public static function fireWebhook()
    {
        $webhook = statichost_deployments_get_webhook_url();

        if (!$webhook) {
            return;
        }

        $args = apply_filters('statichost_deployments_webhook_request_args', [
            'blocking' => false
        ]);

        do_action('statichost_deployments_before_fire_webhook');

        $return = wp_safe_remote_post($webhook, $args);

        do_action('statichost_deployments_after_fire_webhook');

        return $return;
    }
}
