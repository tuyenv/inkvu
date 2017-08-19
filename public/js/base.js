// AJAX settings
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Escape jQuery selectors
function esc_selector(selector) {
    return selector.replace( /(:|\.|\[|\]|,)/g, "\\$1" );
}

jQuery.fn.clearForm = function() {
    // http://stackoverflow.com/questions/6364289/clear-form-fields-with-jquery
    $(this).find('input').not(':button, :submit, :reset, :hidden')
        .val('')
        .removeAttr('checked')
        .removeAttr('selected');

    return this;
};

// Output helpful console message
console.log('%cPolr', 'font-size:5em;color:green');
console.log('%cNeed help? Open a ticket: https://github.com/cydrobolt/polr', 'color:blue');
console.log('%cDocs: https://docs.polr.me', 'color:blue');

// Set up Angular module
var polr = angular.module('polr',[]);

// Setup FileStack
var fileStackKey = 'APsEBWJ5KQtyzuUbunQDNz';

// onboardModal
$('#onboardModal').modalSteps();
$( ".step4-6" ).click(function() {
    $(".js-btn-step-next").trigger("click");
    $(".js-btn-step-next").trigger("click");
});
$( ".step4-5" ).click(function() {
    $(".js-btn-step-next").trigger("click");
});
$('#form-shorten-popup').on('click', '.upload-thumb-popup, #link_image_img_popup', function () {
    client.pick(pickerOptions).then(function(result) {
        var jsonData = result.filesUploaded[0];
        $("#link_image_popup").val(jsonData.url);
        document.getElementById("link_image_img_popup").src = jsonData.url;
    })
});