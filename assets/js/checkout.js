jQuery(document).ready(function($){

    if(typeof wc_maybank2u_params !== 'undefined') {
        if( typeof wc_maybank2u_params.encrypt_json !== 'undefined') {

            $('body').append('<div id="m2upay" ></div>');
            var encrypt_json =  JSON.parse(wc_maybank2u_params.encrypt_json);
            m2upay.initPayment(encrypt_json.encryptedString,encrypt_json.actionUrl, 'OT');

        }
    }
});