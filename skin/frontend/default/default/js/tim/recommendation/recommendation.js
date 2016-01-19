jQuery(document).ready(function () {
    addOpinionAjax();
    addCommentAjax();

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
        jQuery('#tim-abuse-application').hide().val('');
        jQuery('#tim-abuse-application-sendbt').hide();
        jQuery('.tim-markabuse-popup-container p').text('Dziękujemy za informację o nadużyciu. Twoje zgłoszenie zostało przesłane do weryfikacji przez administratora');
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

/**
 * Save opinion by AJAX
 */
function addOpinionAjax(){
    jQuery("#form-validate").ajaxForm({
        beforeSend: function() {
            jQuery('#add-ajax-opinion').prop('disabled', true);
            jQuery('#loading-frontend-mask').show(300);
        },
        success: function(response) {
            displayAjaxOpinionPopupResponse(response);
            jQuery("#form-validate")[0].reset();
        },
        error: function(response) {
            displayAjaxOpinionPopupResponse(response);
        }
    });

    jQuery('#tim-add-opinion-popup-close').on('click', function () {
        jQuery('.tim-add-opinion-popup').hide();
    });
    jQuery(document).keydown(function (e) {
        if (e.keyCode == 27) {
            jQuery('.tim-add-opinion-popup').hide();
        }
    });
}

/**
 * Save comment by AJAX
 */
function addCommentAjax(){
    jQuery("#form-validate-comment").ajaxForm({
        beforeSend: function() {
            jQuery('#add-ajax-comment').prop('disabled', true);
            jQuery('#loading-frontend-mask').show(300);
        },
        success: function(response) {
            displayAjaxCommentPopupResponse(response);
            jQuery("#form-validate-comment")[0].reset();
        },
        error: function(response) {
            displayAjaxCommentPopupResponse(response);
        }
    });

    jQuery('#tim-add-comment-popup-close').on('click', function () {
        jQuery('.tim-add-comment-popup').hide();
    });
    jQuery(document).keydown(function (e) {
        if (e.keyCode == 27) {
            jQuery('.tim-add-comment-popup').hide();
        }
    });
}

function displayAjaxOpinionPopupResponse(response){
    jQuery('#loading-frontend-mask').hide();
    jQuery('.tim-add-opinion-popup').show(300);
    var response = JSON.parse(response);
    jQuery('.tim-add-opinion-popup-container p').text(response['message']);
    jQuery('#add-ajax-opinion').prop('disabled', false);
}

function displayAjaxCommentPopupResponse(response){
    jQuery('#loading-frontend-mask').hide();
    jQuery('.tim-add-comment-popup').show(300);
    var response = JSON.parse(response);
    jQuery('.tim-add-comment-popup-container p').text(response['message']);
    jQuery('#add-ajax-comment').prop('disabled', false);
}