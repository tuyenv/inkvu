<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\CryptoHelper;
use App\Models\User;
use App\Models\Link;
use App\Helpers\UserHelper;

class IndexController extends Controller {
    /**
     * Show the index page.
     *
     * @return Response
     */
    public function showIndexPage(Request $request) {
        if (env('POLR_SETUP_RAN') != true) {
            return redirect(route('setup'));
        }

        if (!env('SETTING_PUBLIC_INTERFACE') && !self::isLoggedIn()) {
            if (env('SETTING_INDEX_REDIRECT')) {
                return redirect()->to(env('SETTING_INDEX_REDIRECT'));
            }
            else {
                return redirect()->to(route('login'));
            }
        }

        $user = UserHelper::getUserByUsername(session('username'));

        if($user && $user->email != '')
            return $this->userProfile($request, $user->username);

        return view('index', ['username' => session('username'), 'user' => $user]);
    }

    public function userProfile(Request $request, $username, $shortlink = null) {
        $user = UserHelper::getUserByUsername($username);

        if (!$user) {
            return redirect(route('index'))->with('error', 'Invalid or disabled account: '.$username.'.');
        }

	return view('user_profile', [
		'isOwner' => session('username') == $username,
		'user' => $user,
		'showlink' => $shortlink,
        'isNewPost' => $request->input('n', 0),
        'no_div_padding' => true,
		'links' => Link::where('creator', $username)->select(['id', 'creator', 'short_url', 'long_url', 'clicks', 'created_at', 'title', 'description', 'image', 'offer_code'])->orderBy('id', 'DESC')->get()
	]);
    }
}
