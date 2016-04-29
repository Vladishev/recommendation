jQuery(document).ready(function () {
    addExtraValidation();
    cropperFunctionality();
});
/**
 * Cropper functionality
 */
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
            if (checkFile.size > 409600) {
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
                        //uncomment for debug
                        //console.log(e.message);
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

/**
 * Add extra validation to forms
 */
function addExtraValidation() {
    Validation.add('custom-url-validate', 'Prosimy o wprowadzenie poprawnego URL. Dla przykładu: http://www.strona.pl lub www.strona.pl', function () {
        var url = document.getElementById("tim-form-url").value;
        var pattern = /([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
        if (pattern.test(url)) {
            return true;
        }
        return false;
    });

    Validation.add('tim-avatar-validate', 'Prosimy o wybór jednej z powyższych opcji.', function () {
        var defaultAvatars = jQuery('input[name=selected_avatar]:checked').val();
        var avatarContainer = jQuery('#tim-avatar-container');
        var customAvatar = avatarContainer.has('img').length;
        if ((typeof defaultAvatars === 'undefined') && (customAvatar == 0)) {
            jQuery('html, body').animate({
                scrollTop: avatarContainer.offset().top - 400
            }, 500);
            return false;
        }
        return true;
    });
}