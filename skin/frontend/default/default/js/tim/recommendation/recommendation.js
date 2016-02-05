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