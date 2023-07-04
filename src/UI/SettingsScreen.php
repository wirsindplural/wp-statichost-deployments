<?php

namespace Wirsindplural\statichostDeployments\UI;

class SettingsScreen
{
    /**
     * Register the requred hooks for the admin screen
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'addMenu']);
    }

    /**
     * Register an tools/management menu for the admin area
     *
     * @return void
     */
    public static function addMenu()
    {
        add_options_page(
            __( 'statichost.eu Deployments', 'wp-statichost-deployments' ),
            __( 'statichost.eu', 'wp-statichost-deployments' ),
            'manage_options',
            'wp-statichost-deployments-settings',
            [__CLASS__, 'renderPage']
        );
    }

    /**
     * Render the management/tools page
     *
     * @return void
     */
    public static function renderPage()
    {
        ?><div class="wrap">

            <h2><?= get_admin_page_title(); ?></h2>
            
            <form method="post" action="<?= esc_url(admin_url('options.php')); ?>">
                <?php

                settings_fields(WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_OPTIONS_KEY);
                do_settings_sections(WIRSINDPLURAL_STATICHOST_DEPLOYMENTS_OPTIONS_KEY);

                submit_button( __( 'Save Settings', 'wp-statichost-deployments' ), 'primary', 'submit', false);

                $uri = wp_nonce_url(
                    admin_url('admin.php?page=wp-statichost-deployments-settings&action=jamstack-deployment-trigger'),
                    'WIRSINDPLURAL_STATICHOST_deployment_trigger',
                    'WIRSINDPLURAL_STATICHOST_deployment_trigger'
                );

                ?>
            </form>

        </div><?php
    }
}
