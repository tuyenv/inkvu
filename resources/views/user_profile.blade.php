@extends('layouts.base')

@section('content')
<div class="container">
<div id="mainprofile">

	@if ($isOwner)
	<button class="btn btn-primary newlink" type="button" data-toggle="modal" data-target="#newlinkmodal">+ New Post
  </button>
	@endif
<!-- New Post Modal -->
<div class="modal fade" id="newlinkmodal" tabindex="-1" role="dialog" aria-labelledby="newlinkmodal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">What would you like to share?</h4>
      </div>
        <form method='POST' action='/shorten' role='form'>
      <div class="modal-body">
          <input name="link-url" onchange="refreshLinkInfo(this.value);" type='url' autocomplete='off' class='form-control long-link-input' placeholder='http://example.com' name='link-url' />
          <div ng-cloak>
            <p>Customize link</p>
            <div>
              <div class='custom-link-text'>
                <h4 class='site-url-field'>ink.vu/{{session('username')}}/</h4>
                <input name="custom-ending" type='text' autocomplete="off" class='form-control custom-url-field' name='custom-ending' />
              </div>
              <a onclick="checkAvailability(this.value); return false;" href='#' class='btn btn-success btn-xs check-btn' id='check-link-availability'>Check Availability</a>
            </div>
            <p>Image Preview</p>
            <div class="button-box col-lg-12">
              <img id="link_image_img" style="width: 150px; height: 150px;" src="http://ericatoelle.com/wp-content/uploads/2012/02/150x150.gif" alt="..." class="img-thumbnail">
              <!--
              <span class="or">Or</span>
              <a href="#" class="btn btn-default">
              <span class="" style="font-size:13px;">Quick Grab</span><br>
              <i class="fa fa-facebook" aria-hidden="true"></i>
              <i class="fa fa-instagram" aria-hidden="true"></i>
              <i class="fa fa-google" aria-hidden="true"></i>
              <i class="fa fa-upload" aria-hidden="true"></i>
              </a>       
              -->
            </div>
            <input type="hidden" id="link_image" name="image">
            <br>
            <div class="form-group">
              <label for="exampleInputEmail1">Link Title</label>
              <input name="title" type="text" class="form-control" id="link_title" placeholder="Name your link...">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Offer Code (Optional)</label>
              <input name="offer_code" type="text" class="form-control" placeholder="Create a unique code">
            </div>
            <div class="form-group">
              <label for="comment">Description:</label>
              <textarea name="description" class="form-control" rows="2" id="link_description"></textarea>
            </div>
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
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
			<button type="button" class="btn btn-primary editprofilebutton" data-toggle="modal" data-target="#myModal">
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
	<div class="icon">
		<a href="{{$link->long_url}}" onclick="return showModalPostViaLink({{json_encode($link->short_url)}});"><img class="pic" src="{{$link->image}}" /></a>
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
		<h4 class="linktitle"><a href="{{$link->long_url}}" onclick="return showModalPostViaLink({{json_encode($link->short_url)}});">{{$link->title}}</a></h4>
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
		<div class="share-buttons">
			<div class="fb-share-button" data-href="http://ink.vu/{{$link->creator}}/{{$link->short_url}}" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a></div>
			<a class="twitter-share-button" href="https://twitter.com/intent/tweet?text={{urlencode($link->fullUrl())}}">Tweet</a>
			<g:plus action="share" href="{{$link->fullUrl()}}"></g:plus>
		</div>
	</div>
	<input class="link-url-holder" type="url" value="{{$link->fullUrl()}}" readonly />
        <a href="{{$link->fullUrl()}}" onclick="
		try {
			this.previousElementSibling.select();
			if(!document.execCommand('copy'))
				console.log('copy failed');
		} catch(e) {
			console.log(e);
		}
		return false;
	"><p class="clipboard">Copy Link</p></a>
        <p class="clicks">{{$link->clicks}} Clicks</p>
        
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
		STUFF
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
			var e = document.getElementById("link-" + shortlink);
			var w = document.querySelector("#modalRegister .wrapper");
			w.innerHTML = e.innerHTML;
			$("#modalRegister").modal('show');
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
<link rel='stylesheet' href='css/custom.css' />
@endsection
