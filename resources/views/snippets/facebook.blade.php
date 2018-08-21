<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId            : '{{env('FACEBOOK_CLIENT_ID')}}',
            autoLogAppEvents : true,
            xfbml            : true,
            version          : 'v3.1'
        });

        $('.btn-share-fb').on('click', function (e) {
            var url = $(this).attr('data-url');
            var id = $(this).attr('data-id');

            FB.ui({
                method: 'share_open_graph',
                action_type: 'og.likes',
                action_properties: JSON.stringify({
                    object:url
                })
            }, function(response){
                ajaxShareButton(id, 'facebook');
            });
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


</script>