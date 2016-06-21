jQuery(document).ready(function () {
    getDataOnEnterEvent();
    changeSortCondition();
    lightRatings();
    changeSortConditionComments();
    getCommentDataOnEnterEvent();
    changeCountAndPager();
    changeCountAndPagerComments();
});
//-------------------- Tim Toolbar start --------------------------//
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
 */
function changeCountAndPager() {
    jQuery('.tim-sort-action-class').on('click', function (){
        if (typeof this !== 'undefined') {
            var el = jQuery(this);
            var $increaseButton = jQuery('.tim-pager-increase-button');
            var $decreaseButton = jQuery('.tim-pager-decrease-button');
            var $pageBox = jQuery('.tim-pager-box');
            var maxPage = parseInt(jQuery('.tim-pager-total').text());
            var currentPage = parseInt($pageBox.val());

            if (el.hasClass('tim-toolbar-count')) {
                jQuery('.tim-toolbar-count').attr('class', 'tim-toolbar-count tim-sort-action-class');
                jQuery(el).addClass('count-active');
            }
            if (el.hasClass('tim-pager-increase-button')) {
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
            }
            if (el.hasClass('tim-pager-decrease-button')) {
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
            }
            getTimToolbarData();
        }
    });
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
            renderProductOpinionList(response);
        }
    });
}

/**
 * Renders product list and rating
 * @param response
 */
function renderProductOpinionList(response) {
    var $mainContainer = jQuery('#tim-list-container');
    var nodeNumber = 3;

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
        $parentList.find('.tim-a-tag').attr('href', item['url']).html('<img src="' + item['image'] + '" alt="' + Translator.translate('Product image') + '"/>' + item['name']);
        $parentList.find('.tim-comm-list-bulk-rating-barinner').contents().filter(function () {
            return this.nodeType == nodeNumber;
        })[0].nodeValue = item['rating'];
    });
    lightRatings();
}

/**
 * Lighted rating boxes
 */
function lightRatings() {
    var nodeNumber = 3;
    //width in percents for one star
    var oneStar = 19.2;
    //width in percents for five stars
    var totalWidth = 100;
    jQuery('.tim-comm-list-bulk-rating-barinner').each(function () {
        var ratingValue = jQuery(this).contents().filter(function () {
            return this.nodeType == nodeNumber;
        }).text();
        var ratingValuePercent = ((ratingValue * oneStar) / totalWidth) * 100;
        jQuery(this).parent().animate({'width': ratingValuePercent + '%'});
        jQuery(this).animate({opacity: '1'}, 1000);
    });
}
//-------------------- Tim Toolbar end --------------------------//

//--------------- Comments toolbar start -------------------------//
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
 */
function changeCountAndPagerComments() {
    jQuery('.tim-comment-sort-action-class').on('click', function () {
        if (typeof this !== 'undefined') {
            var el = jQuery(this);
            var $increaseButton = jQuery('.tim-pager-comment-increase-button');
            var $decreaseButton = jQuery('.tim-pager-comment-decrease-button');
            var $pageBox = jQuery('.tim-comment-pager-box');
            var maxPage = parseInt(jQuery('.tim-comment-pager-total').text());
            var currentPage = parseInt($pageBox.val());
            if (el.hasClass('tim-toolbar-count-comment')) {
                jQuery('.tim-toolbar-count-comment').attr('class', 'tim-toolbar-count-comment tim-comment-sort-action-class');
                jQuery(el).addClass('count-active-comment');
            }
            if (el.hasClass('tim-pager-comment-increase-button')) {
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
            }
            if (el.hasClass('tim-pager-comment-decrease-button')) {
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
            }
            getCommentsToolbarData();
        }
    });
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

        $parentList.attr('class', 'tim-comment-container tim-comment-container-' + i);
        $mainContainer.append($parentList);
        //filling row
        $parentList.find('.comment-date-add').html(item['date_add']);
        $parentList.find('.tim-comment-name-link').attr('href', item['url']).html(item['name']);
        $parentList.find('.tim-comment-container-content').html(item['comment']);
    });
}
//--------------- Comments toolbar end -------------------------//
