;(function ($, window, document, undefined) {
    $(function () {
        var image = $('.wp-statichost-deployments-button img');
        var imageSrc = image.prop('src');
        var refreshTimout = null;
        
        var updateNetlifyBadgeUrl = function () {
            if (!image.length) {
                return;
            }
            var d = new Date();
            image.prop('src', imageSrc + '?v=s_' + d.getTime());
            refreshTimout = setTimeout(updateNetlifyBadgeUrl, 4000);
        };

        refreshTimout = setTimeout(updateNetlifyBadgeUrl, 4000);

        $('.wp-statichost-deployments-button').click(function (e) {
            e.preventDefault();
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
})(jQuery, window, document);
