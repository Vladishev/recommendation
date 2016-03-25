jQuery(document).ready(function () {
    addOpinionAjax();
    getDataOnEnterEvent();
    changeSortCondition();
    lightRatings();
    changeSortConditionComments();
    getCommentDataOnEnterEvent();
    changeCountAndPager();
    showPhotos();
    showVideo();
    closePopup();
    checkProfile();
    displayRatingStars();
    closePopupByEsc();
    scrollToOpinions();
    userLoginPopup();
    cropperFunctionality();
});

function cropperFunctionality() {
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
                        'width': '900',
                        'height': '550',
                        'top': '25%',
                        'left': '37%',
                        'padding': '20px 20px 50px'
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
                        'width': '330',
                        'height': '230',
                        'top': '50%',
                        'left': '58%',
                        'padding': '20px 20px 50px'
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
                built: function () {
                    $image.cropper('setCropBoxData', {
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

    jQuery('.manage-buttons').click(function () {
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
                success: function (response) {
                    var response = JSON.parse(response);
                    var $preview = jQuery('#tim-' + buttonType + '-container');
                    $preview.html('<img src="' + response['path'] + '?' + date.getTime() + '" id="tim-' + buttonType + '-image" alt="\'banner\'" />');
                    jQuery('#' + buttonType + '-hide').attr('value', response['formData'] + '|' + response['tmpFolder'] + '|' + response['imgFolder']);
                    $popUp.hide();
                }
            });
        }
    });
}

//-------------------- Tim Toolbar start (recommendation/user/profile and recommendation/product/view) ----
/**
 * Get data when Enter button was pressed
 */
