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

        #wpadminbar .wp-statichost-deployments-site > .ab-item,
        #wpadminbar .wp-statichost-deployments-button > .ab-item {
            display: flex;
            align-items: center;
        }
        
        #wpadminbar .wp-statichost-deployments-button > .ab-item:hover svg * {
            fill: #72aee6;
        }

        #wpadminbar .wp-statichost-deployments-button svg {
            height: 1rem;
            width: auto;
            margin-left: .25rem
        }

        #wpadminbar .wp-statichost-deployments-button svg * {
            fill: #fff
        }

        #wpadminbar .wp-statichost-deployments-site img {
            height: 1.25rem;
            width: auto;
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
            'id' => 'wp-statichost-deployments-site',
            'title' => '<img src="/wp-content/plugins/wp-statichost-deployments/assets/loading.svg" width="220" height="30" alt="statichost.eu status" />',
            'parent' => 'top-secondary',
            'href' => statichost_deployments_get_site_url(),
            'meta' => [
                'target' => '_blank',
                'class' => 'wp-statichost-deployments-site'
            ]
        ]);
        
        $bar->add_node([
            'id' => 'wp-statichost-deployments-deploy',
            'title' => 'Deploy Website <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="32" width="32"><path d="M12 0a12 12 0 1 0 12 12A12 12 0 0 0 12 0Zm4.91 10.41A1 1 0 0 1 16 11h-2.25a.25.25 0 0 0-.25.25v7.25a1.5 1.5 0 0 1-3 0v-7.25a.25.25 0 0 0-.25-.25H8a1 1 0 0 1-.75-1.66l4-4.5a1 1 0 0 1 1.5 0l4 4.5a1 1 0 0 1 .16 1.07Z" fill="#ffffff"></path></svg>',
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
