jQuery(document).ready(function () {
    addOpinionAjax();
    addCommentAjax();
    sorting();

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
            jQuery('.tim-userlogin-popup').hide();
            jQuery('.tim-add-comment-popup').hide();
            jQuery('.tim-add-opinion-popup').hide();
            jQuery('.tim-markabuse-popup').hide();
        }
    });
});

function sorting() {
    jQuery('.tim-toolbar').change(function (){
        var $sort = jQuery('.tim-comm-sortingselect');
        var dataSet = jQuery('#tim-controller').data();
        var sortBy = $sort.val();
        var url = dataSet.url;
        var productId = dataSet.product;
        var param = {sortBy: sortBy, productId: productId};
        jQuery.ajax({
            url: url,
            type: "post",
            data: param,
            success: function(response){
                //alert('Ok!');
                var response = JSON.parse(response);
                renderOpinionsList(response);
            }
        });
    });
}

function renderOpinionsList(response) {
    response.forEach(function(item, i){
        //console.log(item['recom_id']);
        var recomId = item['recom_id'];
        var opinionData = item['opinionData'];
        var $parentEl = jQuery('.tim-opinion-'+i);

        //Change date
        $parentEl.find('.tim-opinion-date').html('Opinia z dnia: <span>'+opinionData['date_add']+'</span> '+recomId);
        //Change advantages
        $parentEl.find('.tim-opinion-advantages-toolbar').html(opinionData['advantages']);
        //Change defects
        $parentEl.find('.tim-opinion-defects-toolbar').html(opinionData['defects']);
        //Change conclusion
        $parentEl.find('.tim-opinion-conclusion-toolbar').html(opinionData['conclusion']);
        //Change images
        $parentEl.find('.tim-opinion-photo').remove();
        $parentEl.find('.tim-opinion-movie').remove();
        if ((typeof opinionData['images'][0] != 'undefined') && (typeof opinionData['movie_url'] != 'undefined')) {
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div><div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
        } else if (typeof opinionData['movie_url'] != 'undefined') {
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
        } else {
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div>');
        }


    });
}

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
    recomId = id;
    userId = customerId;
    customerIp = ip;
    customerHostName = hostName;
    siteUrl = url;

    jQuery('.tim-markabuse-popup').show(300);
    jQuery('#tim-abuse-application').show();
    jQuery('#tim-abuse-application-sendbt').show();
    jQuery('.tim-markabuse-popup-container p').text('Jeżeli masz uwagi dotyczące naruszenia regulaminu strony, co do formy, treści lub zawartości niniejszego wpisu, napisz nam o tym korzystając z poniżeszego pola do opisu zgłoszenia.');

    jQuery('#tim-abuse-application-sendbt').on('click', function () {
        jQuery('#tim-abuse-application').hide().val('');
        jQuery('#tim-abuse-application-sendbt').hide();
        jQuery('.tim-markabuse-popup-container p').text('Dziękujemy za informację o nadużyciu. Twoje zgłoszenie zostało przesłane do weryfikacji przez administratora');
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
 * Provide show and hide placeholder for comments textarea
 * @param recomId
 */
function commentPlaceholderAction(recomId) {
    jQuery('#ph-tim-opinion-comment-' + recomId).hide();
    jQuery('#tim-opinion-comment-' + recomId).focusout(function () {
        if (!jQuery('#tim-opinion-comment-' + recomId).val()) {
            jQuery('#ph-tim-opinion-comment-' + recomId).show();
        }
    });
}

//temporary, while not finding solution with validation tinyMCE fields
/**
 * Provide show and hide placeholder for text area.
 * @param elem
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
 * Hide placeholder on tinyMce
 * @param data
 */
function hidePlaceholder(data){
    var id = jQuery(data).attr('id');
    var placeholder = jQuery('#ph-'+ id);
    placeholder.hide();
    jQuery('#' + id + '_ifr').focusout(function(){
        if (!placeholder.val()) {
            placeholder.show();
        }
    });
}

/**
 * Show placeholder on tinyMce
 * @param ed
 */
function showPlaceholder(ed){
    var id = jQuery(ed).attr('id');
    var placeholder = jQuery('#ph-'+ id);
    tinyMCE.triggerSave();
    if (!jQuery('#'+id).val()) {
        placeholder.show();
    }
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
}

/**
 * Save comment by AJAX
 */
function addCommentAjax(){
    jQuery(".comment-form").ajaxForm({
        beforeSend: function() {
            jQuery('#add-ajax-comment').prop('disabled', true);
            jQuery('#loading-frontend-mask').show(300);
        },
        success: function(response) {
            displayAjaxCommentPopupResponse(response);
        },
        error: function(response) {
            displayAjaxCommentPopupResponse(response);
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
    jQuery('#tim-opinion-comment-' + response['commentRecomId']).val('');
    jQuery('#ph-tim-opinion-comment-' + response['commentRecomId']).show();
    jQuery('#add-ajax-comment').prop('disabled', false);
}