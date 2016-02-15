jQuery(document).ready(function () {
    addOpinionAjax();
    addCommentAjax();
    validateCommentForm();
    getDataOnEnterEvent();
    changeSortCondition();
    lightRatings();
    changeSortConditionComments();
    getCommentDataOnEnterEvent();

    /* function to put value into html content right to rating stars and switch userlogin details*/

    jQuery('input').on('change', function () {
        var inputChangeName = jQuery(this).attr('name');
        var inputChangeValue = jQuery(this).val();
        var inputChangeNameSpan = 'span.' + inputChangeName;
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

    // Scroll to opinions for app/design/frontend/default/default/template/tim/recommendation/rating/product_view.phtml
    jQuery('#tim-scroll').click(function(){
        jQuery('html, body').animate({
            scrollTop: jQuery( jQuery(this).attr('href') ).offset().top
        }, 500);
        return false;
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

    //------------------------ Cropper functionality -------------------------------

    'use strict';
    var $image = jQuery('#tim-crop-image');
    var $inputImage = jQuery('.upload-buttons');

    $inputImage.on('click', function () {
        $inputImage = jQuery(this);
    });

    var URL = window.URL || window.webkitURL;
    var blobURL;
    var $popUp = jQuery('.tim-crop-image');

    if (URL) {
        $inputImage.change(function () {
            var files = this.files;
            var checkFile = this.files[0];
            var file;
            var id = jQuery(this).attr('id');
            var height;
            var width;
            var ratio;
            var $conteiner = jQuery('.tim-crop-image-container');
            var $info = jQuery('.tim-popup-info');

            //Here you can change popup and cropper settings
            switch (id) {
                case 'banner':
                    $image.cropper('destroy');
                    width = 860;
                    height = 500;
                    ratio = 1.72 / 1;
                    $conteiner.css({
                        'width':'900',
                        'height':'550',
                        'top':'25%',
                        'left':'37%',
                        'padding':'20px 20px 50px'
                    });
                    $info.html('Cropped image have to be not more then cropper window');
                    //set banner file name
                    jQuery('.tim-banner-file-name').html(checkFile.name);
                    break;
                case 'avatar':
                    $image.cropper('destroy');
                    width = 290;
                    height = 180;
                    ratio = 1.61 / 1;
                    $conteiner.css({
                        'width':'330',
                        'height':'230',
                        'top':'50%',
                        'left':'58%',
                        'padding':'20px 20px 50px'
                    });
                    $info.html('Cropped image have to be not more then cropper window');
                    //set avatar file name
                    jQuery('.tim-avatar-file-name').html(checkFile.name);
                    break;
            }

            $image.cropper({
                aspectRatio: ratio,
                minCropBoxWidth: width,
                minCropBoxHeight: height,
                built: function(){
                    $image.cropper('setCropBoxData',{
                        width: width,
                        height: height
                    });
                }
            });

            //Checking file size and type
            if (checkFile.size > 419430) {
                alert('Nie można przesłać pliku. Maksymalny rozmiar to 400 kb.');
                return;
            }
            switch (checkFile.type) {
                case 'image/png':
                case 'image/jpeg':
                    break;
                default:
                    alert('Nie można przesłać pliku. Dopuszczalne są pliki graficzne w formacie jpg lub png.');
                    return false;
            }

            if (!$image.data('cropper')) {
                return;
            }

            $popUp.show(300);
            if (files && files.length) {
                file = files[0];

                if (/^image\/\w+$/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    $image.one('built.cropper', function () {

                        // Revoke when load complete
                        URL.revokeObjectURL(blobURL);
                    }).cropper('reset').cropper('replace', blobURL);
                    $inputImage.val('');
                } else {
                    window.alert('Please choose an image file.');
                }
            }
        });
    } else {
        $inputImage.prop('disabled', true).parent().addClass('disabled');
    }

    jQuery('.manage-buttons').click(function (){
        var data = jQuery(this).data();

        switch (data.method) {
            case 'setDragMode':
                $image.cropper('setDragMode', 'move');
                break;
            case 'zoom-in':
                $image.cropper('zoom', 0.1);
                break;
            case 'zoom-out':
                $image.cropper('zoom', -0.1);
                break;
        }
    });

// Methods
    jQuery('.docs-buttons').on('click', function () {
        var $this = jQuery(this);
        var data = $this.data();
        var $target;
        var result;
        var date = new Date();
        var buttonType = $inputImage.attr('id');

        if ($this.prop('disabled') || $this.hasClass('disabled')) {
            return;
        }

        if ($image.data('cropper') && data.method) {

            data = jQuery.extend({}, data); // Clone a new one
            if (typeof data.target !== 'undefined') {

                $target = jQuery(data.target);
                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }

            result = $image.cropper(data.method, data.option, data.secondOption);

            var sendData = {data: result.toDataURL(), typeOfImage: buttonType};
            var url = data.url;

            jQuery.ajax({
                url: url,
                type: "post",
                data: sendData,
                success: function(response){
                    var response = JSON.parse(response);
                    var $preview = jQuery('#tim-'+buttonType+'-image');
                    $preview.attr('src', response['path']+'?'+date.getTime());
                    jQuery('#'+buttonType+'-hide').attr('value', response['formData']+'|'+response['tmpFolder']+'|'+response['imgFolder']);
                    $popUp.hide();
                }
            });
        }
    });
//    ---------------------------- End of cropper functionality ------------------------

});

//-------------------- Tim Toolbar start (recommendation/user/profile and recommendation/product/view) ----
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
    //collect user id(for user page)
    var userId = dataSet.userid;
    //create params array
    var param = {
        sortBy: sortBy,
        productId: productId,
        countPerPage: countPerPage,
        pageNumber: pageNumber,
        userId: userId
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
            if (dataSet.page == 'userPage') {
                renderProductOpinionList(response);
            } else {
                renderOpinionsList(response);
            }
        }
    });
}

/**
 * Renders product list and rating
 * @param response
 */
function renderProductOpinionList(response) {
    var $mainContainer = jQuery('#tim-list-container');

    response.forEach(function(item, i) {
        //cloning blocks
        var $parentList = jQuery('.tim-products-opinion-list-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        //assigning right class for cloned block and appending it to main div
        $parentList.attr('class', 'tim-comm-list-bulk-positions tim-products-opinion-list-'+i);
        $mainContainer.append($parentList);

        //filling row
        $parentList.find('.tim-a-tag').attr('href', item['url']).html('<img src="' + item['image'] + '" alt="Zdjęcie produktu"/>' + item['name']);
        $parentList.find('.tim-comm-list-bulk-rating-barinner').contents().filter(function(){
            return this.nodeType == 3;
        })[0].nodeValue = item['rating'];
    });
    lightRatings();
}
//---------------------------- Tim Toolbar end ------------------------------------------------------

//---------------------------- Comments toolbar start (recommendation/user/profile)------------------
/**
 * Get data when Enter button was pressed
 */
function getCommentDataOnEnterEvent() {

    var $increaseButton = jQuery('.tim-pager-comment-increase-button');
    var $decreaseButton = jQuery('.tim-pager-comment-decrease-button');
    var $pageBox = jQuery('.tim-comment-pager-box');
    $pageBox.keyup(function(e){
        if(e.keyCode == 13) {
            var currentPage = parseInt($pageBox.val());
            var maxPage = parseInt(jQuery('.tim-comment-pager-total').text());
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
            getCommentsToolbarData()
        }
    });
}

/**
 * Gets 'number per page' and 'current page' data for Comments Toolbar
 * @param el
 */
function changeCountAndPagerComments(el) {
    if (typeof el != 'undefined') {
        var classList = jQuery(el).attr('class').split(/\s+/);
        var $increaseButton = jQuery('.tim-pager-comment-increase-button');
        var $decreaseButton = jQuery('.tim-pager-comment-decrease-button');
        var $pageBox = jQuery('.tim-comment-pager-box');
        var maxPage = parseInt(jQuery('.tim-comment-pager-total').text());
        var currentPage = parseInt($pageBox.val());
        jQuery.each(classList, function(index, item) {
            switch (item) {
                case 'tim-toolbar-count-comment':
                    jQuery('.'+item).attr('class', 'tim-toolbar-count-comment');
                    jQuery(el).addClass('count-active-comment');
                    break;
                case 'tim-pager-comment-increase-button':
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
                case 'tim-pager-comment-decrease-button':
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
        getCommentsToolbarData();
    }
}

/**
 * Provides onchange event for Sorting comments
 */
function changeSortConditionComments() {
    jQuery('.tim-toolbar-comment-select').change(function (){
        getCommentsToolbarData();
    });
}

/**
 * Gets data from Comments Tim Toolbar and send it to controller
 */
function getCommentsToolbarData() {
    var dataSet = jQuery('#comments-controller').data();
    var url = dataSet.url;
    //collect sort data
    var sortBy = jQuery('.tim-toolbar-comment-select').val();
    //collect count per page
    var countPerPage = jQuery('.count-active-comment').text();
    //collect page number
    var $pageBox = jQuery('.tim-comment-pager-box');
    var pageNumber;

    if (!$pageBox.val()) {
        pageNumber = 1;
        $pageBox.val(1);
    } else {
        pageNumber = $pageBox.val();
    }
    //collect user id(for user page)
    var userId = dataSet.userid;
    //create params array
    var param = {
        sortBy: sortBy,
        countPerPage: countPerPage,
        pageNumber: pageNumber,
        userId: userId
    };

    jQuery.ajax({
        url: url,
        type: "post",
        data: param,
        success: function(response){
            var response = JSON.parse(response);
            //console.log(response);
            //set pages count
            var $pageBox = jQuery('.tim-comment-pager-box');
            var $pagesTotal = jQuery('.tim-comment-pager-total');
            var $increaseButton = jQuery('.tim-pager-comment-increase-button');
            var $decreaseButton = jQuery('.tim-pager-comment-decrease-button');
            var pagesCount = response[0]['pagesCount'];
            var curPage = response[0]['curPage'];

            if (curPage > pagesCount) {
                $pageBox.val(pagesCount);
                $increaseButton.hide();
                $decreaseButton.show();
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
            renderCommentsList(response);
        }
    });
}

/**
 * Renders comments list
 * @param response
 */
function renderCommentsList(response) {
    var $mainContainer = jQuery('.tim-comment');

    response.forEach(function(item, i) {
        //cloning blocks
        var $parentList = jQuery('.tim-comment-container-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        $parentList.attr('class', 'tim-comment-container-'+i);
        $mainContainer.append($parentList);
        //filling row
        $parentList.find('.comment-date-add').html(item['date_add']);
        $parentList.find('.tim-comment-name-link').attr('href', item['url']).html(item['name']);
        $parentList.find('.tim-comment-container-content').html(item['comment']);
    });
}
//---------------------------- Comments toolbar end -----------------------------------------------

/**
 * Lighted rating boxes
 */
function lightRatings() {
    jQuery('.tim-comm-list-bulk-rating-barinner').each(function () {
        var ratingValue = jQuery(this).contents().filter(function () {
            return this.nodeType == 3;
        }).text();
        var ratingValuePercent = ((ratingValue * 19.2) / 100) * 100;
//        console.log(ratingValuePercent);
        jQuery(this).parent().animate({'width': ratingValuePercent + '%'});
        jQuery(this).animate({opacity: '1'}, 1000);
    });
}

function renderOpinionsList(response) {
//    merge here method from #4113
}

function validateCommentForm() {
    jQuery('.tim-validate-comment-button').on('click', this, function() {
        var data = jQuery(this).data();
        var minChar = data.mincharacters;
        var maxChar = data.maxcharacters;
        var commentMin = data.commentmin;
        var commentMax = data.commentmax;

        Validation.add('min-length-comment', commentMin, function (v) {
            var min = minChar;
            if (min) {
                if (v.length < min) {
                    return false;
                }
            }
            return true;
        });

        Validation.add('max-length-comment', commentMax, function (v) {
            var max = maxChar;
            if (max) {
                if (v.length > max) {
                    return false;
                }
            }
            return true;
        });
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
function hidePlaceholder(data) {
    var id = jQuery(data).attr('id');
    var placeholder = jQuery('#ph-' + id);
    placeholder.hide();
    jQuery('#' + id + '_ifr').focusout(function () {
        if (!placeholder.val()) {
            placeholder.show();
        }
    });
}

/**
 * Show placeholder on tinyMce
 * @param ed
 */
function showPlaceholder(ed) {
    var id = jQuery(ed).attr('id');
    var placeholder = jQuery('#ph-' + id);
    tinyMCE.triggerSave();
    if (!jQuery('#' + id).val()) {
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
function addOpinionAjax() {
    jQuery("#form-validate-opinion").ajaxForm({
        beforeSend: function () {
            jQuery('#add-ajax-opinion').prop('disabled', true);
            jQuery('#loading-frontend-mask-opinion').show(300);
        },
        success: function (response) {
            displayAjaxOpinionPopupResponse(response);
            jQuery("#form-validate-opinion")[0].reset();
        },
        error: function (response) {
            displayAjaxOpinionPopupResponse(response);
        }
    });
}

/**
 * Save comment by AJAX
 */
function addCommentAjax() {
    jQuery(".comment-form").ajaxForm({
        beforeSend: function () {
            jQuery('#add-ajax-comment').prop('disabled', true);
            jQuery('#loading-frontend-mask-comment').show(300);
        },
        success: function (response) {
            displayAjaxCommentPopupResponse(response);
        },
        error: function (response) {
            displayAjaxCommentPopupResponse(response);
        }
    });
}

function displayAjaxOpinionPopupResponse(response) {
    jQuery('#loading-frontend-mask-opinion').hide();
    jQuery('.tim-add-opinion-popup').show(300);
    var response = JSON.parse(response);
    jQuery('.tim-add-opinion-popup-container p').text(response['message']);
    jQuery('#add-ajax-opinion').prop('disabled', false);
    jQuery('#char-count-tim-opinion-advantages span').text('0');
    jQuery('#ph-tim-opinion-advantages').show();
    jQuery('#char-count-tim-opinion-disadvantages span').text(0);
    jQuery('#ph-tim-opinion-disadvantages').show();
    jQuery('#char-count-tim-opinion-summary span').text(0);
    jQuery('#ph-tim-opinion-summary').show();
}

function displayAjaxCommentPopupResponse(response) {
    jQuery('#loading-frontend-mask-comment').hide();
    jQuery('.tim-add-comment-popup').show(300);
    var response = JSON.parse(response);
    var commentRecomId = response['commentRecomId'];
    jQuery('.tim-add-comment-popup-container p').text(response['message']);
    jQuery('#tim-opinion-comment-' + commentRecomId).val('');
    jQuery('#ph-tim-opinion-comment-' + commentRecomId).show();
    jQuery('#add-ajax-comment').prop('disabled', false);
    jQuery('#char-count-comment-' + commentRecomId).children('span').text(0);
}

/**
 * Display filename and check on image rules
 * @param id
 */
function displayFilename(id) {
    var images = '';
    var maxSize = parseInt(jQuery('#recommendation-img').attr('max-size'),10);
    jQuery.each(jQuery('#' + id).prop('files'), function (idx, file) {
        if (checkImgSize(file['size'], maxSize)) {
            if (checkImgType(file)) {
                images += ' ' + file['name'] + '<br>';
            } else {
                alert('Nie można przesłać pliku: ' + file['name'] + '. Dopuszczalne są pliki graficzne w formacie jpg lub png.');
            }
        } else {
            alert("Nie można przesłać pliku: " + file['name'] + ". Maksymalny rozmiar to 5 mb.");
        }
    });
    jQuery('#downloaded-imgs').html(images);
}
/**
 * Check image size
 * @param fileSize
 * @param limit
 * @returns {boolean}
 */
function checkImgSize(fileSize, limit) {
    if (fileSize < limit) {
        return true;
    }
    return false;
}

/**
 * Check image type
 * @param file
 * @returns {boolean}
 */
function checkImgType(file) {
    switch (file['type']) {
        case 'image/png':
        case 'image/jpeg':
            return true;
        default:
            return false;
    }
}

function countCommentChar(commentId) {
    var text = jQuery('#tim-opinion-comment-' + commentId).val();
    var charCount = text.length;
    jQuery('#char-count-comment-' + commentId).children('span').text(charCount);
}