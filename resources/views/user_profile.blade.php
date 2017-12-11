@extends('layouts.base')

@section('content')
<div class="loading">Loading&#8230;</div>
<div class="container">
<div id="mainprofile">

	@if ($isOwner)
    <button class="btn newlink" type="button" data-toggle="modal" data-target="#newlinkmodal"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></button>
	@endif
<!-- New Post Modal -->
        <div class="modal fade" id="newlinkmodal" tabindex="-1" role="dialog" aria-labelledby="newlinkmodal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Post link</h4>
                    </div>
                    <form method='POST' action='/shorten' role='form' id='form-shorten'>
                        <div class="modal-body">
                            <div class="input-group">
                                <input id="link-url-input" name="link-url" onchange="refreshLinkInfo(this.value);" type='url' autocomplete='off' class="form-control" placeholder='http://example.com'>
                               <span class="input-group-btn">
                                    <button class="btn btn-analyze" onclick="clickAnalyze();" type="button">Analyze</button>
                               </span>
                            </div>
                            <div class="well">
                                <div class="mediadiv">
                                    <div class="media">
                                        <div class="media-left text-center">
                                            <a href="#">
                                                <img alt="postimage" class="media-object" id="link_image_img" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif">
                                                <p id="no-preview" style="display: none; margin-top: 30px; height: 150px; padding-top: 60px;">No image preview available please upload</p>
                                            </a>
                                            <input type="hidden" id="link_image" name="image">
                                            <br>
                                            <button class="btn btn-upload upload-thumb" type="button"><i class="fa fa-upload" aria-hidden="true"></i>Upload Image</button>
                                        </div>
                                        <div class="media-body">
                                            <div class="form-group">
                                                <label for="link_title">Title</label>
                                                <input name="title" type="text" class="form-control" id="link_title" placeholder="Name your link...">
                                            </div>
                                            <p>Customize link</p>
                                            <div>
                                                <div class='custom-link-text'>
                                                    <h4 class='site-url-field'>ink.vu/{{session('username')}}/</h4>
                                                    <input name="custom-ending" type='text' autocomplete="off" class='form-control custom-url-field' name='custom-ending' />
                                                </div>
                                                <a href='#' data-popup="0" class='btn btn-success btn-xs check-btn check-link-availability'>Check Availability</a>
                                                <div class="link-availability-status" id='link-availability-status'></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="offer_code">Offer Code (Optional)</label>
                                                <input id="offer_code" name="offer_code" type="text" class="form-control" placeholder="Create a unique code">
                                            </div>
                                            <div class="form-group">
                                                <label for="link_description">Description:</label>
                                                <textarea name="description" class="form-control" rows="2" id="link_description"></textarea>
                                            </div>
                                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Publish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <!-- End New Post Modal -->

        <!-- Start Profile Details -->
	<div role="tabpanel" class="tab-pane" id="settings">
                <div class="profile">
                <h3 style="display:block;padding-right:10px;">{{$user->username}}</h3>
                    <img class="profilepic" src="{{$user->profile_picture_url}}" alt="" />
                    <div class="profileinfo">
                    <p>Website: <a href="{{$user->website}}" target="_blank">{{$user->website}}</a><br>{{$user->bio}}
                    </p>
                    </div>
                <div class="socialicons">
			<a class="btn btn-social-icon btn-twitter" target="_BLANK" href="{{$user->twitter_url}}">
			<span class="fa fa-twitter"></span>
			</a>
			<a class="btn btn-social-icon btn-facebook" target="_BLANK" href="{{$user->facebook_url}}">
			<span class="fa fa-facebook"></span>
			</a>
			<a class="btn btn-social-icon btn-instagram" target="_BLANK" href="{{$user->instagram_url}}">
			<span class="fa fa-instagram"></span>
			</a>
		</div>
		<br>
                    <button type="button" class="btn btn-primary subscribebutton" data-toggle="modal" data-target="#pushModal">Subscribe</button>
                    <br clear="both" />
            </div>
            </div>
        @if (count($links) == 0)
        <div style="font-size: 20px; color: gray;">Your profile doesn’t have any links yet. Post something!</div>
        @endif
            </div>

    @if (!$isOwner && session('username'))
    <!-- Start push Modal -->
    <div class="modal fade" id="pushModal" tabindex="-1" role="dialog" aria-labelledby="pushModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Get notifications from this user?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="subscribeoptions">
                            <p id="errMsg" style="color: red; display: none;"></p>
                            <label class="switch">
                                <input type="checkbox" id="push_web_check" @if($notifySetting->web_notify) checked @endif>
                                <div class="slider round push_web"></div>
                            </label>
                            <div class="optionslabel">Web Push Notifications&nbsp;<a data-toggle="tooltip" class="tooltipLink" data-original-title="Receive real-time notifications in your browser" data-placement="right"><span class="fa fa-question-circle" title="info"></span></a>
                            </div>

                            <br>
                            <label class="switch">
                                <input type="checkbox" id="push_email_check" @if($notifySetting->email_notify) checked @endif>
                                <div class="slider round"></div>
                            </label>
                            <div class="optionslabel">Email Notifications</div>
                            @if ($notifySetting->email)
                                <div>Your email: <span style="color: #e95950;">{{ $notifySetting->email }}</span>
                                    <a style="color: #222" href="/admin#settings">#settings</a>
                                </div>
                            @else
                                <div class="input-group email-group">
                                    <input type="text" id="push_email" class="form-control" placeholder="Your Email" value="{{ $notifySetting->email }}">
                                <span class="input-group-btn">
                                  <button class="btn btn-default pushVerifyEmail" type="button">Verify</button>
                                </span>
                                </div>
                            @endif
                            <div class="verifylabelemail" style="display: none; margin: 10px 0px;">Your Email: <strong style="color: #e95950"></strong></div>
                            <div class="input-group verify-group-email" style="display: none">
                                <input value="" type="text" id="push_email_verify" class="form-control" placeholder="Please enter verify number #">
                                <span class="input-group-btn">
                                    <button class="btn btn-default pushVerifyNumberEmail" type="button">Verify</button>
                                </span>
                            </div>
                            <div class="email_verified" style="color: #e95950; display: none"></div>
                            <div class="verify_error_label_email" style="color: #e95950; display: none">Verify number incorrect</div>


                            <label class="switch">
                                <input type="checkbox" id="push_mobile_check" @if($notifySetting->mobile_notify) checked @endif>
                                <div class="slider round"></div>
                            </label>
                            <div class="optionslabel">Mobile Notifications</div>
                            @if ($notifySetting->mobile)
                                <div>Your mobile: <span style="color: #e95950;">{{ $notifySetting->mobile }}</span>
                                    <a style="color: #222" href="/admin#settings">#settings</a>
                                </div>
                            @else
                            <div class="input-group mobile-group">
                                <input value="{{ $notifySetting->mobile }}" type="text" id="push_mobile" class="form-control" placeholder="Your Mobile #">
                                <span class="input-group-btn">
                                    <button class="btn btn-default pushVerify" type="button">Verify</button>
                                </span>
                            </div>
                            @endif

                            <div class="verifylabel" style="display: none; margin: 10px 0px;">Your Mobile: <strong style="color: #e95950"></strong></div>
                            <div class="input-group verify-group" style="display: none">
                                <input value="" type="text" id="push_mobile_verify" class="form-control" placeholder="Please enter verify number #">
                                <span class="input-group-btn">
                                    <button class="btn btn-default pushVerifyNumber" type="button">Verify</button>
                                </span>
                            </div>
                            <div class="mobile_verified" style="color: #e95950; display: none"></div>
                            <div class="verify_error_label" style="color: #e95950; display: none">Verify number incorrect</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="push_user_id" value="{{$user->id}}">
                    <button type="button" class="btn btn-primary saveNotify">Save Options</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End push Modal -->
    @endif
        
        
        <!-- Email Inner Modal -->
                    
