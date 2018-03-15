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
                        <h4 style="float:left; padding-top: 5px;" class="modal-title" id="myModalLabel">Post link</h4>
                        @if (isset($instaMedia) && !empty($instaMedia))
                            <button id="btnInstagram" class="btn btn-analyze" style="margin-left: 20px; background-color: #e95950" type="button">Post Instagram</button>
                        @endif
                    </div>
                    <form method='POST' action='/shorten' role='form' id='form-shorten'>
                        <div class="modal-body">
                            <div class="input-group">
                                <input id="link-url-input" name="link-url" onchange="refreshLinkInfo(this.value);" type='url' autocomplete='off' class="form-control" placeholder='http://example.com'>
                                <span class="input-group-btn">
                                    <button class="btn btn-analyze btn-refresh" style="background-color: #e95950" onclick="clickRefresh();" type="button">Refresh</button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-analyze" onclick="clickAnalyze();" type="button">Analyze</button>
                                </span>
                            </div>
                            <div class="well">
                                <div class="mediadiv">
                                    <div class="media">
                                        <p class="steemit-check" style="display: none; color: #e95950;">The link doesn't exist on Steemdata</p>
                                        <div class="media-left text-center">
                                            <a href="#">
                                                <img style="padding-top: 10px;" alt="postimage" class="media-object" id="link_image_img" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif">
                                                <p id="no-preview" style="display: none; margin-top: 30px; height: 150px; padding-top: 60px;">No image preview available please upload</p>
                                            </a>
                                            <!--<input type="hidden" id="link_image" name="image">-->
                                            <input type="hidden" role="uploadcare-uploader" name="image"
                                                   data-crop="300x300 upscale"
                                                   id="link_image"
                                                   data-images-only="true" />
                                            <br>
                                            <!--<button class="btn btn-upload upload-thumb" type="button"><i class="fa fa-upload" aria-hidden="true"></i>Upload Image</button>-->
                                            <div class="form-group paste_link">
                                                <label for="paste_link">Paste Link:</label>
                                                <span class="sp_paste_link" style="color:#e95950; padding-left: 5px;"></span>
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <div class="form-group">
                                                <label for="link_title">Title</label>
                                                <textarea rows="3" name="title" class="form-control" id="link_title" placeholder="Name your link..."></textarea>
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
                                                <textarea name="description" class="form-control" rows="4" id="link_description"></textarea>
                                            </div>

                                            <div class="div-stats" style="display: none">
                                                <div class="form-group">
                                                    <label for="stats_like">Like:</label>
                                                    <input value="0" disabled id="stats_like" name="stats_like" type="text" class="custom-url-field form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="stats_comment">Comment:</label>
                                                    <input value="0" disabled id="stats_comment" name="stats_comment" type="text" class="custom-url-field form-control">
                                                </div>
                                            </div>

                                            <input type="hidden" name='_token' value='{{csrf_token()}}' />

                                            <input type="hidden" name="l-likes" class="l-likes" value="0">
                                            <input type="hidden" name="l-comments" class="l-comments" value="0">
                                            <input type="hidden" name="l-tags" class="l-tags" value="0">
                                            <input type="hidden" name="l-original-date" class="l-original-date" value="">
                                            <input type="hidden" name="l-author" class="l-author" value="">
                                            <input type="hidden" name="l-ucstatus" class="l-ucstatus" value="0">
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
                    <img style="height: 150px; width: 150px;" class="profilepic" src="{{$user->profile_picture_url}}" alt="" />
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
            <!--
			<button type="button" class="btn btn-default editprofilebutton" data-toggle="modal" data-target="#myModal">
  Edit Profile
