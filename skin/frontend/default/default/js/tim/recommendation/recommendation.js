jQuery(document).ready(function () {
    addOpinionAjax();
    addCommentAjax();
    changeSortCondition();
    changeCountAndPager();
    getDataOnEnterEvent();
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
 * Get data when Enter button was pressed
 */
function getDataOnEnterEvent() {

    var $increaseButton = jQuery('.tim-pager-increase-button');
    var $decreaseButton = jQuery('.tim-pager-decrease-button');
    var $pageBox = jQuery('.tim-pager-box');
    $pageBox.keyup(function(e){
        if(e.keyCode == 13) {
            var currentPage = parseInt($pageBox.val());
            var maxPage = parseInt(jQuery('.tim-pager-total').text());
            if (currentPage < 1) {
                currentPage = 1;
                $decreaseButton.hide();
                $increaseButton.show();
            }
            if (currentPage > maxPage) {
                currentPage = maxPage;
                $increaseButton.hide();
                $decreaseButton.show();
            }
            if (isNaN(currentPage)) {
                currentPage = 1;
                $decreaseButton.hide();
                $increaseButton.show();
            }
            $pageBox.val(currentPage);
            getTimToolbarData();
        }
    });
}

/**
 * Gets 'number per page' and 'current page' data
 * @param el
 */
function changeCountAndPager(el) {
    if (typeof el != 'undefined') {
        var classList = jQuery(el).attr('class').split(/\s+/);
        var $increaseButton = jQuery('.tim-pager-increase-button');
        var $decreaseButton = jQuery('.tim-pager-decrease-button');
        var $pageBox = jQuery('.tim-pager-box');
        var maxPage = parseInt(jQuery('.tim-pager-total').text());
        var currentPage = parseInt($pageBox.val());
        jQuery.each(classList, function(index, item) {
            switch (item) {
                case 'tim-toolbar-count':
                    jQuery('.'+item).attr('class', 'tim-toolbar-count');
                    jQuery(el).addClass('count-active');
                    break;
                case 'tim-pager-increase-button':
                    currentPage = currentPage + 1;
                    if (currentPage >= maxPage) {
                        currentPage = maxPage;
                        $increaseButton.hide();
                    } else if ((currentPage <= 1) || (!$pageBox.val())) {
                        currentPage = 2;
                    } else {
                        $increaseButton.show();
                    }
                    $decreaseButton.show();
                    $pageBox.val(currentPage);
                    break;
                case 'tim-pager-decrease-button':
                    currentPage = currentPage - 1;
                    if ((currentPage <= 1) || (!$pageBox.val())) {
                        currentPage = 1;
                        $decreaseButton.hide();
                    } else if (currentPage > maxPage) {
                        currentPage = maxPage;
                    } else {
                        $decreaseButton.show();
                    }
                    $increaseButton.show();
                    $pageBox.val(currentPage);
                    break;
            }
        });
        getTimToolbarData();
    }
}

/**
 * Provides onchange event for Sorting
 */
function changeSortCondition() {
    jQuery('.tim-toolbar-select').change(function (){
        getTimToolbarData();
    });
}

/**
 * Gets data from Tim Toolbar and send it to controller
 */
function getTimToolbarData() {
    var dataSet = jQuery('#tim-controller').data();
    var url = dataSet.url;
    var productId = dataSet.product;
    //collect sort data
    var sortBy = jQuery('.tim-toolbar-select').val();
    //collect count per page
    var countPerPage = jQuery('.count-active').text();
    //collect page number
    var $pageBox = jQuery('.tim-pager-box');
    var pageNumber;

    if (!$pageBox.val()) {
        pageNumber = 1;
        $pageBox.val(1);
    } else {
        pageNumber = $pageBox.val();
    }
    //create params array
    var param = {
        sortBy: sortBy,
        productId: productId,
        countPerPage: countPerPage,
        pageNumber: pageNumber
    };

    jQuery.ajax({
        url: url,
        type: "post",
        data: param,
        success: function(response){
            var response = JSON.parse(response);
            //set pages count
            var $pageBox = jQuery('.tim-pager-box');
            var $pagesTotal = jQuery('.tim-pager-total');
            var maxPage = parseInt($pagesTotal.text());
            var $increaseButton = jQuery('.tim-pager-increase-button');
            var $decreaseButton = jQuery('.tim-pager-decrease-button');
            var pagesCount = response[0]['pagesCount'];
            var curPage = response[0]['curPage'];

            if (curPage > pagesCount) {
                $pageBox.val(pagesCount);
                $increaseButton.hide();
            }
            if ((1 < curPage) && (curPage < pagesCount)) {
                $increaseButton.show();
                $decreaseButton.show();
            }
            if (curPage == 1) {
                $increaseButton.show();
                $decreaseButton.hide();
            }
            if (pagesCount == 1) {
                $pageBox.val(1);
                $increaseButton.hide();
                $decreaseButton.hide();
            }
            $pagesTotal.html(response[0]['pagesCount']);
            renderOpinionsList(response);
        }
    });
}

/**
 * Renders opinion list on product_view after it sorted/paginated
 * @param response
 */
function renderOpinionsList(response) {
    var $mainContainer = jQuery('#tim-main-container-for-all-opinions');

    response.forEach(function(item, i){
        //cloning blocks
        var $parentContent = jQuery('.tim-opinion-0').clone();
        var $parentLeft = jQuery('.tim-opinion-user-block-0').clone();
        var $parentRight = jQuery('.tim-opinion-rating-block-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        //assigning right classes for cloned blocks and appending it to main div
        $parentLeft.attr('class', 'tim-comm-left-column tim-opinion-user-block-'+i);
        $parentContent.attr('class', 'tim-comm-main-column tim-opinion-'+i);
        $parentRight.attr('class', 'tim-comm-right-column tim-opinion-rating-block-'+i);
        $mainContainer.append($parentLeft);
        $mainContainer.append($parentContent);
        $mainContainer.append($parentRight);

        var recomId = item['recom_id'];
        var opinionData = item['opinionData'];
        var abuseData = jQuery('.tim-user-log-info').data();
        var isLoggedIn = abuseData.log;
        var abuseController = abuseData.malpractice;
        var userId = abuseData.id;
        var userIp = abuseData.ip;
        var userHost = abuseData.host;

        //Render middle part - view/content.phtml
        //Change date

        $parentContent.find('.tim-opinion-date').html('Opinia z dnia: <span>'+opinionData['date_add']+'</span>');
        //Change advantages
        $parentContent.find('.tim-opinion-advantages-toolbar').html(opinionData['advantages']);
        //Change defects
        $parentContent.find('.tim-opinion-defects-toolbar').html(opinionData['defects']);
        //Change conclusion
        $parentContent.find('.tim-opinion-conclusion-toolbar').html(opinionData['conclusion']);
        //Change images
        $parentContent.find('.tim-opinion-photo').remove();
        $parentContent.find('.tim-opinion-movie').remove();
        if ((typeof opinionData['images'][0] != 'undefined') && (typeof opinionData['movie_url'] != 'undefined')) {
            //preparing links
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div><div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
            //preparing popups - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-'+recomId);
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-'+recomId);
            //preparing popups - set content to photo
            $parentContent.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function(item, i){
                photoContent += '<img class="tim-all-photo-popup-images-size" src="'+imgUrl+item+'" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
            //preparing popups - set content to video
            $parentContent.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/'+opinionData['youtubeVideoId']+'"></iframe>');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['movie_url'] != 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="'+recomId+'">Zobacz materiał filmowy</a></div>');
            //preparing popup - set id
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-'+recomId);
            //preparing popup - set content
            $parentContent.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/'+opinionData['youtubeVideoId']+'"></iframe>');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['images'][0] != 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="'+recomId+'">Zobacz zdjęcia</a></div>');
            //preparing popup - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-'+recomId);
            //preparing popup - set content
            $parentContent.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function(item, i){
                photoContent += '<img class="tim-all-photo-popup-images-size" src="'+imgUrl+item+'" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
        }
        //render abuse button for opinion
        if (isLoggedIn == '1') {
            $parentContent.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="markUserAbuse('+recomId+','+userId+',\''+userIp+'\',\''+abuseController+'\',\''+userHost+'\')">Zgłoś nadużycie</button>');
        } else {
            $parentContent.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="checkIfUserIsLoggedIn()">Zgłoś nadużycie</button>');
        }
        //render comments
        $parentContent.find('.tim-comment-container').remove();
        opinionData['comments'].forEach(function(item, i){
            if (i > 4) {
                $parentContent.find('.tim-comment').append('<div class="tim-comment-container tim-comment-display-none-'+recomId+' tim-comment-number-'+i+'" style="display:none;"></div>');
            } else {
                $parentContent.find('.tim-comment').append('<div class="tim-comment-container tim-comment-number-'+i+'"></div>');
            }
            $parentContent.find('.tim-comment-number-'+i).append('<div class="tim-comment-container-title tim-comment-title-number-'+i+'"></div>');
            $parentContent.find('.tim-comment-title-number-'+i).append('Comment added <span>' + item['date_add'] + '</span>, user: <span class="tim-last-span'+i+'">' + item['name'] + '</span>');
            //render abuse button for comment
            if (isLoggedIn == '1') {
                $parentContent.find('.tim-last-span'+i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse('+item['recom_id']+','+userId+',\''+userIp+'\',\''+abuseController+'\',\''+userHost+'\')">Zgłoś nadużycie</button>');
            } else {
                $parentContent.find('.tim-last-span'+i).append(' | <button type="button" class="tim-markabuse-button" onclick="checkIfUserIsLoggedIn()">Zgłoś nadużycie</button>');
            }
            //render comment text
            $parentContent.find('.tim-comment-number-'+i).append('<div class="tim-comment-container-content">' + item['comment'] + '</div>');
        });
        //render links 'See more comments' and 'Hide comments'
        $parentContent.find('.tim-comment-seemore').remove();
        if (opinionData['comments'].size() > 5) {
            $parentContent.find('.tim-comment').after('<div class="tim-comment-seemore tim-comment-link-'+ recomId +'">Opinia posiada <span>'+opinionData['comments'].size()+'</span> komentarzy. <a class="tim-readmore" href="#!" onclick="seeAllComments('+ recomId +')">zobacz je wszystkie</a></div><div class="tim-comment-seemore tim-comment-hide-link-'+ recomId +'" style="display: none"><a class="tim-readmore" href="#!" onclick="hideComments('+ recomId +')">Ukryj te komentarze</a></div>');
        }
        //render 'Add comment' button
        $parentContent.find('.tim-comment-add-main').html('<button type="button" class="tim-comment-button-add" id="tim-comment-add-show-'+ recomId +'" onclick="">Add your own comment</button>');
        if (isLoggedIn == '1') {
            $parentContent.find('#tim-comment-add-show-'+ recomId).attr('onclick', 'showAddComment('+ recomId +')');
        } else {
            $parentContent.find('#tim-comment-add-show-'+ recomId).attr('onclick', 'checkIfUserIsLoggedIn()');
        }
        //change data in .comment-form
        var $commentForm = jQuery($parentContent.find('.comment-form'));
        $commentForm.find('.form-recom-id').attr('value', recomId);
        $commentForm.find('.tim-comment-add-window').attr('id', 'tim-comment-add-window-'+recomId);
        $commentForm.find('.tim-opinion-comment-timtoolbar').attr({id: 'tim-opinion-comment-'+recomId, onclick: 'commentPlaceholderAction('+ recomId +')'});
        $commentForm.find('.placeholder-comment-div').attr('id', 'ph-tim-opinion-comment-'+recomId);
        $commentForm.find('.tim-comment-close-form').attr('onclick', 'hideCommentForm('+recomId+')');

        //Render left part - view/left.phtml
        var userData = item['userData'];
        //getting magento url
        var imgPath = $parentLeft.find('.tim-user-photo-tag').data().imgurl;
        //setting avatar
        $parentLeft.find('.tim-user-photo-tag').attr('src', imgPath + userData['avatar']);
        //setting customer name
        $parentLeft.find('.tim-user-name').empty().html('<p>Użytkownik</p><p>' + userData['customer_name'] + '</p>');
        //setting user icon
        var skinUrl = $parentLeft.find('.tim-user-type-icon-tag').data().skinurl;
        $parentLeft.find('.tim-user-type-icon-tag').attr('src', skinUrl + 'images/media/userstatus_icon_timworker.png');
        //setting user type
        $parentLeft.find('.tim-user-type-name').html(userData['user_type_name']);
        //setting opinion quantity
        $parentLeft.find('.tim-user-scoregraph').html(userData['opinion_qty'] + ' opinii');
        //setting link to user page
        var baseUrl = $parentLeft.find('.tim-user-about-link').data().baseurl;
        $parentLeft.find('.tim-user-about-link').attr('href', baseUrl + 'recommendation/user/profile/id/' + userData['customer_id']);

        //Render right part - view/right.phtml
        //setting average rating
        $parentRight.find('.tim-rating-score-main').html(opinionData['average_rating'] + '<span>/5<span>');
        //setting price rating
        $parentRight.find('.tim-rating-price').attr('class', 'tim-rating-price tim-chart-stars tim-stars-' + opinionData['rating_price']).html(opinionData['rating_price'] + '<span>/5<span>');
        //setting durability rating
        $parentRight.find('.tim-rating-durability').attr('class', 'tim-rating-durability tim-chart-stars tim-stars-' + opinionData['rating_durability']).html(opinionData['rating_durability'] + '<span>/5<span>');
        //setting failure rating
        $parentRight.find('.tim-rating-failure').attr('class', 'tim-rating-failure tim-chart-stars tim-stars-' + opinionData['rating_failure']).html(opinionData['rating_failure'] + '<span>/5<span>');
        //setting service rating
        $parentRight.find('.tim-rating-service').attr('class', 'tim-rating-service tim-chart-stars tim-stars-' + opinionData['rating_service']).html(opinionData['rating_service'] + '<span>/5<span>');
        //setting purchased result
        $parentRight.find('.byIt-yes-' + recomId).attr('class', 'byIt-yes-' + recomId);
        $parentRight.find('.byIt-no-' + recomId).attr('class', 'byIt-no-' + recomId);
        if (opinionData['by_it'] != null) {
            $parentRight.find('.byIt-yes-' + recomId).addClass('tim-chart-boolean-active');
        } else {
            $parentRight.find('.byIt-no-' + recomId).addClass('tim-chart-boolean-active');
        }
        //setting recommend result
        $parentRight.find('.recommend-yes-' + recomId).attr('class', 'recommend-yes-' + recomId);
        $parentRight.find('.recommend-no-' + recomId).attr('class', 'recommend-no-' + recomId);
        if (opinionData['recommend'] == 1) {
            $parentRight.find('.recommend-yes-' + recomId).addClass('tim-chart-boolean-active');
        } else {
            $parentRight.find('.recommend-no-' + recomId).addClass('tim-chart-boolean-active');
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
            var response = JSON.parse(response);
            displayAjaxCommentPopupResponse(response);
            jQuery('#tim-comment-add-window-'+response['commentRecomId']).hide();
            jQuery('#tim-comment-add-show-'+response['commentRecomId']).show();
        },
        error: function(response) {
            displayAjaxCommentPopupResponse(response);
        }
    });
}

function displayAjaxOpinionPopupResponse(response){
    jQuery('#loading-frontend-mask').hide();
    jQuery('.tim-add-opinion-popup').show(300);
    jQuery('.tim-add-opinion-popup-container p').text(response['message']);
    jQuery('#add-ajax-opinion').prop('disabled', false);
}

function displayAjaxCommentPopupResponse(response){
    jQuery('#loading-frontend-mask').hide();
    jQuery('.tim-add-comment-popup').show(300);
    jQuery('.tim-add-comment-popup-container p').text(response['message']);
    jQuery('#tim-opinion-comment-' + response['commentRecomId']).val('');
    jQuery('#ph-tim-opinion-comment-' + response['commentRecomId']).show();
    jQuery('#add-ajax-comment').prop('disabled', false);
}