<div id="myInnerModal1" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Would you like to receive email updates instead?</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
      <br>
      <button type="submit" class="btn btn-primary" style="width:100%;">Submit</button>
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
                    <!-- Email Inner Modal End -->
    
    
	<div class="endbuttons">
		<p class="labelmenu">Recent</p>

		@if ($isOwner)
		<div class="editprofile">

			<!-- Button trigger modal -->
			<button type="button" class="btn btn-default editprofilebutton" data-toggle="modal" data-target="#myModal">
  Edit Profile
</button>

			<!-- Start Edit Profile Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Profile</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="/{{$user->username}}/update">
				<div class="modal-body">
        					<input type="hidden" name='_token' value='{{csrf_token()}}' />
						<div class="form-group">
							<input name="facebook_url" type="url" value="{{$user->facebook_url}}" class="form-control" placeholder="Facebook URL">
						</div>
						<div class="form-group">
							<input name="twitter_url" type="url" value="{{$user->twitter_url}}" class="form-control" placeholder="Twitter URL">
						</div>
						<div class="form-group">
							<input name="instagram_url" type="url" value="{{$user->instagram_url}}" class="form-control" placeholder="Instagram URL">
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
				</form>
      </div>
    </div>
  </div>
</div>
<!-- End Edit Profile Modal --> 


			</div>
		</div>
		@endif
	</div>

	<div id="linkcards">
        @foreach($links as $link)

                <div class="wrapper" id="link-{{$link->short_url}}">

                    <div style="display: none" id="linkmodal-{{$link->short_url}}">
                        <div class="modalicon">
                            @if ($link->image)
                            <img class="pic error_image" src="{{$link->image}}" />
                            @else
                            <img class="pic error_image" src="{{$error_image}}" />
                            @endif
                        </div>
                        <div class="content">
                            <h6>{{$link->created_at}}</h6>
                            <h4 class="linktitle"><a target="_blank" href="{{$link->long_url}}">{{$link->title}}</a></h4>
                            <p class="short-desc">{{$link->description}}</p>
                            @if ($link->offer_code)
                                <p class="offercode"><strong>Offer Code:</strong> <input readonly type="text" class="offer-code-holder" value="{{$link->offer_code}}" /> <button class="btn copybutton btn-xs" type="button" onclick="
                                try {
                                    this.previousElementSibling.select();
                                    if(!document.execCommand('copy'))
                                        console.log('copy failed');
                                } catch(e) {
                                    console.log(e);
                                }
                                return false;

                            ">COPY</button></p>
                            @endif
                            <div class="card-footer">
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-fb" class="fa fa-facebook-square fa-2x social"></i></a>
                                    <a href="https://twitter.com/share?url={{urlencode($link->fullUrl())}}"><i id="social-tw" class="fa fa-twitter-square fa-2x social"></i></a>
                                    <a href="https://plus.google.com/share?url={{urlencode($link->fullUrl())}}"><i id="social-gp" class="fa fa-google-plus-square fa-2x social"></i></a>
                                </div>
                            </div>
                            <a data-link-id="{{$link->id}}" data-clipboard-target="#target-url-{{$link->id}}" id="target-url-{{$link->id}}" data-full-url="{{$link->fullUrl()}}" class="btn-copy" href="javascript:void(0);"><p class="@if ($isOwner)clipboard_owner @endif clipboard">Copy Link</p></a>
                            @if ($isOwner)<p class="clicks"><i class="fa fa-bar-chart" aria-hidden="true"></i>{{$link->clicks}} Clicks</p>@endif
                            <p class="copied copytext-{{$link->id}}" style="display: none">copied</p>
                            @if ($isOwner)
                                <button data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline edit-inline"><span class="glyphicon glyphicon-edit"></span> Edit</button>
                                <button style="display: none" data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline save-inline"><span class="glyphicon glyphicon-save"></span> Save</button>
                            @endif
                        </div>
                    </div>

                    <div class="icon" onclick="return showModalPostViaLink({{json_encode($link->short_url)}});">
                        @if ($link->image)
                            <img class="pic error_image" src="{{$link->image}}" />
                        @else
                            <img class="pic error_image" src="{{$error_image}}" />
                        @endif
                    </div>
                    <div class="content" id="linkcontent-{{$link->short_url}}">
                        @if ($isOwner)
                            <div class="dropdown" style="float:right;">
                                <button class="btn-delete dropdown-toggle" style="background:none;" type="button" data-toggle="dropdown">
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="#" data-href="#" data-toggle="modal" data-target="#confirm-delete" onclick="
                                                document.getElementById('delete-id-element').value = {{json_encode($link->short_url)}};
                                                ">Delete</a></li>
                                </ul>
                            </div>
                        @endif

                        <h6>{{$link->created_at}}</h6>
                        <h4 class="linktitle" onclick="return showModalPostViaLink({{json_encode($link->short_url)}});">{{$link->title}}</h4>
                        <h4 style="display: none" class="linktitle_text">{{$link->title}}</h4>
                        <p class="short-desc">{{$link->description}}</p>
                        @if ($link->offer_code)
                            <p class="offercode"><strong>Offer Code:</strong> <input readonly type="text" class="offer-code-holder" value="{{$link->offer_code}}" /> <button class="btn copybutton btn-xs" type="button" onclick="
                                try {
                                    this.previousElementSibling.select();
                                    if(!document.execCommand('copy'))
                                        console.log('copy failed');
                                } catch(e) {
                                    console.log(e);
                                }
                                return false;

                            ">COPY</button></p>
                        @endif
                        <div class="card-footer">
                            <div class="text-center center-block">
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-fb" class="fa fa-facebook-square fa-2x social"></i></a>
                                    <a href="https://twitter.com/share?url={{urlencode($link->fullUrl())}}"><i id="social-tw" class="fa fa-twitter-square fa-2x social"></i></a>
                                    <a href="https://plus.google.com/share?url={{urlencode($link->fullUrl())}}"><i id="social-gp" class="fa fa-google-plus-square fa-2x social"></i></a>
                                </div>
                            </div>
                        </div>
                        <a data-link-id="{{$link->id}}" data-clipboard-target="#target-url-{{$link->id}}" id="target-url-{{$link->id}}" data-full-url="{{$link->fullUrl()}}" class="btn-copy" href="javascript:void(0);"><p class="@if ($isOwner)clipboard_owner @endif clipboard">Copy Link</p></a>
                        @if ($isOwner)<p class="clicks"><i class="fa fa-bar-chart" aria-hidden="true"></i>{{$link->clicks}} Clicks</p>@endif
                        <p class="copied copytext-{{$link->id}}" style="display: none">copied</p>
                        @if ($isOwner)
                            <button data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline edit-inline"><span class="glyphicon glyphicon-edit"></span> Edit</button>
                            <button style="display: none" data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline save-inline"><span class="glyphicon glyphicon-save"></span> Save</button>
                        @endif
                    </div>
                </div>

        @endforeach
            </div>