</button>-->

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
                                <a target="_blank" href="{{$link->long_url}}"><img onerror="onErrorTeam(this);" class="pic error_image" src="{{$link->image}}" /></a>
                            @else
                                <a target="_blank" href="{{$link->long_url}}"><img onerror="onErrorTeam(this);" class="pic error_image" src="{{$error_image}}" /></a>
                            @endif
                        </div>
                        <div class="content content-popup">
                            <a style="position: absolute;right: 10px;" target="_blank" href="{{$link->fullUrl()}}"><img width="60" src="http://beta.ink.vu/wp-content/uploads/2017/04/inkvu-03.png" alt="Ink.vu"></a>
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
                            <img onerror="onErrorTeam(this);" class="pic error_image" src="{{$link->image}}" />
                        @else
                            <img onerror="onErrorTeam(this);" class="pic error_image" src="{{$error_image}}" />
                        @endif
                    </div>
                    @if ($isOwner)
                        <button data-id="{{$link->id}}" data-image="{{$link->image}}" data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary edit-picture">
                            <span class="glyphicon glyphicon-edit"></span> Edit
                        </button>
                    @endif
                    <div class="content content-list" id="linkcontent-{{$link->short_url}}">
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

                        @if ($link->source == 'steemit.com')
                        <div class="cl-steemit">
                            <span class="st-original-date"><i class="fa fa-clock-o fa-st" aria-hidden="true"></i> {{ time_elapsed_string($link->original_date) }} by <a class="ptc">{{$link->created_by}}</a></span>
                            <span class="st-comments fl-right"><i class="fa fa-comments fa-st" aria-hidden="true"></i> {{$link->comments}}</span>
                            <span class="st-votes fl-right">{{$link->likes}} votes</span>
                        </div>
                        @endif

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

                        @if ($link->tags)
                        <?php
                        $arrTag = explode(',', $link->tags);
                        ?>
                        <div class="st-tag">
                            <?php
                            if (!empty($arrTag)) {
                                foreach ($arrTag as $tag) {
                                    echo '<a href="#">'.$tag.'</a>';
                                }
                            }
                            ?>
                        </div>
                        @endif

                        <div class="card-top-right">
                            <a data-link-id="{{$link->id}}" data-clipboard-target="#target-url-{{$link->id}}" id="target-url-{{$link->id}}" data-full-url="{{$link->fullUrl()}}" class="btn-copy" href="javascript:void(0);"><p class="@if ($isOwner)clipboard_owner @endif clipboard">Copy Link</p></a>
                            @if ($isOwner)<p class="clicks"><i class="fa fa-bar-chart" aria-hidden="true"></i>{{$link->clicks}} Clicks</p>@endif
                            <p class="copied copytext-{{$link->id}}" style="display: none">copied</p>
                            @if ($isOwner)
                                <button data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline edit-inline"><span class="glyphicon glyphicon-edit"></span> Edit</button>
                                <button style="display: none" data-link-id="{{$link->short_url}}" type="button" class="btn btn-primary btn-inline save-inline"><span class="glyphicon glyphicon-save"></span> Save</button>
                            @endif
                        </div>

                    </div>
                </div>

        @endforeach
            </div>

<!-- Start Card 1 Modal -->
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="modalRegister">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="padding-top: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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

<!-- Edit picture modal -->
<div class="modal fade" id="editPictureModal" tabindex="-1" role="dialog" aria-labelledby="editPictureModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit picture</h4>
            </div>
            <form method='POST' action='/editpicture' role='form' id='editpicture'>
                <div class="modal-body">

                    <div class="well">
                        <div class="mediadiv">
                            <div class="media">
                                <div class="media-body text-center">
                                    <a href="#">
                                        <img id="img-edit" onerror="onErrorTeam(this);" style="margin-left: 135px;" alt="postimage" class="media-object error_image media-src" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif">
                                        <p id="no-preview" style="display: none; margin-top: 30px; height: 150px; padding-top: 60px;">No image preview available please upload</p>
                                    </a>

                                    <input class="input-img-edit" type="hidden" role="uploadcare-uploader" name="image"
                                           data-crop="300x300 upscale"
                                           data-images-only="true" />

                                    <input type="hidden" class="edit_image_name" name="edit_image_name" value="">
                                    <input type="hidden" class="post_id" name="post_id" value="0">
                                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Done</button>
                </div>
            </form>
        </div>
    </div>
</div>