function getDataOnEnterEvent() {

    var $increaseButton = jQuery('.tim-pager-increase-button');
    var $decreaseButton = jQuery('.tim-pager-decrease-button');
    var $pageBox = jQuery('.tim-pager-box');
    $pageBox.keyup(function (e) {
        if (e.keyCode == 13) {
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
        jQuery.each(classList, function (index, item) {
            switch (item) {
                case 'tim-toolbar-count':
                    jQuery('.' + item).attr('class', 'tim-toolbar-count');
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
    jQuery('.tim-toolbar-select').change(function () {
        getTimToolbarData();
    });
}

/**
 * Gets data from Tim Toolbar and send it to controller
 */
function getTimToolbarData() {
    var dataSet = jQuery('#tim-controller').data();
    if (dataSet.page == 'productList') {
        hideAddOpinionForm();
    }
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
        success: function (response) {
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

    response.forEach(function (item, i) {
        //cloning blocks
        var $parentList = jQuery('.tim-products-opinion-list-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        //assigning right class for cloned block and appending it to main div
        $parentList.attr('class', 'tim-comm-list-bulk-positions tim-products-opinion-list-' + i);
        $mainContainer.append($parentList);

        //filling row
        $parentList.find('.tim-a-tag').attr('href', item['url']).html('<img src="' + item['image'] + '" alt="Zdjęcie produktu"/>' + item['name']);
        $parentList.find('.tim-comm-list-bulk-rating-barinner').contents().filter(function () {
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
    $pageBox.keyup(function (e) {
        if (e.keyCode == 13) {
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
        jQuery.each(classList, function (index, item) {
            switch (item) {
                case 'tim-toolbar-count-comment':
                    jQuery('.' + item).attr('class', 'tim-toolbar-count-comment');
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
    jQuery('.tim-toolbar-comment-select').change(function () {
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
        success: function (response) {
            var response = JSON.parse(response);
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

    response.forEach(function (item, i) {
        //cloning blocks
        var $parentList = jQuery('.tim-comment-container-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        $parentList.attr('class', 'tim-comment-container-' + i);
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
        jQuery(this).parent().animate({'width': ratingValuePercent + '%'});
        jQuery(this).animate({opacity: '1'}, 1000);
    });
}

/**
 * Renders opinion list on product_view after it sorted/paginated
 * @param response
 */
function renderOpinionsList(response) {
    var $mainContainer = jQuery('#tim-main-container-for-all-opinions');

    response.forEach(function (item, i) {
        //cloning blocks
        var $parentContent = jQuery('.tim-opinion-0').clone();
        var $parentLeft = jQuery('.tim-opinion-user-block-0').clone();
        var $parentRight = jQuery('.tim-opinion-rating-block-0').clone();
        //cleaning main div
        if (i == 0) {
            $mainContainer.empty();
        }

        //assigning right classes for cloned blocks and appending it to main div
        $parentLeft.attr('class', 'tim-comm-left-column tim-opinion-user-block-' + i);
        $parentContent.attr('class', 'tim-comm-main-column tim-opinion-' + i);
        $parentRight.attr('class', 'tim-comm-right-column tim-opinion-rating-block-' + i);
        $mainContainer.append($parentLeft);
        $mainContainer.append($parentContent);
        $mainContainer.append($parentRight);

        //Hide open comments forms if they exist
        jQuery('.tim-comment-add-window').hide();

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

        $parentContent.find('.tim-opinion-date').html('Opinia z dnia: <span>' + opinionData['date_add'] + '</span>');
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
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="' + recomId + '">Zobacz zdjęcia</a></div><div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="' + recomId + '">Zobacz materiał filmowy</a></div>');
            //preparing popups - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-' + recomId);
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-' + recomId);
            //preparing popups - set content to photo
            $parentContent.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function (item, i) {
                photoContent += '<img class="tim-all-photo-popup-images-size" src="' + imgUrl + item + '" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
            //preparing popups - set content to video
            $parentContent.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<input type="hidden" id="tim-youtube-data-' + recomId + '" value="' + opinionData['youtubeVideoId'] + '">');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['movie_url'] != 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-movie"><a class="tim-readmore" href="#" id="' + recomId + '">Zobacz materiał filmowy</a></div>');
            //preparing popup - set id
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-' + recomId);
            //preparing popup - set content
            $parentContent.find('.tim-video-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<input type="hidden" id="tim-youtube-data-' + recomId + '" value="' + opinionData['youtubeVideoId'] + '">');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['images'][0] != 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><a class="tim-readmore tim-opinion-photo-link" href="#" id="' + recomId + '">Zobacz zdjęcia</a></div>');
            //preparing popup - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-' + recomId);
            //preparing popup - set content
            $parentContent.find('.tim-all-photo-popup-container').html('<input type="button" class="tim-popup-close" value="x"/>');
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function (item, i) {
                photoContent += '<img class="tim-all-photo-popup-images-size" src="' + imgUrl + item + '" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
        }
        //render abuse button for opinion
        if (isLoggedIn == '1') {
            $parentContent.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + recomId + ',' + userId + ',\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
        } else {
            $parentContent.find('.tim-abuse-button-position-opinion').html('<button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + recomId + ',0 ,\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
        }
        //render comments
        $parentContent.find('.tim-comment-container').remove();
        opinionData['comments'].forEach(function (item, i) {
            if (i > 4) {
                $parentContent.find('.tim-comment').append('<div class="tim-comment-container tim-comment-display-none-' + recomId + ' tim-comment-number-' + i + '" style="display:none;"></div>');
            } else {
                $parentContent.find('.tim-comment').append('<div class="tim-comment-container tim-comment-number-' + i + '"></div>');
            }
            $parentContent.find('.tim-comment-number-' + i).append('<div class="tim-comment-container-title tim-comment-title-number-' + i + '"></div>');
            $parentContent.find('.tim-comment-title-number-' + i).append('Comment added <span>' + item['date_add'] + '</span>, user: <span class="tim-last-span' + i + '">' + item['name'] + '</span>');
            //render abuse button for comment
            if (isLoggedIn == '1') {
                $parentContent.find('.tim-last-span' + i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + item['recom_id'] + ',' + userId + ',\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
            } else {
                $parentContent.find('.tim-last-span' + i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + item['recom_id'] + ',0 ,\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
            }
            //render comment text
            $parentContent.find('.tim-comment-number-' + i).append('<div class="tim-comment-container-content">' + item['comment'] + '</div>');
        });
        //render links 'See more comments' and 'Hide comments'
        $parentContent.find('.tim-comment-seemore').remove();
        if (opinionData['comments'].size() > 5) {
            $parentContent.find('.tim-comment').after('<div class="tim-comment-seemore tim-comment-link-' + recomId + '">Opinia posiada <span>' + opinionData['comments'].size() + '</span> komentarzy. <a class="tim-readmore" href="#!" onclick="seeAllComments(' + recomId + ')">zobacz je wszystkie</a></div><div class="tim-comment-seemore tim-comment-hide-link-' + recomId + '" style="display: none"><a class="tim-readmore" href="#!" onclick="hideComments(' + recomId + ')">Ukryj te komentarze</a></div>');
        }
        //render 'Add comment' button
        $parentContent.find('.tim-comment-add-main').html('<button type="button" class="tim-comment-button-add" id="tim-comment-add-show-' + recomId + '" onclick="">Dodaj własny komentarz</button>');
        if (isLoggedIn == '1') {
            $parentContent.find('#tim-comment-add-show-' + recomId).attr('onclick', 'showAddComment(' + recomId + ')');
        } else {
            $parentContent.find('#tim-comment-add-show-' + recomId).attr('onclick', 'checkIfUserIsLoggedIn()');
        }
        //change data in .comment-form
        var $commentForm = jQuery($parentContent.find('.comment-form'));
        $commentForm.attr('id', 'form-validate-comment-' + recomId);
        $commentForm.find('.tim-validate-comment-button').attr('id', '#add-ajax-comment-' + recomId);
        $commentForm.find('.tim-validate-comment-button').attr('data-formid', 'form-validate-comment-' + recomId);

        $commentForm.find('.form-recom-id').attr('value', recomId);
        $commentForm.find('.tim-comment-add-window').attr('id', 'tim-comment-add-window-' + recomId);
        $commentForm.find('.tim-opinion-comment-timtoolbar').attr({
            id: 'tim-opinion-comment-' + recomId,
            onclick: 'commentPlaceholderAction(' + recomId + ')',
            onkeyup: 'countCommentChar(' + recomId + ')'
        }).val('');
        removingValidation(jQuery('#tim-opinion-comment-' + recomId));
        $commentForm.find('#ph-tim-opinion-comment-' + recomId).show();
        $commentForm.find('.char-count').attr('id', 'char-count-comment-' + recomId);
        $commentForm.find('.char-count').children('span').text(0);
        $commentForm.find('.placeholder-comment-div').attr('id', 'ph-tim-opinion-comment-' + recomId);
        $commentForm.find('.tim-comment-close-form').attr('onclick', 'hideCommentForm(' + recomId + ')');

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

//Removes 'validation-failed' from cloned form
function removingValidation($el) {
    var classList = $el.attr('class').split(/\s+/);
    var validationRemoved = 0;

    jQuery.each(classList, function (index, item) {
        if (item == 'validation-failed') {
            classList.splice(index, 1);
            validationRemoved = 1;
        }
    });

    if (validationRemoved == 1) {
        $el.attr('class', classList.join(' '));
        jQuery('.validation-advice').remove();
    }
}

function validateCommentForm(el) {
    var data = jQuery(el).data();
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


/**
 * Shows popup if user's profile not fill for input field
 */
function checkProfileInputField() {
    var status = jQuery('#tim-profile-status').val();

    if (status == '0') {
        jQuery('.check-tim-profile-status-popup').show(300);
        return false;
    }
    return true;
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
    
      if (userId == '1') {
        jQuery('input.abuse-email-input').hide();
    }
    
    
    vex.dialog.open({
        className: 'vex-theme-default',
        message: 'Jeżeli masz uwagi dotyczące naruszenia regulaminu strony, co do formy, treści lub zawartości niniejszego wpisu, napisz nam o tym korzystając z poniżeszego pola do opisu zgłoszenia.',
        input: "<input name=\"abusecontent\" type=\"text\" placeholder=\"treść\" required />\n<input name=\"email\" type=\"email\" placeholder=\"e-mail\" class=\"abuse-email-input\"/>",
        buttons: [
    jQuery.extend({}, vex.dialog.buttons.YES, {
                text: 'Wyślij zgłoszenie'
            }), jQuery.extend({}, vex.dialog.buttons.NO, {
                text: 'Zamknij okno'
            })
  ],
        callback: function (data) {
           if (data === true) {
            var param = {
                userId: userId,
                customerHostName: customerHostName,
                customerIp: customerIp,
                recom_id: recomId,
                comment: data.abusecontent,
                email: data.email
            };
            jQuery.ajax({
                url: siteUrl,
                data: param,
                type: 'post',
                success: function () {
                    vex.defaultOptions.className = 'vex-theme-default';
                    vex.dialog.alert('Dziękujemy za informację o nadużyciu. Twoje zgłoszenie zostało przesłane do weryfikacji przez administratora');
                }
            });
        }
        }
    });
}
/**
 * Send customer parameters to controller
 */
function sendParams() {
    var abuseForm = new VarienForm('tim-abuse-popup-form');
    var comment = jQuery('#tim-abuse-application').val();
    var email = jQuery('#tim-abuse-email-input').val();
    var param = {
        userId: userId,
        customerHostName: customerHostName,
        customerIp: customerIp,
        recom_id: recomId,
        comment: comment,
        email: email
    };
    if (abuseForm.validator.validate()) {
        jQuery.ajax({
            url: siteUrl,
            data: param,
            type: 'post',
            success: function (response) {
                jQuery('#tim-abuse-application').hide().val('');
                jQuery('#tim-abuse-application-sendbt').hide();
                jQuery('#tim-abuse-email').hide();
                jQuery('#tim-abuse-email-input').val('');
                jQuery('.tim-markabuse-popup-container p').text('Dziękujemy za informację o nadużyciu. Twoje zgłoszenie zostało przesłane do weryfikacji przez administratora');
            }
        });
    }
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
    //clean inputs
    jQuery("#form-validate-opinion")[0].reset();
    //add placeholders
    jQuery('#ph-tim-opinion-advantages').show();
    jQuery('#ph-tim-opinion-disadvantages').show();
    jQuery('#ph-tim-opinion-summary').show();
    //clean characters count boxes
    jQuery('#char-count-tim-opinion-advantages span').text(0);
    jQuery('#char-count-tim-opinion-disadvantages span').text(0);
    jQuery('#char-count-tim-opinion-summary span').text(0);
    //clean count of stars
    jQuery('.itemValuetomoney').text('0');
    jQuery('.itemDurability').text('0');
    jQuery('.itemFailure').text('0');
    jQuery('.itemEaseofinstall').text('0');
    //clean downloaded files list
    jQuery('#downloaded-imgs').empty();
    //hide form and show general button
    jQuery('#tim-general-add-opinion-button').show();
    jQuery('#tim-add-opinion-layout').hide();
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
            hideAddOpinionForm();
            displayAjaxOpinionPopupResponse(response);
        },
        error: function (response) {
            displayAjaxOpinionPopupResponse(response);
        }
    });
}

/**
 * Save comment by AJAX
 */
function addCommentAjax(el) {
    var formId = jQuery(el).data().formid;
    new VarienForm(formId);
    validateCommentForm(el);

    jQuery(".comment-form").ajaxForm({
        beforeSend: function () {
            jQuery(el).prop('disabled', true);
            jQuery('#loading-frontend-mask-comment').show(300);
        },
        success: function (response) {
            var response = JSON.parse(response);
            displayAjaxCommentPopupResponse(response);
            jQuery(el).prop('disabled', false);
            jQuery('#tim-comment-add-window-' + response['commentRecomId']).hide();
            jQuery('#tim-comment-add-show-' + response['commentRecomId']).show();
        },
        error: function (response) {
            var response = JSON.parse(response);
            displayAjaxCommentPopupResponse(response);
        }
    });
}

function displayAjaxOpinionPopupResponse(response) {
    jQuery('#loading-frontend-mask-opinion').hide();
    		vex.open({
		   	content: jQuery('.tim-add-opinion-popup').html(),
			className: 'vex-theme-default'
		});
    var response = JSON.parse(response);
    jQuery('.tim-add-opinion-popup-container p').text(response['message']);
    jQuery('#add-ajax-opinion').prop('disabled', false);
}

function displayAjaxCommentPopupResponse(response) {
    jQuery('#loading-frontend-mask-comment').hide();
        		vex.open({
		   	content: jQuery('.tim-add-comment-popup').html(),
			className: 'vex-theme-default'
		});
    var commentRecomId = response['commentRecomId'];
    jQuery('.tim-add-comment-popup-container p').text(response['message']);
    jQuery('#tim-opinion-comment-' + commentRecomId).val('');
    jQuery('#ph-tim-opinion-comment-' + commentRecomId).show();
    jQuery('#char-count-comment-' + commentRecomId).children('span').text(0);
}

/**
 * Display filename and check on image rules
 * @param id
 */
function displayFilename(id) {
    var images = '';
    var maxSize = parseInt(jQuery('#recommendation-img').attr('max-size'), 10);
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

        var youtubeVideoId = jQuery('#tim-youtube-data-' + recomId).val();
        var $popup = jQuery('#tim-video-popup-' + recomId);
        var isset = $popup.find('.iframe-video-popup');
        if (typeof youtubeVideoId != 'undefined') {
            if (isset.length == 0) {
                $popup.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/' + youtubeVideoId + '"></iframe>');
            } else {
                $popup.find('.iframe-video-popup').attr("src", "https://www.youtube.com/embed/" + youtubeVideoId);
            }
        }
        $popup.show(300);

/**
 * Close popup action
 */
function closePopup() {
    jQuery(document).on('click', '.tim-popup-close', function () {
        var popupClass = '.' + jQuery(this).parents().get(1).className;
        jQuery(popupClass).hide();
    });
}



/***** VEX based popups for recommendation *****/

/* Check if user is logged in */
function checkIfUserIsLoggedIn() {
    /* function open modal when submit button is pressed without validation yet */
	vex.defaultOptions.className = 'vex-theme-default';    
    vex.dialog.alert('<strong>Uwaga!</strong> Nie jesteś zalogowanym użytkownikiem, aby dodać opinię lub komentarz musisz zalogować się lub założyć konto na naszym serwisie.');
}

/* Shows popup if user's profile not fill */
function checkProfile() {
     jQuery(document).on('click', '.check-tim-profile-status', function () {
        var status = jQuery('#tim-profile-status').val();
        if (status == '0') {
		vex.open({
		   	content: jQuery('.check-tim-profile-status-popup').html(),
			className: 'vex-theme-default'
		});
            return false;
        }
        return true;
    });
}

/* Check click action and show popup with opinion photo */
function showPhotos() {
	jQuery(document).on('click', '.tim-opinion-photo-link', function (e) {
		vex.open({
		   	content: jQuery('#tim-all-photo-popup-' + e.target.id).html(),
			className: 'vex-theme-default'
		});
	});
}

/* Check click action and show popup with opinion video */
function showVideo() {
    jQuery(document).on('click', '.tim-opinion-movie', function (e) {
		vex.open({
		   	content: jQuery('#tim-video-popup-' + e.target.id).html(),
			className: 'vex-theme-default'
		});
        return false;
    });
}

/**
 * Display selected qty of stars on add opinion view
 */
function displayRatingStars() {
    jQuery('.tim-rating-input-span').on('change', function () {
        var inputChangeName = jQuery(this).attr('name');
        var inputChangeValue = jQuery(this).val();
        var inputChangeNameSpan = 'span.' + inputChangeName;
        jQuery(inputChangeNameSpan).html(inputChangeValue);
    });
}