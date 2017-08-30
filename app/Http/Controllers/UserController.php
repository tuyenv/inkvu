<?php
namespace App\Http\Controllers;
use Mail;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;

use App\Helpers\CryptoHelper;
use App\Helpers\UserHelper;

use App\Factories\UserFactory;

class UserController extends Controller {
    /**
     * Show pages related to the user control panel.
     *
     * @return Response
     */
    public function displayLoginPage(Request $request) {
        return view('login');
    }

    public function displaySignupPage(Request $request) {
        return view('signup');
    }

    public function displayLostPasswordPage(Request $request) {
        return view('lost_password');
    }

    public function performLogoutUser(Request $request) {
        $request->session()->forget('username');
        return redirect()->route('index');
    }

    public function performLogin(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $credentials_valid = UserHelper::checkCredentials($username, $password);

        if ($credentials_valid != false) {
            // log user in
            $role = $credentials_valid['role'];
            $request->session()->put('username', $username);
            $request->session()->put('role', $role);

            // update last_login
            $user = $credentials_valid['user'];
            $request->session()->put('userId', $user->id);
            $request->session()->put('isNewUser', 0);
            if (empty($user->last_login)) {
                $request->session()->put('isNewUser', 1);
            }
            $user->last_login = date("Y-m-d H:i:s");
            $user->save();

            return redirect()->route('index');
        }
        else {
            return redirect('login')->with('error', 'Invalid password or inactivated account. Try again.');
        }
    }

    public function saveUser(Request $request) {
        $username = session('username');

        $user = user::where('active', 1)
             ->where('username', $username)
             ->first();

        $user->first_name = $request->input("first_name");
        $user->last_name = $request->input("last_name");
        $user->email = $request->input("email");
        $user->keep_notified = $request->input("keep_notified") ? 1 : 0;
        $user->save();

        return redirect("/{$username}?show=welcome");
    }

    public function instagram(Request $request) {
	$client_id = env("INSTAGRAM_ID");
	$client_secret = env("INSTAGRAM_SECRET");
	$redirect_url = env('APP_PROTOCOL') . env('APP_ADDRESS') . '/' . 'instagram';

	if(isset($_GET['error'])) {
		echo htmlentities($_GET['error_description']);
		exit;
	}

	if(isset($_GET['code'])) {
		$post = array();

		$post["client_id"] = $client_id;
		$post["client_secret"] = $client_secret;
		$post["grant_type"] = "authorization_code";
		$post["redirect_uri"] = $redirect_url;
		$post["code"] = $_GET['code'];

		$ch = curl_init("https://api.instagram.com/oauth/access_token");

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$json = json_decode($response);

                $username = $json->user->username;

                $users = User::where('active', 1)
                    ->where('username', $username);

                if($users->count() == 0) {
                    $email = "";
                    $password = CryptoHelper::generateRandomHex(50);
                    $active = 1;
                    $api_active = false;
                    $api_key = null;
                    if (env('SETTING_AUTO_API')) {
                        // if automatic API key assignment is on
                        $api_active = 1;
                        $api_key = CryptoHelper::generateRandomHex(env('_API_KEY_LENGTH'));
                    }
                    $ip = $request->ip();

                    $user = UserFactory::createUser($username, $email, $password, $active, $ip, $api_key, $api_active);

                } else {
                    $user = $users->first();
                    $user->instagram_access_token = $json->access_token;

                    $user->bio = $json->user->bio;
                    $user->website = $json->user->website;
                    $user->profile_picture_url = $json->user->profile_picture;
                }

                $request->session()->put('isNewUser', 0);
                $request->session()->put('userId', $user->id);
                if (empty($user->last_login)) {
                    $request->session()->put('isNewUser', 1);
                }
                $user->last_login = date("Y-m-d H:i:s");
                $user->save();

                $user = user::where('active', 1)
                    ->where('username', $username)
                    ->first();

                $role = $user->role;
                $request->session()->put('username', $username);
                $request->session()->put('insta_token', $json->access_token);

		return redirect("/");
	} else {
		return redirect("https://api.instagram.com/oauth/authorize/?client_id={$client_id}&redirect_uri=".urlencode($redirect_url)."&response_type=code");
	}
    }

