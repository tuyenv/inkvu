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
var clientFileStack = filestack.init(fileStackKey, { policy: 'policy', signature: 'signature' });
var pickerOptions = {
    accept: ['image/*'],
    maxFiles: 1,
    storeTo: { path: '/custom_thumb/' }
};

// onboardModal
$('#onboardModal').modalSteps();
$( ".step4-6" ).click(function() {
    $(".js-btn-step-next").trigger("click");
    $(".js-btn-step-next").trigger("click");
    $("#is_replace_image").val(1);
});
$( ".step4-5" ).click(function() {
    $(".js-btn-step-next").trigger("click");
    $("#is_replace_image").val(1);
});
$('#form-shorten-popup').on('click', '.upload-thumb-popup, #link_image_img_popup', function () {
    clientFileStack.pick(pickerOptions).then(function(result) {
        var jsonData = result.filesUploaded[0];
        $("#link_image_popup").val(jsonData.url);
        $("#link_image").val(jsonData.url);
        document.getElementById("link_image_img_popup").src = jsonData.url;
    })
});

$(".insta-li").click(function() {
    $(".insta-li img").removeClass('insta-img');
    $(this).find('img').addClass('insta-img');
    var imgLink = $(this).find('img').attr('src');
    $("#link_image_popup").val(imgLink);
    $("#link_image").val(imgLink);
    document.getElementById("link_image_img_popup").src = imgLink;
    $("#is_replace_image").val(0);
});

// scrape link
function clickAnalyze(popup) {
    if (popup) {
        var url = $("#link-url-input-popup").val();
    } else {
        var url = $("#link-url-input").val();
    }
    refreshLinkInfo(url, popup);
}

function refreshLinkInfo(url, popup) {
    $.post("/describe", {url : url}, function(data) {
        if (popup) {
            document.getElementById("link_title_popup").value = data.title;
            document.getElementById("link_description_popup").value = data.description;

            if (data.image) {
                $("#no-preview-popup").hide();
                $("#link_image_img_popup").show();
                if ($("#is_replace_image").val() == 1) {
                    document.getElementById("link_image_popup").value = data.image;
                    document.getElementById("link_image_img_popup").src = data.image;
                }
            } else {
                $("#link_image_img_popup").hide();
                $("#no-preview-popup").show();
            }
        } else {
            document.getElementById("link_title").value = data.title;
            document.getElementById("link_description").value = data.description;
            document.getElementById("link_image").value = data.image;
            if (data.image) {
                $("#no-preview").hide();
                $("#link_image_img").show();
                document.getElementById("link_image_img").src = data.image;
            } else {
                $("#link_image_img").hide();
                $("#no-preview").show();
            }
        }
    });
}