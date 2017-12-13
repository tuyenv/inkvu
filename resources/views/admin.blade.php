@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/admin.css'>
<link rel='stylesheet' href='/css/datatables.min.css'>
@endsection

@section('content')
<div ng-controller="AdminCtrl" class="ng-root">
    <div class='col-md-2'>
        <ul class='nav nav-pills nav-stacked admin-nav' role='tablist'>
            <li role='presentation' aria-controls="home" class='admin-nav-item active'><a href='#home'>Home</a></li>
            <li role='presentation' aria-controls="links" class='admin-nav-item'><a href='#links'>Links</a></li>
            <li role='presentation' aria-controls="settings" class='admin-nav-item'><a href='#settings'>Settings</a></li>

            @if ($role == $admin_role)
            <li role='presentation' class='admin-nav-item'><a href='#admin'>Admin</a></li>
            @endif

            @if ($api_active == 1)
            <li role='presentation' class='admin-nav-item'><a href='#developer'>Developer</a></li>
            @endif
        </ul>
    </div>
    <div class='col-md-10'>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <h2>Welcome to your {{env('APP_NAME')}} dashboard!</h2>
                <p>Use the links on the left hand side to navigate your {{env('APP_NAME')}} dashboard.</p>
            </div>

            <div role="tabpanel" class="tab-pane" id="links">
                @include('snippets.link_table', [
                    'table_id' => 'user_links_table'
                ])
            </div>

            <div role="tabpanel" class="tab-pane" id="settings">
                <h3>User Profile</h3>
                    <img style="float: left; margin-right: 1em; margin-bottom: 1em;" src="{{$profile_picture_url}}" alt="" />
                    Website: <a href="{{$website}}">{{$website}}</a>
                    <div>{{$bio}}</div>
                    <br clear="both" />

                <form action='/admin/action/change_setting' method='POST'>
                    Email: <input disabled class="form-control password-box" type='text' name='txt_email' value="{{$user->email}}" />
                    Mobile: <input disabled class="form-control password-box" type='text' name='txt_mobile' value="{{$user->mobile}}" />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <a class='btn btn-success change-password-btn' data-toggle="modal" data-target="#pushModal">Change</a>
                </form>

                <h3>Change Password</h3>
                <form action='/admin/action/change_password' method='POST'>
                    Old Password: <input class="form-control password-box" type='password' name='current_password' />
                    New Password: <input class="form-control password-box" type='password' name='new_password' />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <input type='submit' class='btn btn-success change-password-btn'/>
                </form>
            </div>

            @if ($role == $admin_role)
            <div role="tabpanel" class="tab-pane" id="admin">
                <h3>Links</h3>
                @include('snippets.link_table', [
                    'table_id' => 'admin_links_table'
                ])

                <h3 class="users-heading">Users</h3>
                <a ng-click="state.showNewUserWell = !state.showNewUserWell" class="btn btn-primary btn-sm status-display">New</a>

                <div ng-if="state.showNewUserWell" class="new-user-fields well">
                    <table class="table">
                        <tr>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                        <tr id="new-user-form">
                            <td><input type="text" class="form-control" id="new-username"></td>
                            <td><input type="password" class="form-control" id="new-user-password"></td>
                            <td><input type="email" class="form-control" id="new-user-email"></td>
                            <td>
                                <select class="form-control new-user-role" id="new-user-role">
                                    @foreach  ($user_roles as $role_text => $role_val)
                                        <option value="{{$role_val}}">{{$role_text}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a ng-click="addNewUser($event)" class="btn btn-primary btn-sm status-display new-user-add">Add</a>
                            </td>
                        </tr>
                    </table>
                </div>

                @include('snippets.user_table', [
                    'table_id' => 'admin_users_table'
                ])

            </div>
            @endif

            @if ($api_active == 1)
            <div role="tabpanel" class="tab-pane" id="developer">
                <h3>Developer</h3>

                <p>API keys and documentation for developers.</p>
                <p>
                    Documentation:
                    <a href='http://docs.polr.me/en/latest/developer-guide/api/'>http://docs.polr.me/en/latest/developer-guide/api/</a>
                </p>

                <h4>API Key: </h4>
                <div class='row'>
                    <div class='col-md-8'>
                        <input class='form-control status-display' disabled type='text' value='{{$api_key}}'>
                    </div>
                    <div class='col-md-4'>
                        <a href='#' ng-click="generateNewAPIKey($event, '{{$user_id}}', true)" id='api-reset-key' class='btn btn-danger'>Reset</a>
                    </div>
                </div>


                <h4>API Quota: </h4>
                <h2 class='api-quota'>
                    @if ($api_quota == -1)
                        unlimited
                    @else
                        <code>{{$api_quota}}</code>
                    @endif
                </h2>
                <span> requests per minute</span>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="pushModal" tabindex="-1" role="dialog" aria-labelledby="pushModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change settings?</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="subscribeoptions">
                        <p id="errMsg" style="color: red; display: none;"></p>

                        <div class="optionslabel">Email Notifications</div>
                        <div class="input-group email-group">
                            <input value="{{$user->email}}" type="text" id="push_email" class="form-control" placeholder="Your Email">
                                <span class="input-group-btn">
                                  <button class="btn btn-default pushVerifyEmail" type="button">Verify</button>
                                </span>
                        </div>

                        <div class="verifylabelemail" style="display: none; margin: 10px 0px;">Your Email: <strong style="color: #e95950"></strong></div>
                        <div class="input-group verify-group-email" style="display: none">
                            <input value="" type="text" id="push_email_verify" class="form-control" placeholder="Please enter verify number #">
                                <span class="input-group-btn">
                                    <button class="btn btn-default pushVerifyNumberEmail" type="button">Verify</button>
                                </span>
                        </div>
                        <div class="email_verified" style="color: #e95950; display: none"></div>
                        <div class="verify_error_label_email" style="color: #e95950; display: none">Verify number incorrect</div>



                        <div class="optionslabel">Mobile Notifications</div>
                        <div class="input-group mobile-group">
                            <input value="{{$user->mobile}}" type="text" id="push_mobile" class="form-control" placeholder="Your Mobile #">
                                <span class="input-group-btn">
                                    <button class="btn btn-default pushVerify" type="button">Verify</button>
                                </span>
                        </div>
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
                <button type="button" class="btn btn-primary saveNotify">Save Options</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
{{-- Include modal templates --}}
@include('snippets.modals')

{{-- Include extra JS --}}
<script src='/js/handlebars-v4.0.5.min.js'></script>
<script src='/js/datatables.min.js'></script>
<script src='/js/api.js'></script>
<script src='/js/AdminCtrl.js'></script>

{{-- Extra templating --}}
<script id="api-modal-template" type="text/x-handlebars-template">
    <div>
        <p>
            <span>API Active</span>:

            <code class='status-display'>
                @{{#if api_active}}True@{{else}}False@{{/if}}</code>

            <a ng-click="toggleAPIStatus($event, '@{{user_id}}')" class='btn btn-xs btn-success'>toggle</a>
        </p>
        <p>
            <span>API Key: </span><code class='status-display'>@{{api_key}}</code>
            <a ng-click="generateNewAPIKey($event, '@{{user_id}}', false)" class='btn btn-xs btn-danger'>reset</a>
        </p>
        <p>
            <span>API Quota (req/min, -1 for unlimited):</span> <input type='number' class='form-control api-quota' value='@{{api_quota}}'>
            <a ng-click="updateAPIQuota($event, '@{{user_id}}')" class='btn btn-xs btn-warning'>change</a>
        </p>
    </div>
</script>

<script>
    $('#pushModal').on('click', '.saveNotify, .pushSave', function () {

        var data = {
            push_email: $("#push_email").val(),
            push_mobile: $("#push_mobile").val()
        };

        $.ajax({
            url: '/save_settings',
            data: data,
            dataType: 'json',
            type: 'POST',
            success: function(jsonData) {
                if (parseInt(jsonData.code) != 1) {
                    $("#errMsg").html(jsonData.message);
                    $("#errMsg").show();
                } else {
                    location.reload();
                    $("#pushModal").modal('hide');
                    $("#errMsg").hide();
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
@endsection
