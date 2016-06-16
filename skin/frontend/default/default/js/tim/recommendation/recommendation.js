jQuery(document).ready(function () {
    addOpinionAjax();
    getDataOnEnterEvent();
    changeSortCondition();
    lightRatings();
    changeCountAndPager();
    showPhotos();
    showVideo();
    closePopup();
    checkProfile();
    displayRatingStars();
    scrollToAddOpinion();
    addExtraValidation();
    countOpinionChars();
    openAddOpinionByHash();
});
//------------------------------------------- product view start -------------------------------------------//
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
        $parentRight.attr('class', 'tim-comm-right-column tim-opinion-rating-block-' + i);
        $parentContent.attr('class', 'tim-comm-main-column tim-opinion-' + i);
        $mainContainer.append($parentLeft);
        $mainContainer.append($parentRight);
        $mainContainer.append($parentContent);

        //Hide open comments forms if they exist
        jQuery('.tim-comment-add-window').hide();

        var recomId = item['recom_id'];
        var opinionData = item['opinionData'];
        var abuseData = jQuery('.tim-user-log-info').data();
        var isLoggedIn = abuseData.log;
        var abuseController = abuseData.abusecontroller;
        var userId = abuseData.id;
        var userIp = abuseData.ip;
        var userHost = abuseData.host;

        //Render middle part - view/content.phtml
        //Change date
        var abuseButtonHtml;
        if (isLoggedIn == '1') {
            abuseButtonHtml = '<button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + recomId + ',' + userId + ',\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>'
        } else {
            abuseButtonHtml = '<button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + recomId + ',0 ,\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>'
        }
        $parentContent.find('.tim-opinion-date-value').html('Opinia z dnia: <span>' + opinionData['date_add'] + '</span> | ');
        $parentContent.find('.tim-opinion-date-value').append(abuseButtonHtml);
        //Change advantages
        $parentContent.find('.tim-opinion-advantages-toolbar').html(opinionData['advantages']);
        //Change defects
        $parentContent.find('.tim-opinion-defects-toolbar').html(opinionData['defects']);
        //Change conclusion
        $parentContent.find('.tim-opinion-conclusion-toolbar').html(opinionData['conclusion']);
        //Change images
        $parentContent.find('.tim-opinion-photo').remove();
        $parentContent.find('.tim-opinion-movie').remove();
        $parentContent.find('.tim-all-photo-popup-container').empty();
        $parentContent.find('.tim-video-popup-container').empty();
        $parentContent.find('.tim-all-photo-popup').attr('id', '');
        $parentContent.find('.tim-video-popup').attr('id', '');
        if ((typeof opinionData['images'][0] !== 'undefined') && (typeof opinionData['movie_url'] !== 'undefined')) {
            //preparing links
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><div class="tim-readmore tim-opinion-photo-link" id="' + recomId + '">Zobacz zdjęcia</div></div><div class="tim-opinion-movie"><div class="tim-readmore" id="' + recomId + '">Zobacz materiał filmowy</div></div>');
            //preparing popups - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-' + recomId);
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-' + recomId);
            //preparing popups - set content to photo
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function (item, i) {
                photoContent += '<img class="tim-all-photo-popup-images-size" src="' + imgUrl + item + '" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
            //preparing popups - set content to video
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<input type="hidden" id="tim-youtube-data-' + recomId + '" value="' + opinionData['youtubeVideoId'] + '">');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['movie_url'] !== 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-movie"><div class="tim-readmore" id="' + recomId + '">Zobacz materiał filmowy</div></div>');
            //preparing popup - set id
            $parentContent.find('.tim-video-popup').attr('id', 'tim-video-popup-' + recomId);
            //preparing popup - set content
            if (opinionData['youtubeVideoId']) {
                $parentContent.find('.tim-video-popup-container').append('<input type="hidden" id="tim-youtube-data-' + recomId + '" value="' + opinionData['youtubeVideoId'] + '">');
            } else {
                $parentContent.find('.tim-video-popup-container').append('User have added video not from the youtube.');
            }
        } else if (typeof opinionData['images'][0] !== 'undefined') {
            //preparing link
            $parentContent.find('.tim-opinion-media').html('<div class="tim-opinion-photo"><div class="tim-readmore tim-opinion-photo-link" id="' + recomId + '">Zobacz zdjęcia</div></div>');
            //preparing popup - set id
            $parentContent.find('.tim-all-photo-popup').attr('id', 'tim-all-photo-popup-' + recomId);
            //preparing popup - set content
            var photoContent = '';
            var imgUrl = $parentContent.find('.tim-all-photo-popup').data().imgurl;
            opinionData['images'].forEach(function (item, i) {
                photoContent += '<img class="tim-all-photo-popup-images-size" src="' + imgUrl + item + '" alt="img"/>';
            });
            $parentContent.find('.tim-all-photo-popup-container').append(photoContent);
        }
        //render comments
        $parentContent.find('.tim-comment-container').remove();
        opinionData['comments'].forEach(function (item, i) {
            if (i > 4) {
                $parentContent.find('.tim-comments-to-opinion').append('<div class="tim-comment-container tim-comment-display-none-' + recomId + ' tim-comment-number-' + i + '" style="display:none;"></div>');
            } else {
                $parentContent.find('.tim-comments-to-opinion').append('<div class="tim-comment-container tim-comment-number-' + i + '"></div>');
            }
            $parentContent.find('.tim-comment-number-' + i).append('<div class="tim-comment-container-title tim-comment-title-number-' + i + '"></div>');
            $parentContent.find('.tim-comment-title-number-' + i).append('<div class="tim-comment-container-added-' + i + '">Komentarz dodany <span>' + item['date_add'] + '</span> przez użytkownika: <span class="tim-last-span' + i + '">' + item['name'] + '</span></div>');
            //render abuse button for comment
            if (isLoggedIn == '1') {
                $parentContent.find('.tim-comment-container-added-' + i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + item['recom_id'] + ',' + userId + ',\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
            } else {
                $parentContent.find('.tim-comment-container-added-' + i).append(' | <button type="button" class="tim-markabuse-button" onclick="markUserAbuse(' + item['recom_id'] + ',0 ,\'' + userIp + '\',\'' + abuseController + '\',\'' + userHost + '\')">Zgłoś nadużycie</button>');
            }
            //render comment text
            $parentContent.find('.tim-comment-number-' + i).append('<div class="tim-comment-container-content">' + item['comment'] + '</div>');
        });
        //render links 'See more comments' and 'Hide comments'
        $parentContent.find('.tim-comment-seemore').remove();
        if (opinionData['comments'].size() > 5) {
            $parentContent.find('.tim-comment').after('<div class="tim-comment-seemore tim-comment-link-' + recomId + '">Opinia posiada <span>' + opinionData['comments'].size() + '</span> komentarzy. <a class="tim-readmore-comments" href="#!" onclick="seeAllComments(' + recomId + ')">zobacz je wszystkie</a></div><div class="tim-comment-seemore tim-comment-hide-link-' + recomId + '" style="display: none"><a class="tim-readmore-comments" href="#!" onclick="hideComments(' + recomId + ')">Ukryj te komentarze</a></div>');
        }
        //render 'Add comment' button
        $parentContent.find('.tim-comment-add-main').remove();
        $parentContent.find('.tim-comments-to-opinion').append('<div class="tim-comment-add-main"><button type="button" class="tim-comment-button-add" id="tim-comment-add-show-' + recomId + '" onclick="">Dodaj własny komentarz</button></div>');
        if (isLoggedIn == '1') {
            $parentContent.find('#tim-comment-add-show-' + recomId).attr('onclick', 'showAddComment(' + recomId + ')');
        } else {
            $parentContent.find('#tim-comment-add-show-' + recomId).attr('onclick', 'checkIfUserIsLoggedIn()');
        }
        //change data in .comment-form
        var $commentForm = jQuery($parentContent.find('.tim-comment'));
        $commentForm.find('.comment-form').attr('id', 'form-validate-comment-' + recomId);
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
        var baseUrl = $parentLeft.find('.tim-user-about-link').data().baseurl;
        //getting magento url
        //var imgPath = $parentLeft.find('.tim-user-photo-tag').data().imgurl;
        //setting avatar
        //$parentLeft.find('.tim-user-photo-tag').attr('src', imgPath + userData['avatar']);
        //setting customer name
        $parentLeft.find('.tim-user-name-title').html('Użytkownik:');
        $parentLeft.find('.tim-user-name-about-link').html('<span>' + userData['customer_name'] + '</span>');
        $parentLeft.find('.tim-user-opinion-qty').html('(' + userData['opinion_qty'] + ' wpisów)');

        //setting user icon
        //var skinUrl = $parentLeft.find('.tim-user-type-icon-tag').data().skinurl;
        //$parentLeft.find('.tim-user-type-icon-tag').attr('src', skinUrl + 'media/userstatus_icon_timworker.png');
        //setting user type
        $parentLeft.find('.tim-user-type-name').html(userData['user_type_name']);
        //setting opinion quantity
        $parentLeft.find('.tim-user-scoregraph').attr('class', 'tim-user-scoregraph tim-score-' + userData['user_score']);
        //setting link to user page
        //var baseUrl = $parentLeft.find('.tim-user-about-link').data().baseurl;
        $parentLeft.find('.tim-user-about-link').attr('href', baseUrl + 'recommendation/user/profile/id/' + userData['customer_id']);

        //Render right part - view/right.phtml
        //setting average rating
        $parentRight.find('.tim-rating-score-main').html(opinionData['average_rating'] + '<span>/5<span>');
        //setting price rating
        $parentRight.find('.tim-rating-price').attr('class', 'tim-rating-price tim-chart-stars tim-stars-' + opinionData['rating_price']);
        //setting durability rating
        $parentRight.find('.tim-rating-durability').attr('class', 'tim-rating-durability tim-chart-stars tim-stars-' + opinionData['rating_durability']);
        //setting failure rating
        $parentRight.find('.tim-rating-failure').attr('class', 'tim-rating-failure tim-chart-stars tim-stars-' + opinionData['rating_failure']);
        //setting service rating
        $parentRight.find('.tim-rating-service').attr('class', 'tim-rating-service tim-chart-stars tim-stars-' + opinionData['rating_service']);
        //setting purchased result
        $parentRight.find('.tim-use-it-yes-no').attr('id', 'tim-use-it-' + recomId);
        if (opinionData['use_it'] == 1) {
            $parentRight.find('#tim-use-it-' + recomId).html('<span class="byIt-yes">Tak</span>');
        } else {
            $parentRight.find('#tim-use-it-' + recomId).html('<span class="use-it-yes">Nie</span>');
        }
        //setting recommend result
        $parentRight.find('.tim-recommend-yes-no').attr('id', 'tim-recommend-' + recomId);
        if (opinionData['recommend'] == 1) {
            $parentRight.find('#tim-recommend-' + recomId).html('<span class="recommend-yes">Tak</span>');
        } else {
            $parentRight.find('#tim-recommend-' + recomId).html('<span class="recommend-no">Nie</span>');
        }
    });
}

