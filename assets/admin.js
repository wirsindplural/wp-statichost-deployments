;(function ($, window, document, undefined) {
    $(function () {
        var image = $('.wp-statichost-deployments-button img')
        var refreshTimout = null
        
        var updateNetlifyBadgeUrl = function () {
            $.get('/wp-content/plugins/wp-statichost-deployments/src/status.php')
            .done(function (data) {
                data = JSON.parse(data)
                image.prop('src', '/wp-content/plugins/wp-statichost-deployments/assets/' + data.status + '.svg')
                refreshTimout = setTimeout(updateNetlifyBadgeUrl, 4000)
            })
        };

        updateNetlifyBadgeUrl();

        $('.wp-statichost-deployments-button').click(function (e) {
            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: wpjd.ajaxurl,
                data: {
                    action: 'wp_statichost_deployments_manual_trigger',
                    security: wpjd.deployment_button_nonce,
                },
                dataType: 'json'
            });
        });

    });
})(jQuery, window, document)
