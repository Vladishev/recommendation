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

    jQuery('.tim-opinion-photo a').click(function (e) {
        e.preventDefault();
        var recomId = e.target.id;
        jQuery('#tim-all-photo-popup-' + recomId).show(300);
    });
    jQuery('.tim-opinion-movie a').click(function (e) {
        e.preventDefault();
        var recomId = e.target.id;
        jQuery('#tim-video-popup-' + recomId).show(300);
    });
    jQuery('.tim-popup-close').click(function () {
        var popupClass = '.' + jQuery(this).parents().get(1).className;
        jQuery(popupClass).hide();
    });

    jQuery(document).keydown(function (e) {
        if (e.keyCode == 27) {
            jQuery('.tim-all-photo-popup').hide();
            jQuery('.tim-video-popup').hide();
        }
    });
});

/**
 * function to show place for adding comment
 */
function showAddComment(recomId) {
    jQuery('#tim-comment-add-show-' + recomId).hide();
    jQuery('#tim-comment-add-window-' + recomId).show(300);
}

/**
 * Shows all comments for opinion
 * @param recomId
 */
function seeAllComments(recomId) {
    jQuery('.tim-comment-display-none-' + recomId).show(300);
    jQuery('.tim-comment-link-' + recomId).hide();
    jQuery('.tim-comment-hide-link-' + recomId).show();
}

/**
 * Hides comments which not display by default
 * @param recomId
 */
function hideComments(recomId) {
    jQuery('.tim-comment-hide-link-' + recomId).hide();
    jQuery('.tim-comment-display-none-' + recomId).hide(300);
    jQuery('.tim-comment-link-' + recomId).show();
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
    var param = {
        userId: userId,
        customerHostName: customerHostName,
        customerIp: customerIp,
        recom_id: recomId,
        comment: comment
    };

    jQuery.ajax({
        url: siteUrl,
        data: param
    });
}

/**
 * Provide show and hide placeholder for text area.
 * @param (this)elem
 */
function placeholderAction(elem) {
    var id = jQuery(elem).attr('id');
    jQuery('#ph-' + id).hide();
    jQuery('#' + id).focusout(function () {
        if (!jQuery('#' + id).val()) {
            jQuery('#ph-' + id).show();
        }
    });
}

/**
 * Shows opinion form after click button
 */
function showAddOpinionForm() {
    jQuery('#tim-add-opinion-layout').show(500);
    jQuery('#tim-general-add-opinion-button').hide();
}

/**
 * Hides opinion form after click button
 */
function hideAddOpinionForm() {
    jQuery('#tim-add-opinion-layout').hide(500);
    jQuery('#tim-general-add-opinion-button').show();
}

/**
 * Hides comment form after click button
 */
function hideCommentForm(recomId) {
    jQuery('#tim-comment-add-window-' + recomId).hide(300);
    jQuery('#tim-comment-add-show-' + recomId).show(300);
}