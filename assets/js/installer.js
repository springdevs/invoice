jQuery(document).ready(function ($) {
    $('.sdevs-loading-icon').hide();

    $('.sdevs-install-plugin').click(() => {
        install_woocommerce_plugin();
    });

    $('.sdevs-activate-plugin').click(() => {
        activate_woocommerce_plugin();
    });

    function install_woocommerce_plugin() {
        $.ajax({
            type: 'POST',
            url: sdevs_installer_helper_obj.ajax_url,
            data: { install_plugin: 'woocommerce', action: 'install_woocommerce_plugin' },
            beforeSend: function () {
                $('.sdevs-loading-icon').show();
            },
            success: function (data) {
                activate_woocommerce_plugin();
            },
            complete: function () {
                console.log("Plugin installed");
            }
        });
    }

    function activate_woocommerce_plugin() {
        $.ajax({
            type: 'POST',
            url: sdevs_installer_helper_obj.ajax_url,
            data: { activate_plugin: 'woocommerce', action: 'activate_woocommerce_plugin' },
            beforeSend: function () {
                $('.sdevs-loading-icon').show();
            },
            success: function (data) {
                window.location.reload();
            },
            complete: function () {
                console.log("Plugin activated");
                window.location.reload();
            }
        });
    }
});