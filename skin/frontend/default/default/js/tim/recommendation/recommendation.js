jQuery(document).ready(function () {
    addOpinionAjax();
    addCommentAjax();
    getTimToolbarData();
    showPhotos();
    showVideo();
    closePopup();

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

/**
 * Check click action and show popup with opinion photo
 */
function showPhotos() {
    jQuery(document).on('click', '.tim-opinion-photo a', function(e){
            e.preventDefault();
            var recomId = e.target.id;
            jQuery('#tim-all-photo-popup-' + recomId).show(300);
    });
}

/**
 * Check click action and show popup with opinion video
 */
function showVideo() {
    jQuery(document).on('click', '.tim-opinion-movie a', function(e){
        e.preventDefault();
        var recomId = e.target.id;
        jQuery('#tim-video-popup-' + recomId).show(300);
    });
}

/**
 * Close popup action
 */
function closePopup() {
    jQuery(document).on('click', '.tim-popup-close', function(){
        var popupClass = '.' + jQuery(this).parents().get(1).className;
        jQuery(popupClass).hide();
    });
}

/**
 * Gets data from Tim Toolbar and send it to controller
 */
function getTimToolbarData() {
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

/**
 * Renders opinion list on product_view after it sorted/paginated
 * @param response
 */
function renderOpinionsList(response) {
    response.forEach(function(item, i){
        var recomId = item['recom_id'];
        var opinionData = item['opinionData'];
        var $parentEl = jQuery('.tim-opinion-'+i);
        var abuseData = jQuery('.tim-user-log-info').data();
        var isLoggedIn = abuseData.log;
        var abuseController = abuseData.malpractice;
        var userId = abuseData.id;
        var userIp = abuseData.ip;
        var userHost = abuseData.host;

        //Change date
        $parentEl.find('.tim-opinion-date').html('Opinia z dnia: <span>'+opinionData['date_add']+'</span>');
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
            //preparing links
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div><div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
            //preparing popups - set id
            $parentEl.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-'+recomId);
            $parentEl.find('.tim-video-popup').attr('id', 'tim-video-popup-'+recomId);
            //preparing popups - set content to photo
            $parentEl.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentEl.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function(item, i){
                photoContent += '<img class="tim-all-photo-popup-images-size" src="'+imgUrl+item+'" alt="img"/>';
            });
            $parentEl.find('.tim-all-photo-popup-container').append(photoContent);
            //preparing popups - set content to video
            $parentEl.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentEl.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/'+opinionData['youtubeVideoId']+'"></iframe>');
            } else {
                $parentEl.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['movie_url'] != 'undefined') {
            //preparing link
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
            //preparing popup - set id
            $parentEl.find('.tim-video-popup').attr('id', 'tim-video-popup-'+recomId);
            //preparing popup - set content
            $parentEl.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentEl.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/'+opinionData['youtubeVideoId']+'"></iframe>');
            } else {
                $parentEl.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['images'][0] != 'undefined') {
            //preparing link
            $parentEl.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div>');
            //preparing popup - set id
            $parentEl.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-'+recomId);
            //preparing popup - set content
            $parentEl.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentEl.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function(item, i){
                photoContent += '<img class="tim-all-photo-popup-images-size" src="'+imgUrl+item+'" alt="img"/>';
            });
            $parentEl.find('.tim-all-photo-popup-container').append(photoContent);
        }
        //render abuse button for opinion
        if (isLoggedIn == '1') {
            $parentEl.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="markUserAbuse('+recomId+','+userId+',\''+userIp+'\',\''+abuseController+'\',\''+userHost+'\')">Zgłoś nadużycie</button>');
        } else {
            $parentEl.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="checkIfUserIsLoggedIn()">Zgłoś nadużycie</button>');
        }
        //render comments
        $parentEl.find('.tim-comment-container').remove();
        opinionData['comments'].forEach(function(item, i){
            if (i > 4) {
                $parentEl.find('.tim-comment').append('<div class="tim-comment-container tim-comment-display-none-'+recomId+' tim-comment-number-'+i+'" style="display:none;"></div>');
            } else {
                $parentEl.find('.tim-comment').append('<div class="tim-comment-container tim-comment-number-'+i+'"></div>');
            }
            $parentEl.find('.tim-comment-number-'+i).append('<div class="tim-comment-container-title tim-comment-title-number-'+i+'"></div>');
            $parentEl.find('.tim-comment-title-number-'+i).append('Comment added <span>' + item['date_add'] + '</span>, user: <span class="tim-last-span'+i+'">' + item['name'] + '</span>');
            //render abuse button for comment
            if (isLoggedIn == '1') {
                $parentEl.find('.tim-last-span'+i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse('+item['recom_id']+','+userId+',\''+userIp+'\',\''+abuseController+'\',\''+userHost+'\')">Zgłoś nadużycie</button>');
            } else {
                $parentEl.find('.tim-last-span'+i).append(' | <button type="button" class="tim-markabuse-button" onclick="checkIfUserIsLoggedIn()">Zgłoś nadużycie</button>');
            }
            //render comment text
            $parentEl.find('.tim-comment-number-'+i).append('<div class="tim-comment-container-content">' + item['comment'] + '</div>');
        });
        //render links 'See more comments' and 'Hide comments'
        $parentEl.find('.tim-comment-seemore').remove();
        if (opinionData['comments'].size() > 5) {
            $parentEl.find('.tim-comment').after('<div class="tim-comment-seemore tim-comment-link-'+ recomId +'">Opinia posiada <span>'+opinionData['comments'].size()+'</span> komentarzy. <a class="tim-readmore" href="#!" onclick="seeAllComments('+ recomId +')">zobacz je wszystkie</a></div><div class="tim-comment-seemore tim-comment-hide-link-'+ recomId +'" style="display: none"><a class="tim-readmore" href="#!" onclick="hideComments('+ recomId +')">Ukryj te komentarze</a></div>');
        }
        //render 'Add comment' button
        $parentEl.find('.tim-comment-add-main').html('<button type="button" class="tim-comment-button-add" id="tim-comment-add-show-'+ recomId +'" onclick="">Add your own comment</button>');
        if (isLoggedIn == '1') {
            $parentEl.find('#tim-comment-add-show-'+ recomId).attr('onclick', 'showAddComment('+ recomId +')');
        } else {
            $parentEl.find('#tim-comment-add-show-'+ recomId).attr('onclick', 'checkIfUserIsLoggedIn()');
        }
        //change data in .comment-form
        var $commentForm = jQuery($parentEl.find('.comment-form'));
        $commentForm.find('.form-recom-id').attr('value', recomId);
        $commentForm.find('.tim-comment-add-window').attr('id', 'tim-comment-add-window-'+recomId);
        $commentForm.find('.tim-opinion-comment-timtoolbar').attr({id: 'tim-opinion-comment-'+recomId, onclick: 'commentPlaceholderAction('+ recomId +')'});
        $commentForm.find('.placeholder-comment-div').attr('id', 'ph-tim-opinion-comment-'+recomId);
        $commentForm.find('.tim-comment-close-form').attr('onclick', 'hideCommentForm('+recomId+')');
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