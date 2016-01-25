jQuery(document).ready(function () {
    /* function to put value into html content right to rating stars and switch userlogin details*/

    jQuery('input').on('change', function () {
        var inputChangeName = jQuery(this).attr('name');
        var inputChangeValue = jQuery(this).val();
        var inputChangeNameSpan = 'span.' + inputChangeName
        /* alert(inputChangeName+inputChangeValue)         */
        jQuery(inputChangeNameSpan).html(inputChangeValue);
        //console.log(inputChangeName);

        if (inputChangeName == 'userHaveaccount') {
            if (inputChangeValue == 'TAK') {
                jQuery('.tim-userlogin-existuser').show();
                jQuery('.tim-userlogin-newuser').hide();
            }
            if (inputChangeValue == 'NIE') {
                jQuery('.tim-userlogin-existuser').hide();
                jQuery('.tim-userlogin-newuser').show();
            }
        }
    });

    jQuery('.tim-popup-close').click(function () {
        var popupClass = '.' + jQuery(this).parents().get(1).className;
        jQuery(popupClass).hide();
    });

});

/* function to show place for adding comment */
function showAddComment() {
    jQuery('#tim-comment-add-show').hide();
    jQuery('.tim-comment-add-window').show(300);
}

function checkIfUserIsLoggedIn() {
    /* function open modal when submit button is pressed without validation yet */
    jQuery('.tim-userlogin-popup').show(300);

    /* function to close modal login area */
    jQuery('#tim-userlogin-popup-close').on('click', function () {
        jQuery('.tim-userlogin-popup').hide();
    });
    /* function to close modal login area by escape button*/
    jQuery(document).keydown(function (e) {
        if (e.keyCode == 27) {
            jQuery('.tim-userlogin-popup').hide();
        }
    });
}
/**
 * Variables for sendParams() method
 */
var recomId;
var userId;
var customerIp;
var customerHostName;
var siteUrl;

/**
 * Display popup and set variables
 * @param id
 * @param customerId
 * @param ip
 * @param hostName
 * @param url
 */
function markUserAbuse(id, customerId, ip, url, hostName) {
    jQuery('.tim-markabuse-popup').show(300);
    //console.log(url);
    jQuery('#tim-markabuse-popup-close').on('click', function () {
        jQuery('.tim-markabuse-popup').hide();
        jQuery('#tim-abuse-application').show();
        jQuery('#tim-abuse-application-sendbt').show();
    });
    jQuery('#tim-abuse-application-sendbt').on('click', function () {
        jQuery('#tim-abuse-application').hide();
        jQuery('#tim-abuse-application-sendbt').hide();
        jQuery('.tim-markabuse-popup-container p').text('Dziękujemy za wysłanie zgłoszenia, sytuacja zostanie zbadana przez naszego pracownika w najszybszym możliwym terminie.');
    });
    recomId = id;
    userId = customerId;
    customerIp = ip;
    customerHostName = hostName;
    siteUrl = url;
    jQuery(document).keydown(function (e) {
        if (e.keyCode == 27) {
            jQuery('.tim-markabuse-popup').hide();
        }
    });
}
/**
 * Send customer parameters to controller
 */
function sendParams() {

    var comment = jQuery('#tim-abuse-application').val();
    var param = {userId: userId, customerHostName: customerHostName, customerIp: customerIp, recom_id: recomId, comment: comment};

    jQuery.ajax({
        url: siteUrl,
        data: param
    });
}