/**
 * Removes 'validation-failed' from cloned form
 * @param $el
 */
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
 * Variables for markUserAbuse() and sendParams() methods
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
    vex.dialog.open({
        afterOpen: function ($vexContent) {
            if (userId == '0') {
                var $el = "<input name='abusecontent' id='abusecontent' type='text' placeholder='treść' required/>\n<input name='email' id='abuseemail' type='email' placeholder='e-mail' class='abuse-email-input' required/>";
            } else {
                var $el = "<input name='abusecontent' id='abusecontent' type='text' placeholder='treść' required/>";
            }
            return jQuery('.vex-dialog-input').append($el);
        },
        className: 'vex-theme-default',
        message: Translator.translate('If you have any comments regarding violations of the Rules of the page, as to the form, content or the content of this post, write us about it using the text field to describe the application.'),
        buttons: [
            jQuery.extend({}, vex.dialog.buttons.YES, {
                text: Translator.translate('Send report')
            }), jQuery.extend({}, vex.dialog.buttons.NO, {
                text: Translator.translate('Close the window')
            })
        ],
        callback: function (data) {
            if (data !== false) {
                var abuseComment = jQuery('#abusecontent').val();
                var abuseEmail = jQuery('#abuseemail').val();
                var param = {
                    userId: userId,
                    customerHostName: customerHostName,
                    customerIp: customerIp,
                    recom_id: recomId,
                    comment: abuseComment,
                    email: abuseEmail
                };
                jQuery.ajax({
                    url: siteUrl,
                    data: param,
                    dataType: 'json',
                    type: 'post',
                    success: function (response) {
                        vex.defaultOptions.className = 'vex-theme-default';
                        if (response.status == 'true') {
                            vex.dialog.alert(Translator.translate('Dziękujemy za informację o nadużyciu. Twoje zgłoszenie zostało przesłane do weryfikacji przez administratora.'));
                        } else {
                            vex.dialog.alert(Translator.translate('Abuse wasn\'t added.'));
                        }
                    }
                });
            }
        }
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
 * Shows opinion form after click button
 */
function showAddOpinionForm() {
    jQuery('#tim-add-opinion-layout').show(500);
    jQuery('#tim-general-add-opinion-button').hide();
    jQuery('.tim-add-opinion-button').hide();
    jQuery('html, body').animate({
        scrollTop: jQuery('#tim-add-opinion-layout').offset().top
    }, 500);
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
    jQuery('.tim-add-opinion-button').show();
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
/**
 * Validate comment form before add comment by ajax
 * @param el
 */
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
 *Display result in popup after adding opinion by ajax
 * @param response
 */
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

/**
 * Display result in popup after adding comment by ajax
 * @param response
 */
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
 * Add files to opinion
 * @returns {boolean}
 */
function addFilesToOpinion() {
    var fileArray = [];
    var recommendationImg = jQuery('#recommendation-img');
    var fileInput = recommendationImg[0];
    var inputFilesQty = fileInput.files.length;
    var maxFilesQty = 5;
    var downloadedImgsSize = jQuery("#downloaded-imgs div").length;
    var maxSize = parseInt(recommendationImg.attr('max-size'), 10);
    vex.defaultOptions.className = 'vex-theme-default';

    //check on max allowed qty of images
    if (inputFilesQty <= maxFilesQty) {
        //check on max allowed qty of images together with already added images
        if ((downloadedImgsSize + inputFilesQty) <= maxFilesQty) {
            var data = new FormData();
            var url = jQuery('input[name=saveTmpImgUrl]').val();
            var userId = jQuery('input[name=customer_id]').val();
            data.append('userId', userId);
            for (var i = 0; i < inputFilesQty; ++i) {
                //check allowed image size
                if (checkImgSize(fileInput.files[i]['size'], maxSize)) {
                    //check allowed image type
                    if (checkImgType(fileInput.files[i])) {
                        data.append('tim-recommendation-img[]', fileInput.files[i]);
                        fileArray.push(fileInput.files[i]);
                    } else {
                        vex.dialog.alert(Translator.translate('You can not upload a file: ') + fileInput.files[i]['name'] + Translator.translate('. They are acceptable image files in jpg or png.'));
                    }
                } else {
                    vex.dialog.alert(Translator.translate('You can not upload a file: ') + fileInput.files[i]['name'] + Translator.translate('. The maximum size is ') + maxFilesQty + " MB.");
                }
            }
            data.append('userId', userId);
            //upload images to tmp directory for next saving to opinion
            uploadImgToTmpDirAjax(url, data, fileArray);
        } else {
            vex.dialog.alert(Translator.translate('You can not transfer files. Maximum number of files to be transferred to ') + maxFilesQty + '.');
            return false;
        }
    } else {
        vex.dialog.alert(Translator.translate('You can not transfer files. Maximum number of files to be transferred to ') + maxFilesQty + '.');
        return false;
    }
    //clear input with files
    jQuery('#recommendation-img').val('');
}

/**
 * Upload images that customer add to opinion to tmp directory
 * @param url
 * @param data
 * @param fileArray
 */
function uploadImgToTmpDirAjax(url, data, fileArray) {
    jQuery.ajax({
        type: 'POST',
        url: url,
        headers: {'Cache-Control': 'no-cache'},
        data: data,
        contentType: false,
        processData: false,
        success: function () {
            displayFilesName(fileArray);
        },
        error: function () {
            vex.dialog.alert(Translator.translate('Something went wrong. Please add again last images.'));
        }
    });
}

/**
 * Display file names in add opinion form
 * @param fileArray
 */
function displayFilesName(fileArray) {
    var images = '';
    jQuery.each(fileArray, function (idx, file) {
        images += '<div id="div-img-del-' + idx + '"><span class="delete-image" id="' + idx + '" onclick="deleteFile(this)"">X</span>' + file['name'] + '</div>';
    });
    jQuery('#downloaded-imgs').append(images);
    var downloadedImgsDivs = jQuery("#downloaded-imgs div");
    if (downloadedImgsDivs.length) {
        prepareImagesForSave(downloadedImgsDivs);
    }
}

function prepareImagesForSave(downloadedImgsDivs) {
    var imgs = [];
    jQuery.each(downloadedImgsDivs, function (id, element) {
        //remove first char(X) from beginning of file name and replace all spaces on underscore
        var fileName = jQuery(element).text().substring(1, jQuery(element).text().length).replace(/ /g, '_');
        var imgsForSave = jQuery('#imagesForSave');
        //create array with images for a save
        imgs.push(fileName);
        imgsForSave.val(JSON.stringify(imgs));
    });
}

/**
 * Delete file from div and prepare new div with deleted files
 */
function deleteFile(element) {
    var elementParent = jQuery(element).parent();
    //remove first char(X) from beginning of file name and replace all spaces on underscore
    var fileName = elementParent.text().substring(1, elementParent.text().length).replace(/ /g, '_');
    var deletedImgs = jQuery('#deleted-imgs');
    if (deletedImgs.val() == '') {
        var deleteFile = [];
        deleteFile.push(fileName);
        deletedImgs.val(JSON.stringify(deleteFile))
    } else {
        var deletedFiles = JSON.parse(deletedImgs.val());
        deletedFiles.push(fileName);
        deletedImgs.val(JSON.stringify(deletedFiles));
    }

    /** Remove element from list of files for saving*/
    var imgsForSave = jQuery('#imagesForSave');
    var savedImgs = JSON.parse(imgsForSave.val());
    savedImgs = jQuery.grep(savedImgs, function (value) {
        return value != fileName;
    });
    imgsForSave.val(JSON.stringify(savedImgs));

    //remove element from div
    elementParent.remove();
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

/**
 * Close popup action
 */
function closePopup() {
    jQuery(document).on('click', '.tim-popup-close', function () {
        var popupClass = '.' + jQuery(this).parents().get(1).className;
        jQuery(popupClass).hide();
    });
}

/**
 * Display selected qty of stars on add opinion view
 */
function displayRatingStars() {
    if (!jQuery('#tim-profile-status').val() || jQuery('#isLoggedIn').val()) {
        jQuery('.tim-rating-input-span').on('change', function () {
            var inputChangeName = jQuery(this).attr('name');
            var inputChangeValue = jQuery(this).val();
            var inputChangeNameSpan = 'span.' + inputChangeName;
            jQuery(inputChangeNameSpan).html(inputChangeValue);
        });
    }
}

/**
 * Scrolling from product info to add opinion form
 */
function scrollToAddOpinion() {
    // Scroll down for adding an opinion for app/design/frontend/default/default/template/tim/recommendation/rating/product_view.phtml
    jQuery('#tim-scroll-add-opinion').on('click', function () {
        if (jQuery('#tim-add-opinion-layout').is(":visible")) {
            jQuery('html, body').animate({
                scrollTop: jQuery('#tim-recom-add-header').offset().top
            }, 500);
            return false;
        } else {
            showAddOpinionForm();
            jQuery('html, body').animate({
                scrollTop: jQuery(jQuery(this).attr('href')).offset().top
            }, 500);
            return false;
        }
    });
    jQuery('#tim-see-more-button').on('click', function () {
        if (jQuery('#tim-add-opinion-layout').is(":visible")) {
            jQuery('html, body').animate({
                scrollTop: jQuery('#tim-recom-add-header').offset().top
            }, 500);
            return false;
        } else {
            jQuery('html, body').animate({
                scrollTop: jQuery(jQuery(this).attr('href')).offset().top
            }, 500);
            return false;
        }
    });
}

/**
 * Get last element from url
 * @return {string}
 */
function getLastElementFromUrl() {
    var url = window.location.href;
    var urlsplit = url.split("/");
    return urlsplit.last();
}

/**
 * Open add opinion form by expected hash in url
 * @returns {boolean}
 */
function openAddOpinionByHash() {
    if (getLastElementFromUrl() == '#add-opinion') {
        showAddOpinionForm();
        jQuery('html, body').animate({
            scrollTop: jQuery('#tim-add-opinion-layout').offset().top
        }, 500);
        return false;
    }
}

/**
 * Count chars in comment textarea
 * @param commentId
 */
function countCommentChar(commentId) {
    var text = jQuery('#tim-opinion-comment-' + commentId).val();
    var charCount = text.length;
    jQuery('#char-count-comment-' + commentId).children('span').text(charCount);
}

/**
 * Count chars in opinion's textarea
 */
function countOpinionChars() {
    jQuery('.tim-opinion-characters-count').on('keyup', function () {
        var $el = jQuery(this);
        var id = $el.attr('id');
        var span = jQuery('#char-count-' + id).children("span");
        span.html($el.val().length);
    });
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

/**
 * Add extra validation to forms
 */
function addExtraValidation() {
    Validation.add('validate-url-link', Translator.translate('Given the link does not lead to the film from YouTube'), function (v) {
        var url = v;
        if (/([^\s])/.test(url)) {
            var regExp = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/;
            var match = url.match(regExp);
            if (!match) {
                return false;
            }
        }
        return true;
    });

    if (jQuery('#opinionLimitChars').length) {
        var limitCharsData = jQuery('#opinionLimitChars').data();
        var opinionLimitCharsMin = limitCharsData.min;
        var opinionLimitCharsMax = limitCharsData.max;

        Validation.add('min-length-opinion', Translator.translate('The minimum number of characters is ') + opinionLimitCharsMin, function (v) {
            var min = (opinionLimitCharsMin ? opinionLimitCharsMin : 0);
            if (min) {
                if (v.length < min) {
                    return false;
                }
            }
            return true;
        });
        Validation.add('max-length-opinion', Translator.translate('The maximum number of characters is ') + opinionLimitCharsMax, function (v) {
            var max = (opinionLimitCharsMax ? opinionLimitCharsMax : 0);
            if (max) {
                if (v.length > max) {
                    return false;
                }
            }
            return true;
        });
    }
}
//------------------------------------------- product view end -------------------------------------------//
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
    jQuery('.tim-sort-action-class').on('click', function () {
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
//----------------------------------------- VEX based popups for recommendation start -------------------------------//
/**
 * Check if user is logged in
 */
function checkIfUserIsLoggedIn() {
    /* function open modal when submit button is pressed without validation yet */
    jQuery("#form-validate-opinion")[0].reset();
    vex.open({
        content: '<strong>Uwaga!</strong> Nie jesteś zalogowanym użytkownikiem, aby dodać opinię lub komentarz musisz zalogować się lub założyć konto na naszym serwisie.',
        className: 'vex-theme-default'
    });
    return false;
}

/**
 * Shows popup if user's profile not fill
 */
function checkProfile() {
    jQuery('.check-tim-profile-status').on('click', function () {
        var status = jQuery('#tim-profile-status').val();
        var fillProfile = 0;

        if (status == fillProfile) {
            vex.open({
                content: jQuery('.check-tim-profile-status-popup').html(),
                className: 'vex-theme-default'
            });
            return false;
        }
        return true;
    });
}

/**
 * Check click action and show popup with opinion photo
 */
function showPhotos() {
    jQuery(document).on('click', '.tim-opinion-photo-link', function (e) {
        vex.open({
            content: jQuery('#tim-all-photo-popup-' + e.target.id).html(),
            className: 'vex-theme-default'
        });
    });
}

/**
 * Check click action and show popup with opinion video
 */
function showVideo() {
    jQuery(document).on('click', '.tim-opinion-movie', function (e) {
        var recomId = e.target.id;
        var youtubeVideoId = jQuery('#tim-youtube-data-' + recomId).val();
        var $popup = jQuery('#tim-video-popup-' + recomId);
        var isset = $popup.find('.iframe-video-popup');
        if (typeof youtubeVideoId !== 'undefined') {
            if (isset.length == 0) {
                $popup.find('.tim-video-popup-container').append('<iframe class="iframe-video-popup" src="https://www.youtube.com/embed/' + youtubeVideoId + '"></iframe>');
            } else {
                $popup.find('.iframe-video-popup').attr("src", "https://www.youtube.com/embed/" + youtubeVideoId);
            }
        }
        vex.open({
            content: $popup.html(),
            className: 'vex-theme-default'
        });
        return false;
    });
}
//--------------------------- VEX based popups for recommendation end -------------------------------//