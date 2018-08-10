@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/signup.css' />
@endsection

@section('content')
<div class="center-text">
    <h2 class='title'>Register</h2>
<div class='col-md-1'></div>
<div class='col-md-4'>
	<br /><br />
	<a style="font-size: 200%;" href="{{route('instagram')}}">Register with Instagram</a><br><hr>
    <a style="font-size: 200%;" href="{{route('google')}}">Register with Google</a>
</div>
<div class='col-md-2'>
	<br /><br />OR
</div>
<div class='col-md-4' style="text-align: left;">
    <form action='{{route('psignup')}}' method='POST'>
        Username: <input type='text' name='username' class='form-control form-field' placeholder='Username' />
            <p class="tip">The username you will use to login to {{env('APP_NAME')}}.</p>
        Password: <input type='password' name='password' class='form-control form-field' placeholder='Password' />
            <p class="tip">The secure password you will use to login to {{env('APP_NAME')}}.</p>
        Email: <input type='email' name='email' class='form-control form-field' placeholder='Email' />
        <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <p class="tip">The email you will use to verify your account or to recover your account.</p>
        <input type="submit" class="btn btn-default btn-success" value="Register"/>
        <p class='login-prompt'>
            <small>Already have an account? <a href='{{route('login')}}'>Login</a></small>
        </p>
    </form>
</div>
<div class='col-md-1'></div>
</div>
@endsection
