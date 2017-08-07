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

// Set up clipboard
var clipboard = new Clipboard('.btn-copy', {
    text: function(trigger) {
        return trigger.getAttribute('data-full-url');
    }
});

clipboard.on('success', function(e) {
    var linkId = e.trigger.getAttribute('data-link-id');
    $(".copied").hide();
    $(".copytext-"+linkId).show();
});

// Prevent shorten submit
$('#form-shorten').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

setTimeout(function(){ $(".alert").alert('close'); }, 5000);

$('.content-div').on('click', '.edit-inline', function () {
    $("#modalRegister .close").click();
    $(this).hide();
    var linkId = $(this).data('link-id');
    var title = $("#linkcontent-"+linkId+" .linktitle_text").html();
    var desc = $("#linkcontent-"+linkId+" .short-desc").html();
    $("#linkcontent-"+linkId+" .linktitle_text").html('<input style="width: 400px" onclick="return false;" id="inp-linktitle-'+linkId+'" value="'+title+'" />');
    $("#linkcontent-"+linkId+" .short-desc").html('<textarea style="width: 400px; height: 75px">' + desc + '</textarea>');

    $("#linkcontent-"+linkId+" .linktitle").hide();
    $("#linkcontent-"+linkId+" .linktitle_text").show();
    $('.save-inline').show();
});

$('.content-div').on('click', '.save-inline', function () {
    $('.save-inline').hide();
    var linkId = $(this).data('link-id');
    var title = $("#linkcontent-"+linkId+" .linktitle_text input").val();
    var desc = $("#linkcontent-"+linkId+" .short-desc textarea").val();
    var data = {
        link_ending: linkId,
        title: title,
        description: desc
    };

    $(".loading").show();
    $.ajax({
        url: '/edit_link',
        data: data,
        dataType: 'json',
        type: 'POST',
        success: function(jsonData) {
            if (parseInt(jsonData.code) == 1) {
                $("#link-"+linkId+" .linktitle_text").html(title);
                $("#link-"+linkId+" .linktitle").html(title);
                $("#link-"+linkId+" .short-desc").html(desc);

                $("#linkcontent-"+linkId+" .linktitle").show();
                $("#linkcontent-"+linkId+" .linktitle_text").hide();
                $('.edit-inline').show();
            } else {
                var errors = jsonData.errors;

            }
            $(".loading").hide();
        }
    });
});