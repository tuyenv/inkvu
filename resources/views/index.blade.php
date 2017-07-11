@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='css/index.css' />
@endsection

@section('content')
  @if (empty(session('username')))
	<style>
		.message {
		    padding:100px;
		    text-align:center;
		    width:100%;
		}

		div.container > div > .button {
		    font-size:18px;
		    white-space: normal;
		}

		@media only screen and (max-width: 500px) {

			div.container > div > .button {
			    font-size:16px;
			    white-space: normal;
			}
		}

		h2,h3 {
		    font-family:'lato';
		    font-weight:300;
		}
	</style>


	<div class="container">
		<div class="message">
			<img width="200" src="http://beta.ink.vu/wp-content/uploads/2017/04/inkvu-03.png" alt="Ink.vu" />
			<h2>Connect directly to your followers</h2>
			<h3>Join the free beta waiting list:</h3>
			<br>
			<a href="/instagram" class="button btn btn-primary btn-lg"><i class="fa fa-instagram" aria-hidden="true"></i>&nbsp;&nbsp;Register with Instagram</a>
		</div>
	</div>
  @else
	@include('beta_registration', [])

	<!--
  	Welcome back.
	<a href="/{{ session('username') }}">Go to your profile</a>
	<script>
		location.href = "/{{ session('username') }}";
	</script>
	-->
  @endif
@endsection