@if (isset($instaMedia) && !empty($instaMedia))
    <div class="modal fade" id="instagramModal" tabindex="-1" role="dialog" aria-labelledby="instagramModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>Post instagram link</h4>
                </div>

                <div>
                    <center><p style="font-size:18px;">Select a piece of content from your profile.</p></center>
                    <div style="margin-bottom: 30px; overflow: scroll; height: 275px;" data-title="Select a piece of content from your profile">
                        <div class="well">

                                <ul class="hide-bullets">
                                    @foreach ($instaMedia as $media)
                                        <li class="col-sm-3 insta-post" data-tags="{{implode(',', $media['tags'])}}"
                                            data-comments="{{$media['comments']['count']}}"
                                            data-likes="{{$media['likes']['count']}}"
                                            data-caption="{{$media['caption']['text']}}"
                                            data-link="{{$media['link']}}"
                                            data-img="{{$media['images']['standard_resolution']['url']}}">

                                            <a class="thumbnail">
                                                <img src="{{$media['images']['thumbnail']['url']}}">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="choose-instagram">Choose</button>
                </div>
            </div>
        </div>
    </div>
@endif



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
            $("#modalRegister").modal('hide');
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

            $("#linkcontent-"+linkId+" .linktitle_text").html('<input class="form-control" style="width: 100%" onclick="return false;" id="inp-linktitle-'+linkId+'" value="'+title+'" />');
            $("#linkcontent-"+linkId+" .short-desc").html('<textarea class="form-control" style="width: 100%; height: 75px">' + desc + '</textarea>');

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

            var dialog = uploadcare.openDialog(null, {
                crop: "disabled",
                imagesOnly: true
            });

            dialog.done(function(file) {
                file.promise().done(function(fileInfo){
                    console.log(fileInfo.cdnUrl);
                    $("#link_image").val(fileInfo.cdnUrl);
                    $("#form-shorten .l-ucstatus").val(1);
                    document.getElementById("link_image_img").src = fileInfo.cdnUrl;
                });
            });

            dialog.fail(function(result) {
                // Dialog closed and no file or file group was selected.
                // The result argument is either null or the last selected file.
                console.log(result);
            });

            dialog.always(function() {
                // Handles a closing dialog regardless of whether or not files were selected.
                console.log('always');
                $("#form-shorten .l-ucstatus").val(1);
            });

            dialog.progress(function(tabName) {
                // tabName is selected.
                console.log('process');
                $("#form-shorten .l-ucstatus").val(1);
            });

        });

        $('.content-div').on('click', '.input-img-edit, #img-edit', function () {

            var dialog = uploadcare.openDialog(null, {
                crop: "disabled",
                imagesOnly: true
            });

            dialog.done(function(file) {
                file.promise().done(function(fileInfo){
                    console.log(fileInfo.cdnUrl);
                    $(".edit_image_name").val(fileInfo.cdnUrl);
                    document.getElementById("img-edit").src = fileInfo.cdnUrl;
                });
            });

            dialog.fail(function(result) {
                // Dialog closed and no file or file group was selected.
                // The result argument is either null or the last selected file.
                console.log(result);
            });

            dialog.always(function() {
                // Handles a closing dialog regardless of whether or not files were selected.
                console.log('always');
            });

            dialog.progress(function(tabName) {
                // tabName is selected.
                console.log('process');
            });

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

        $('.edit-picture').on('click', function (e) {
            var postId = $(this).data("id");
            var postImg = $(this).data("image");

            $("#editPictureModal .uploadcare--widget__button").text('Change Image');
            $("#editPictureModal .edit_image_name").val(postImg);
            $("#editPictureModal .media-src").attr('src', postImg);
            $("#editPictureModal .post_id").val(postId);

            $('#editPictureModal').modal('show');
        });

        $(".insta-post").click(function() {
            $(".insta-post img").removeClass('insta-img');
            $(this).find('img').addClass('insta-img');

            var url = $(this).data('link');
            $("#link-url-input").val(url);
        });

        $('#btnInstagram').on('click', function (e) {
            $('#instagramModal').modal('show');
            $('#newlinkmodal').modal('hide');
        });

        $('#choose-instagram').on('click', function (e) {
            $('#instagramModal').modal('hide');
            $('#newlinkmodal').modal('show');
            clickAnalyze();
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


