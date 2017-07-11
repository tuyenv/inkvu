<div class="container">
    <div class="message">
        <img width="200" src="http://beta.ink.vu/wp-content/uploads/2017/04/inkvu-03.png" alt="Ink.vu">
        <h3>Almost done...</h3>
        <form id="login-form" action="/save-user" method="post" role="form" style="display: block;">
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <div class="form-group">
                <input type="text" name="first_name" id="fname" tabindex="1" class="form-control" placeholder="First Name" value="{{$user->first_name}}">
            </div>
            <div class="form-group">
                <input type="text" name="last_name" id="lname" tabindex="1" class="form-control" placeholder="Last Name" value="{{$user->last_name}}">
            </div>
            <div class="form-group">
                <input type="email" name="email" id="email" tabindex="2" class="form-control" placeholder="Email" value="{{$user->email}}">
            </div>
            <div class="form-group text-center">
                <input type="checkbox" tabindex="3" class="" name="keep_notified" id="remember" checked>
                <label for="remember">Keep me notified via email with launch updates</label>
            </div>
            <p>By clicking through, I agree to the Ink.Vu Terms &amp; Privacy Policy</p>
            <button type="submit" class="button btn btn-primary btn-lg" style="width:100%;">COMPLETE SIGNUP</button>
        <form>
    </div>
</div>
<style>
h2,h3 {
    font-family:'lato';
    font-weight:300;
}
.message {
    padding:100px;
    text-align:center;
    width:100%;
}
#login-form {
    max-width: 320px;
    margin-left:auto;
    margin-right:auto;
}

div.container .button {
    font-size:18px;
    white-space: normal;
}
</style>