<!-- Start Card 1 Modal -->
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="modalRegister">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="singlemodal" style="display:none;">Post</h4>
            </div>
            <div class="modal-body">
                <div class="wrapper">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Card 1 Modal -->

<!-- Delete Modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Delete Post?
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<form method="POST" action="{{route('pdelete')}}">
					<input type="hidden" name='_token' value='{{csrf_token()}}' />
					<input type="hidden" name="short_url" id="delete-id-element" value="" />
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger btn-ok">Delete</button>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Delete Modal -->

</div>
</div>

<!-- @ include('snippets/shorten_form', []) -->

@endsection

@section('js')
	<script src='/js/index.js'></script>
	<script>
        $(document).ready(function () {
            $(".error_image").on("error", function() {
                $(this).attr('src', '{{$error_image}}');
            });
        });

		function showModalPostViaLink(shortlink) {
			if($("#modalRegister").hasClass('in')) {
				// from clicks on the page just keep going
				return true;
			} else {
				return showModalPost(shortlink);
			}
		}
		function showModalPost(shortlink) {
			var e = document.getElementById("linkmodal-" + shortlink);
			var w = document.querySelector("#modalRegister .wrapper");
			w.innerHTML = e.innerHTML;
            $("#modalRegister").modal('show');

            pop_clipboard = new Clipboard('.btn-copy', {
                container: document.getElementById('modalRegister'),
                text: function(trigger) {
                    return trigger.getAttribute('data-full-url');
                }
            });

            pop_clipboard.on('success', function(e) {
                var linkId = e.trigger.getAttribute('data-link-id');
                $(".copied").hide();
                $(".copytext-"+linkId).show();
            });

			return false;
		}

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

            if ($("#linkcontent-"+linkId+" .linktitle_text input").length) {
                var title = $("#linkcontent-"+linkId+" .linktitle_text input").val();
            } else {
                var title = $("#linkcontent-"+linkId+" .linktitle_text").html();
            }

            if ($("#linkcontent-"+linkId+" .short-desc textarea").length) {
                var desc = $("#linkcontent-"+linkId+" .short-desc textarea").val();
            } else {
                var desc = $("#linkcontent-"+linkId+" .short-desc").html();
            }

            $("#linkcontent-"+linkId+" .linktitle_text").html('<input class="form-control" style="width: 400px" onclick="return false;" id="inp-linktitle-'+linkId+'" value="'+title+'" />');
            $("#linkcontent-"+linkId+" .short-desc").html('<textarea class="form-control" style="width: 400px; height: 75px">' + desc + '</textarea>');

            $("#linkcontent-"+linkId+" .linktitle").hide();
            $("#linkcontent-"+linkId+" .linktitle_text").show();
            $("#linkcontent-"+linkId+" .save-inline").show();
        });

        $('.content-div').on('click', '.save-inline', function () {
            var linkId = $(this).data('link-id');
            var title = $("#linkcontent-"+linkId+" .linktitle_text input").val();
            var desc = $("#linkcontent-"+linkId+" .short-desc textarea").val();
            var data = {
                link_ending: linkId,
                title: title,
                description: desc
            };

            $("#linkcontent-"+linkId+" .save-inline").hide();
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
                        $("#linkcontent-"+linkId+" .edit-inline").show();
                    } else {
                        var errors = jsonData.errors;
                    }
                    $(".loading").hide();
                }
            });
        });

        $('.content-div').on('click', '.upload-thumb, #link_image_img', function () {
            clientFileStack.pick(pickerOptions).then(function(result) {
                var jsonData = result.filesUploaded[0];
                $("#link_image").val(jsonData.url);
                document.getElementById("link_image_img").src = jsonData.url;
            })
        });

        $('#pushModal').on('click', '.saveNotify, .pushSave', function () {

            var data = {
                push_web_check: $('#push_web_check').is(":checked"),
                push_email_check: $('#push_email_check').is(":checked"),
                push_email: $("#push_email").val(),
                push_mobile_check: $('#push_mobile_check').is(":checked"),
                push_mobile: $("#push_mobile").val(),
                push_notify_user: $("#push_user_id").val(),
                push_web_userid: OneSignalUserID
            };

            var dataUpdatePlayerID = '';
            $.ajax({
                url: '/save_notification',
                data: data,
                dataType: 'json',
                type: 'POST',
                success: function(jsonData) {
                    if (parseInt(jsonData.code) != 1) {
                        $("#errMsg").html(jsonData.message);
                        $("#errMsg").show();
                    } else {
                        $("#pushModal").modal('hide');
                        $("#errMsg").hide();

                        if ($('#push_web_check').is(":checked")) {
                            OneSignal.push(function() {
                                // If we're on an unsupported browser, do nothing
                                if (!OneSignal.isPushNotificationsSupported()) {
                                    return;
                                }
                                OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                                    OneSignal.registerForPushNotifications({
                                        modalPrompt: true
                                    });

                                    OneSignal.sendTags({
                                        email: $("#push_email").val()
                                    }).then(function(tagsSent) {
                                        // Callback called when tags have finished sending

                                    });

                                    OneSignal.getUserId(function(userId) {
                                        dataUpdatePlayerID = {
                                            id: jsonData.data,
                                            push_web_userid: userId
                                        };
                                        $.ajax({
                                            url: '/update_notification',
                                            data: dataUpdatePlayerID,
                                            dataType: 'json',
                                            type: 'POST',
                                            success: function(jsonData) {

                                            }
                                        });
                                    });
                                });
                            });
                        } else {
                            OneSignal.push(function() {
                                // If we're on an unsupported browser, do nothing
                                if (!OneSignal.isPushNotificationsSupported()) {
                                    return;
                                }
                                OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                                    if (isEnabled) {
                                        OneSignal.push(["setSubscription", false]);
                                    }
                                });

                                dataUpdatePlayerID = {
                                    id: jsonData.data,
                                    push_web_userid: ''
                                };
                                $.ajax({
                                    url: '/update_notification',
                                    data: dataUpdatePlayerID,
                                    dataType: 'json',
                                    type: 'POST',
                                    success: function(jsonData) {

                                    }
                                });
                            });
                        }
                    }
                }
            });
        });


        $('#pushModal').on('click', '.pushVerifyEmail', function () {
            var email = $("#push_email").val();
            if (!email) {
                return false;
            }

            var data = {
                email: $("#push_email").val()
            };
            $.ajax({
                url: '/verifyemail',
                data: data,
                dataType: 'json',
                type: 'POST',
                success: function(jsonData) {

                }
            });

            $(".email-group").hide();
            $(".verify-group-email").show();
            $(".verifylabelemail strong").text(email);
            $(".verifylabelemail").show();
        });

        $('#pushModal').on('click', '.pushVerifyNumberEmail', function () {
            var verifyNumber = $("#push_email_verify").val();
            if (!verifyNumber) {
                $(".push_email_verify").addClass("input-error");
                $(".verify_error_label_email").show();
                return false;
            }

            var data = {
                verifyNumber: verifyNumber
            };
            $.ajax({
                url: '/verifynumberemail',
                data: data,
                dataType: 'json',
                type: 'POST',
                success: function(jsonData) {
                    if (jsonData.code == 1) {
                        $(".push_email_verify").removeClass("input-error");
                        $(".verify_error_label_email").hide();

                        $(".verifylabelemail").hide();
                        $(".verify-group-email").hide();
                        $(".email_verified").html($("#push_email").val() + ' <span style="color: #cccccc;">Verified</span>');
                        $(".email_verified").show();
                    } else {
                        $(".push_email_verify").addClass("input-error");
                        $(".verify_error_label_email").show();
                    }
                }
            });
        });





        $('#pushModal').on('click', '.pushVerify', function () {
            var mobile = $("#push_mobile").val();
            if (!mobile) {
                return false;
            }

            var data = {
                mobile: $("#push_mobile").val()
            };
            $.ajax({
                url: '/verifysns',
                data: data,
                dataType: 'json',
                type: 'POST',
                success: function(jsonData) {

                }
            });

            $(".mobile-group").hide();
            $(".verify-group").show();
            $(".verifylabel strong").text(mobile);
            $(".verifylabel").show();
        });

        $('#pushModal').on('click', '.pushVerifyNumber', function () {
            var verifyNumber = $("#push_mobile_verify").val();
            if (!verifyNumber) {
                $(".push_mobile_verify").addClass("input-error");
                $(".verify_error_label").show();
                return false;
            }

            var data = {
                verifyNumber: verifyNumber
            };
            $.ajax({
                url: '/verifynumbersns',
                data: data,
                dataType: 'json',
                type: 'POST',
                success: function(jsonData) {
                    if (jsonData.code == 1) {
                        $(".push_mobile_verify").removeClass("input-error");
                        $(".verify_error_label").hide();

                        $(".verifylabel").hide();
                        $(".verify-group").hide();
                        $(".mobile_verified").html($("#push_mobile").val() + ' <span style="color: #cccccc;">Verified</span>');
                        $(".mobile_verified").show();
                    } else {
                        $(".push_mobile_verify").addClass("input-error");
                        $(".verify_error_label").show();
                    }
                }
            });
        });
	</script>

	@if ($showlink && $isNewPost == 0)
	<script>
		showModalPost({!! json_encode($showlink) !!}, true);
	</script>
    @endif

    @if (session('isNewUser'))
    {{session()->put('isNewUser', 0)}}
    <script>
        $('#onboardModal').modal('show');
    </script>
    @endif

@endsection

@section('css')
	<link rel='stylesheet' href='css/index.css' />
@endsection


