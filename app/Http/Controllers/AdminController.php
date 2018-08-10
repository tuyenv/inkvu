<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Hash;

use App\Models\Link;
use App\Models\User;
use App\Helpers\UserHelper;
use App\Models\NotifySettings;

class AdminController extends Controller {
    /**
     * Show the admin panel, and process setting changes.
     *
     * @return Response
     */

    public function displayAdminPage(Request $request) {
        if (!$this->isLoggedIn()) {
            return redirect(route('login'))->with('error', 'Please login to access your dashboard.');
        }

        $username = session('username');
        $role = session('role');

        $user = UserHelper::getUserByUsername($username);

        if (!$user) {
            return redirect(route('index'))->with('error', 'Invalid or disabled account.');
        }

        if ($user->profile_picture_url == '') {
            $user->profile_picture_url = url('/') . '/img/default.jpg';
        }

        return view('admin', [
            'user' => $user,
            'role' => $role,
            'admin_role' => UserHelper::$USER_ROLES['admin'],
            'user_roles' => UserHelper::$USER_ROLES,

            'profile_picture_url' => $user->profile_picture_url,
            'bio' => $user->bio,
            'website' => $user->website,

            'api_key' => $user->api_key,
            'api_active' => $user->api_active,
            'api_quota' => $user->api_quota,
            'user_id' => $user->id
        ]);
    }

    public function changePassword(Request $request) {
        if (!$this->isLoggedIn()) {
            return abort(404);
        }

        $username = session('username');
        $user = UserHelper::getUserByUsername($username);
        if ($user->is_first_pass == 0) {
            $old_password = $request->input('current_password');
            $new_password = $request->input('new_password');

            if (UserHelper::checkCredentials($username, $old_password) == false) {
                // Invalid credentials
                return redirect('admin')->with('error', 'Current password invalid. Try again.');
            }
            else {
                // Credentials are correct

                $user->password = Hash::make($new_password);
                $user->save();

                $request->session()->flash('success', "Password changed successfully.");
                return redirect(route('admin'));
            }
        } else {
            $new_password = $request->input('new_password');
            $user->password = Hash::make($new_password);
            $user->is_first_pass = 0;
            $user->save();

            $request->session()->flash('success', "Password changed successfully.");
            return redirect(route('admin'));
        }
    }

    public function changeSettings(Request $request)
    {
        if (!$this->isLoggedIn()) {
            return abort(404);
        }

        $username = session('username');
        $email = $request->input('txt_email');
        $mobile = $request->input('txt_mobile');
        $inputUsername = $request->input('txt_username');
        $checkUser = UserHelper::getUsersByUsername($inputUsername);

        if ($inputUsername != $username) {
            if (count($checkUser) > 0) {
                $userInfo = $checkUser[0];
                if ($userInfo->email != $email) {
                    $request->session()->flash('error', "Username already exists");
                    return redirect(route('admin'));
                }
            } else if (count($checkUser) > 1) {
                $request->session()->flash('error', "Username already exists");
                return redirect(route('admin'));
            }
        }

        $user = UserHelper::getUserByUsername($username);
        $user->email = $email;
        $user->mobile = $mobile;
        $user->username = $inputUsername;
        $user->save();

        $request->session()->put('username', $inputUsername);

        $notifySetting = NotifySettings::where('creator', $user->id)->get();
        foreach ($notifySetting as $noti) {
            $noti->email = $email;
            $noti->mobile = $mobile;
            $noti->save();
        }

        $request->session()->flash('success', "Settings changed successfully.");
        return redirect(route('admin'));
    }

    public function changePicture(Request $request)
    {
        if (!$this->isLoggedIn()) {
            return abort(404);
        }

        try {
            $this->validate($request, [
                'picture_profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('picture_profile')) {
                $username = session('username');
                $user = UserHelper::getUserByUsername($username);

                $image = $request->file('picture_profile');
                $name = $user->id . time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/users');
                $image->move($destinationPath, $name);
                $profile_picture_url = url('/') . '/users/' . $name;

                $user->profile_picture_url = $profile_picture_url;
                $user->save();
                $request->session()->flash('success', "Picture changed successfully.");
            }
        } catch (\Exception $e) {

        }

        return redirect(route('admin'));
    }
}
