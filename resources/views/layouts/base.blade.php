<!--
Polr, a minimalist URL shortening platform.
Copyright (C) 2013-2017 Chaoyi Zha

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
-->

<!DOCTYPE html>
<html ng-app="polr">
<head>
    <title>@section('title'){{env('APP_NAME')}}@show</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Leave this for stats --}}
    <meta name="generator" content="Polr {{env('POLR_VERSION')}}" />
    @yield('meta')

    {{-- Load Stylesheets --}}
    @if (env('APP_STYLESHEET'))
    <link rel="stylesheet" href="{{env('APP_STYLESHEET')}}">
    @else
    <link rel="stylesheet" href="/css/default-bootstrap.min.css">
    @endif

    <link href="/css/base.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    <link href="/css/toastr.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">

    @yield('css')
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"> 
</head>
<body>
    @include('snippets.navbar')
    <div class="container">
    <div style="min-height: calc(100vh - 328px)">
        <div class="content-div @if (!isset($no_div_padding)) content-div-padding @endif @if (isset($large)) jumbotron large-content-div @endif">
            @yield('content')

            <!-- Onboarding Modal -->
            @if (isset($user))
            <div class="modal fade" id="onboardModal" tabindex="-1" role="dialog" aria-labelledby="onboardModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="js-title-step"></h4>
                        </div>
                        <div class="modal-hide">
                            <div class="row hide" data-step="1" data-title="Welcome to Ink.vu, Joe!">
                                <div class="well">
                                    <center><img src="{{$user->profile_picture_url}}" alt="yourprofile" style="width:30%;"></center>
                                    <div class="tutorial-text">
                                        <p style="font-size:18px;text-align:center;">Ink is a useful tool for promoting your links on social media!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row hide" data-step="2" data-title="No more 'Link in Bio' posts, period.">
                                <div class="well">
                                    <center><img src="{{$user->profile_picture_url}}" alt="yourprofile"></center>
                                    <div class="tutorial-text">
                                        <center><p style="font-size:18px;">Instagram makes link sharing a pain. Stop sharing content that people can't find on Instagram and start tracking clicks with Ink.vu!</p></center>
                                    </div>
                                </div>
                            </div>
                            <div class="row hide" data-step="3" data-title="How it Works">
                                <div class="well">
                                    <div class="videowrapper">
                                        <iframe width="560" height="315" src="https://www.youtube.com/embed/pwSpMdvImNw?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                            <div class="row hide" data-step="4" data-title="Now, let's Ink your first link">
                                <p style="font-size:18px;text-align:center;">What would you like to do?</p>
                                <div class="well">
                                    <div class="pickone">
                                        <div class="panel panel-default col-sm-6">
                                            <div class="step4-5 panel-body"><i class="fa fa-instagram" aria-hidden="true"></i><br>
                                                I want to Ink a link for an Instagram post
                                            </div>
                                        </div>
                                        <div class="panel panel-default col-sm-6 col-sm-6-offset-2">
                                            <div class="step4-6 panel-body"><i class="fa fa-link" aria-hidden="true"></i><br>
                                                I want to Ink a link from any website</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-bottom: 30px;" class="row hide" data-step="5" data-title="Select a piece of content from your profile">
                                <center><p style="font-size:18px;">You'll add details on the next page.</p></center>
                                <div class="well">
                                    @if (isset($instaMedia) && !empty($instaMedia))
                                        <ul class="hide-bullets">
                                        @foreach ($instaMedia as $media)
                                            <li class="col-sm-3 insta-li">
                                                <a class="thumbnail">
                                                    <img src="{{$media['images']['thumbnail']['url']}}">
                                                </a>
                                            </li>
                                        @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="row hide" data-step="6" data-title="Say a little bit about your link:">
                                <form method='POST' action='/shorten' role='form' id='form-shorten-popup'>
                                    <div class="well">
                                        <div class="input-group">
                                            <input id="link-url-input-popup" name="link-url" onchange="refreshLinkInfo(this.value, 1);" type='url' autocomplete='off' class="form-control" placeholder='http://example.com'>
                           <span class="input-group-btn">
                                <button class="btn btn-analyze" onclick="clickAnalyze(1);" type="button">Analyze</button>
                           </span>
                                        </div>
                                        <div class="mediadiv">
                                            <div class="media">
                                                <div class="media-left text-center">
                                                    <a href="#">
                                                        <img alt="postimage" class="media-object" id="link_image_img_popup" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif">
                                                        <p id="no-preview-popup" style="display: none; margin-top: 30px; height: 150px; padding-top: 60px;">No image preview available please upload</p>
                                                    </a>
                                                    <input type="hidden" id="link_image_popup" name="image">
                                                    <br>
                                                    <button class="btn btn-upload upload-thumb-popup" type="button"><i class="fa fa-upload" aria-hidden="true"></i>Upload Image</button>
                                                </div>
                                                <div class="media-body">
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <input name="title" id="link_title_popup" type="text" class="form-control" placeholder="Name your link...">
                                                    </div>
                                                    <p>Customize link</p>
                                                    <div>
                                                        <div class='custom-link-text'>
                                                            <h4 class='site-url-field'>ink.vu/{{session('username')}}/</h4>
                                                            <input name="custom-ending" type='text' autocomplete="off" class='form-control custom-url-field-popup' name='custom-ending' />
                                                        </div>
                                                        <a href='#' data-popup="1" class='btn btn-success btn-xs check-btn check-link-availability'>Check Availability</a>
                                                        <div class="link-availability-status" id='link-availability-status-popup'></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Offer Code (Optional)</label>
                                                        <input name="offer_code" type="text" class="form-control" placeholder="Create a unique code">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="comment">Description:</label>
                                                        <textarea name="description" class="form-control" rows="2" id="link_description_popup"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top:none;">
                                        <button style="margin-right: 15px;" type="submit" class="btn btn-primary">Publish</button>
                                    </div>
                                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                                    <input type="hidden" id="is_replace_image" value="1" />
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default js-btn-step pull-left" data-orientation="skip" data-dismiss="modal">Skip</button>
                            <button type="button" class="btn btn-secondary js-btn-step" data-orientation="previous">Back</button>
                            <button type="button" class="btn btn-primary js-btn-step js-btn-step-next" data-orientation="next">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Onboarding Modal -->
            @endif

        </div>
    </div>
    </div>

    <footer id="myFooter">
        <div class="container">
            <ul>
                <li><a href="#">Company Information</a></li>
                <li><a href="#">Contact us</a></li>
                <li><a href="#">Reviews</a></li>
                <li><a href="#">Terms of service</a></li>
            </ul>
            <p class="footer-copyright">© 2017 Ink.vu</p>
        </div>
        <div class="footer-social">
            <a href="#" class="social-icons"><i class="fa fa-facebook"></i></a>
            <a href="#" class="social-icons"><i class="fa fa-google-plus"></i></a>
            <a href="#" class="social-icons"><i class="fa fa-twitter"></i></a>
        </div>
    </footer>

    {{-- Load header JavaScript --}}
    <script src='/js/constants.js'></script>
    <script src="/js/jquery-1.11.3.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src='/js/angular.min.js'></script>
    <script src='/js/toastr.min.js'></script>
    <script src='/js/clipboard.min.js'></script>
    <script src='/js/jquery-bootstrap-modal-steps.js'></script>
    <script src="https://static.filestackapi.com/v3/filestack.js"></script>
    <script src='/js/base.js'></script>

    <script>
    @if (Session::has('info'))
        toastr["info"](`{{ str_replace('`', '\`', session('info')) }}`, "Info")
    @endif
    @if (Session::has('error'))
        toastr["error"](`{{str_replace('`', '\`', session('error')) }}`, "Error")
    @endif
    @if (Session::has('warning'))
        toastr["warning"](`{{ str_replace('`', '\`', session('warning')) }}`, "Warning")
    @endif
    @if (Session::has('success'))
        toastr["success"](`{{ str_replace('`', '\`', session('success')) }}`, "Success")
    @endif

    @if (count($errors) > 0)
        // Handle Lumen validation errors
        @foreach ($errors->all() as $error)
            toastr["error"](`{{ str_replace('`', '\`', $error) }}`, "Error")
        @endforeach
    @endif
    </script>

    @yield('js')


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=100126226747838";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };

  return t;
}(document, "script", "twitter-wjs"));</script>

<script src="https://apis.google.com/js/platform.js" async defer></script>


</body>
</html>