    public function performSignup(Request $request) {
        if (env('POLR_ALLOW_ACCT_CREATION') == false) {
            return redirect(route('index'))->with('error', 'Sorry, but registration is disabled.');
        }

        // Validate signup form data
        $this->validate($request, [
            'username' => 'required|alpha_dash',
            'password' => 'required',
            'email' => 'required|email'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $email = $request->input('email');

        if (env('SETTING_RESTRICT_EMAIL_DOMAIN')) {
            $email_domain = explode('@', $email)[1];
            $permitted_email_domains = explode(',', env('SETTING_ALLOWED_EMAIL_DOMAINS'));

            if (!in_array($email_domain, $permitted_email_domains)) {
                return redirect(route('signup'))->with('error', 'Sorry, your email\'s domain is not permitted to create new accounts.');
            }
        }

        $ip = $request->ip();

        $user_exists = UserHelper::userExists($username);
        $email_exists = UserHelper::emailExists($email);

        if ($user_exists || $email_exists) {
            // if user or email email
            return redirect(route('signup'))->with('error', 'Sorry, your email or username already exists. Try again.');
        }

        $acct_activation_needed = env('POLR_ACCT_ACTIVATION');

        if ($acct_activation_needed == false) {
            // if no activation is necessary
            $active = 1;
            $response = redirect(route('login'))->with('success', 'Thanks for signing up! You may now log in.');
        }
        else {
            // email activation is necessary
            $response = redirect(route('login'))->with('success', 'Thanks for signing up! Please confirm your email to continue.');
            $active = 0;
        }

        $api_active = false;
        $api_key = null;

        if (env('SETTING_AUTO_API')) {
            // if automatic API key assignment is on
            $api_active = 1;
            $api_key = CryptoHelper::generateRandomHex(env('_API_KEY_LENGTH'));
        }

        $user = UserFactory::createUser($username, $email, $password, $active, $ip, $api_key, $api_active);

        if ($acct_activation_needed) {
            Mail::send('emails.activation', [
                'username' => $username, 'recovery_key' => $user->recovery_key, 'ip' => $ip
            ], function ($m) use ($user) {
                $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

                $m->to($user->email, $user->username)->subject(env('APP_NAME') . ' account activation');
            });
        }

        return $response;
    }

    public function performSendPasswordResetCode(Request $request) {
        if (!env('SETTING_PASSWORD_RECOV')) {
            return redirect(route('index'))->with('error', 'Password recovery is disabled.');
        }

        $email = $request->input('email');
        $ip = $request->ip();
        $user = UserHelper::getUserByEmail($email);

        if (!$user) {
            return redirect(route('lost_password'))->with('error', 'Email is not associated with a user.');
        }

        $recovery_key = UserHelper::resetRecoveryKey($user->username);

        Mail::send('emails.lost_password', [
            'username' => $user->username, 'recovery_key' => $recovery_key, 'ip' => $ip
        ], function ($m) use ($user) {
            $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

            $m->to($user->email, $user->username)->subject(env('APP_NAME') . ' Password Reset');
        });

        return redirect(route('index'))->with('success', 'Password reset email sent. Check your inbox for details.');
    }

    public function performActivation(Request $request, $username, $recovery_key) {
        $user = UserHelper::getUserByUsername($username, true);

        if (UserHelper::userResetKeyCorrect($username, $recovery_key, true)) {
            // Key is correct
            // Activate account and reset recovery key
            $user->active = 1;
            $user->save();

            UserHelper::resetRecoveryKey($username);
            return redirect(route('login'))->with('success', 'Account activated. You may now login.');
        }
        else {
            return redirect(route('index'))->with('error', 'Username or activation key incorrect.');
        }
    }

    public function performPasswordReset(Request $request, $username, $recovery_key) {
        $new_password = $request->input('new_password');
        $user = UserHelper::getUserByUsername($username);

        if (UserHelper::userResetKeyCorrect($username, $recovery_key)) {
            if (!$new_password) {
                return view('reset_password');
            }

            // Key is correct
            // Reset password
            $user->password = Hash::make($new_password);
            $user->save();

            UserHelper::resetRecoveryKey($username);
            return redirect(route('login'))->with('success', 'Password reset. You may now login.');
        }
        else {
            return redirect(route('index'))->with('error', 'Username or reset key incorrect.');
        }
    }

    public function performProfileLinksUpdate(Request $request) {
        self::ensureLoggedIn();
        $username = session('username');
        $user = UserHelper::getUserByUsername($username);

        $user->facebook_url = $request->input("facebook_url");
        $user->twitter_url = $request->input("twitter_url");
        $user->instagram_url = $request->input("instagram_url");

        $user->save();

       return redirect("/{$username}");
    }

}
