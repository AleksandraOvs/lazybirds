jQuery(document).ready(function($){

let referral_system = $('#referal-system');

    referral_system.click(function(){
        if ($(this).is(':checked')){
            $('#referal-options').show(100);
        } else {
            $('#referal-options').hide(100);
        }
    });

    if (referral_system.is(':checked')){
        $('#referal-options').show(100);
    } else {
        $('#referal-options').hide(100);
    }

    if ($('#exclude-role').length > 0) {
        new SlimSelect({
            select: '#exclude-role',
        });
    }

    if ($('#exclude-category').length > 0) {
        new SlimSelect({
            select: '#exclude-category',
        });
    }

    if ($('#exclude-payment-method').length > 0) {
        new SlimSelect({
            select: '#exclude-payment-method',
        });
    }
});