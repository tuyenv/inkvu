var isRefresh = false;
var isPostedImg = false;

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
/*
var fileStackKey = 'APsEBWJ5KQtyzuUbunQDNz';
var clientFileStack = filestack.init(fileStackKey);
var pickerOptions = {
    fromSources: ['local_file_system', 'instagram', 'facebook', 'googledrive', 'dropbox'],
    accept: ['image/*'],
    maxFiles: 1,
    storeTo: { path: '/custom_thumb/', location: 's3' }
};
*/

// onboardModal
var callback = function() {
    if ($("#isDoubleStep").val() == 1) {
        $(".step4").hide();
    } else {
        $(".step4").show();
    }

    if ($("#actual-step").val() == 4) {
        $(".step4").show();
    } else {
        $(".panel-default").removeClass('panel-picked');
    }
    $("#isPopupComplete").val(0);
};

$('#onboardModal').modalSteps({
    btnLastStepHtml: 'Publish',
    callbacks: {
        '*': callback
    }
});

$( ".js-btn-step-next" ).click(function() {
    var step = $(this).attr("data-step");
    var actualStep = $("#actual-step").val();
    var isComplete = $("#isPopupComplete").val();

    if (step == 'complete' && actualStep == 6 && isComplete == 1) {
        $( "#form-shorten-popup" ).submit();
    }

    if (step == 'complete' && actualStep == 6) {
        $("#isPopupComplete").val(1);
    } else {
        $("#isPopupComplete").val(0);
    }
});

$( ".step4-6" ).click(function() {
    $(".pickone .panel-default").removeClass('panel-picked');
    $(this).parent(".panel-default").addClass("panel-picked");
    $("#is_replace_image").val(1);
    $("#isDoubleStep").val(1);
    $("#actual-step").val(5);
});

$( ".step4-5" ).click(function() {
    $(".pickone .panel-default").removeClass('panel-picked');
    $(this).parent(".panel-default").addClass("panel-picked");
    $("#is_replace_image").val(1);
    $("#isDoubleStep").val(0);
    $("#actual-step").val(4);
});

$('#form-shorten-popup').on('click', '.upload-thumb-popup, #link_image_img_popup', function () {
    var dialog = uploadcare.openDialog(null, {
        crop: "disabled",
        imagesOnly: true
    });

    dialog.done(function(file) {
        file.promise().done(function(fileInfo){
            console.log(fileInfo.cdnUrl);
            $("#link_image_popup").val(fileInfo.cdnUrl);
            document.getElementById("link_image_img_popup").src = fileInfo.cdnUrl;
        });
    });
});

$(".insta-li").click(function() {
    $(".insta-li img").removeClass('insta-img');
    $(this).find('img').addClass('insta-img');
    var imgLink = $(this).find('img').attr('src');
    $("#link_image_popup").val(imgLink);
    document.getElementById("link_image_img_popup").src = imgLink;
    $("#is_replace_image").val(0);
    $("#link_image_popup").val($(this).attr("data-img"));

    var url = $(this).data('link');
    var caption = $(this).data('caption');
    $("#link-url-input-popup").val(url);
    $("#link_description_popup").val(caption);
    $("#form-shorten-popup .l-likes").val($(this).data('likes'));
    $("#form-shorten-popup .l-comments").val($(this).data('comments'));
    $("#form-shorten-popup .l-tags").val($(this).data('tags'));


    if ($( ".step4-5").parent(".panel-default").hasClass("panel-picked")) {
        $("#link-url-input-popup").val($(this).attr("data-link"));
        clickAnalyze(1);
    }
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

function clickRefresh() {
    isRefresh = true;
    $("#link-url-input").val('');
    $("#link_title").val('');
    $(".custom-url-field").val('');
    $("#offer_code").val('');
    $("#link_description").val('');
    $(".btn-refresh").text('Clear');
    $("#link_image_img").attr("src", "http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif");
    $(".uploadcare--widget__button").text('Upload an image');
}

function refreshLinkInfo(url, popup) {
    if (isRefresh) {
        return false;
    }

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

            $("#form-shorten-popup #stats_like").val(data.likes);
            $("#form-shorten-popup #stats_comment").val(data.comments);
            $("#form-shorten-popup .l-likes").val(data.likes);
            $("#form-shorten-popup .l-comments").val(data.comments);
            $("#form-shorten-popup .l-tags").val(data.tags);
            $("#form-shorten-popup .l-original-date").val(data.original_date);
            if (data.image != '') {
                isPostedImg = true;
                $("#form-shorten-popup .uploadcare--widget__button").text('Change Image');
            }
            if (data.is_stats == 1) {
                $("#form-shorten-popup .div-stats").show();
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

            $("#form-shorten #stats_like").val(data.likes);
            $("#form-shorten #stats_comment").val(data.comments);
            $("#form-shorten .l-likes").val(data.likes);
            $("#form-shorten .l-comments").val(data.comments);
            $("#form-shorten .l-tags").val(data.tags);
            $("#form-shorten .l-original-date").val(data.original_date);
            if (data.image != '') {
                isPostedImg = true;
                $("#form-shorten .uploadcare--widget__button").text('Change Image');
            }

            if (data.is_stats == 1) {
                $("#form-shorten .div-stats").show();
            }
        }
    });
}

