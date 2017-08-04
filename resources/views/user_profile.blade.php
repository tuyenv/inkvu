@extends('layouts.base')

@section('content')
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
                                <input name="link-url" onchange="refreshLinkInfo(this.value);" type='url' autocomplete='off' class="form-control" placeholder='http://example.com' name='link-url'>
                               <span class="input-group-btn">
                                    <button class="btn btn-analyze" type="button">Analyze</button>
                               </span>
                            </div>
                            <div class="well">
                                <div class="mediadiv">
                                    <div class="media">
                                        <div class="media-left text-center">
                                            <a href="#">
                                                <img alt="postimage" class="media-object" id="link_image_img" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif">
                                            </a>
                                            <input type="hidden" id="link_image" name="image">
                                            <br>
                                            <button class="btn btn-upload" type="button"><i class="fa fa-upload" aria-hidden="true"></i>Upload Image</button>
                                        </div>
                                        <div class="media-body">
                                            <div class="form-group">
                                                <label for="link_title">Title</label>
                                                <input name="title" type="text" class="form-control" id="link_title" placeholder="Name your link...">
                                            </div>
                                            <div class="permalink"><p>http://ink.vu/username/customURL</p></div>
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
            </div>
    
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

    <div class="col-xs-6">
        <button class="btn btn-warning btn-block" data-toggle="modal" data-target="#myInnerModal1">No Thanks</button>
    </div>
    <div class="col-xs-6">
        <button class="btn btn-success btn-block" onclick="myFunction()" data-dismiss="modal">Allow</button>
        
    </div>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End push Modal -->
        
        
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
        @if ($isNewPost !== 0)
        <div style="margin-bottom: 0px" class="alert alert-success alert-dismissible fade show in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Post published.</strong>
        </div>
        @endif
        @foreach($links as $link)

                <div class="wrapper" id="link-{{$link->short_url}}">

                    <div style="display: none" id="linkmodal-{{$link->short_url}}">
                        <div class="modalicon">
                            <img class="pic" src="{{$link->image}}" />
                        </div>
                        <div class="content">
                            <h6>{{$link->created_at}}</h6>
                            <h4 class="linktitle">{{$link->title}}</h4>
                            <p>{{$link->description}}</p>
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
                                    <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-fb" class="fa fa-facebook-square fa-2x social"></i></a>
                                    <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-tw" class="fa fa-twitter-square fa-2x social"></i></a>
                                    <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-gp" class="fa fa-google-plus-square fa-2x social"></i></a>
                                    <a href="mailto:bootsnipp@gmail.com"><i id="social-em" class="fa fa-envelope-square fa-2x social"></i></a>
                                </div>
                            </div>
                            <a data-link-id="{{$link->id}}" data-clipboard-target="#target-url-{{$link->id}}" id="target-url-{{$link->id}}" data-full-url="{{$link->fullUrl()}}" class="btn-copy" href="javascript:void(0);"><p class="clipboard">Copy Link</p></a>
                            @if ($isOwner)<p class="clicks"><i class="fa fa-bar-chart" aria-hidden="true"></i>5 Clicks</p>@endif
                            <p class="copied copytext-{{$link->id}}" style="display: none">copied</p>
                        </div>
                    </div>

                    <div class="icon" onclick="return showModalPostViaLink({{json_encode($link->short_url)}});">
                        <img class="pic" src="{{$link->image}}" />
                    </div>
                    <div class="content">
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
                        <p>{{$link->description}}</p>
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
                                <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-fb" class="fa fa-facebook-square fa-2x social"></i></a>
                                <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-tw" class="fa fa-twitter-square fa-2x social"></i></a>
                                <a href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}"><i id="social-gp" class="fa fa-google-plus-square fa-2x social"></i></a>
                                <a href="mailto:bootsnipp@gmail.com"><i id="social-em" class="fa fa-envelope-square fa-2x social"></i></a>
                            </div>
                        </div>
                        <a data-link-id="{{$link->id}}" data-clipboard-target="#target-url-{{$link->id}}" id="target-url-{{$link->id}}" data-full-url="{{$link->fullUrl()}}" class="btn-copy" href="javascript:void(0);"><p class="clipboard">Copy Link</p></a>
                        @if ($isOwner)<p class="clicks"><i class="fa fa-bar-chart" aria-hidden="true"></i>5 Clicks</p>@endif
                        <p class="copied copytext-{{$link->id}}" style="display: none">copied</p>
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
					<button type="submit" class="btn btn-danger btn-ok">Delete</a>
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
	<script src='js/index.js'></script>
	<script>
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

		function refreshLinkInfo(url) {
			$.post("/describe", {url : url}, function(data) {
				document.getElementById("link_title").value = data.title;
				document.getElementById("link_description").value = data.description;
				document.getElementById("link_image").value = data.image;
				document.getElementById("link_image_img").src = data.image;
			});

		}

		function checkAvailability(ext) {

		}
	</script>

	@if ($showlink)
	<script>
		showModalPost({!! json_encode($showlink) !!}, true);
	</script>
@endif

@endsection

@section('css')
	<link rel='stylesheet' href='css/index.css' />
@endsection
