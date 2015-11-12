jQuery(document).ready(function() {
/* function to put value into html content right to rating stars and switch userlogin details*/

    jQuery('input').on('change', function () {
        var inputChangeName = jQuery(this).attr('name');
        var inputChangeValue = jQuery(this).val();
        var inputChangeNameSpan = 'span.' + inputChangeName
        /* alert(inputChangeName+inputChangeValue)         */
        jQuery(inputChangeNameSpan).html(inputChangeValue);
        console.log(inputChangeName);
        
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

    function markUserAbuse() {
        jQuery('.tim-markabuse-popup').show(300);

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
		
        jQuery(document).keydown(function (e) {
            if (e.keyCode == 27) {
                jQuery('.tim-markabuse-popup').hide();
            }
        });        
    }    