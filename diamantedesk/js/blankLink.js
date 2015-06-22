jQuery(document).ready(function () {
    jQuery('.blank-link').each(function () {
        var uri = jQuery(this).attr('href');
        var key = getParameterByName(uri, 'id_configuration');
        jQuery(this).attr('target', '_blank');
        jQuery(this).attr('href', DiamanteDesk_Server_Address + DiamanteDesk_Link_To_Admin + key);
    });
});

function getParameterByName(uri, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(uri);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}