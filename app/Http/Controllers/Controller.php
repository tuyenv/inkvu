<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\View;

class Controller extends BaseController {

    public function __construct()
    {
        if (!empty(session('username'))) {
            $user = UserHelper::getUserByUsername(session('username'));
            View::share('user', $user);
        }

        if (session('insta_token')) {
            $instaMedia = $this->getUserInstagramMedia(session('insta_token'));
            View::share('instaMedia', $instaMedia);
        }
    }

    private function getUserInstagramMedia($instaToken)
    {
        $urlEndpoint = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$instaToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $urlEndpoint);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $data = curl_exec($ch);
        curl_close($ch);

        $resData = json_decode($data, true);
        if (isset($resData['data']) && !empty($resData['data'])) {
            return $resData['data'];
        }

        return false;
    }

    protected static function currIsAdmin() {
        $role = session('role');
        if ($role == 'admin') {
            return true;
        }
        else {
            return false;
        }
    }

    protected static function isLoggedIn() {
        $username = session('username');
        if (!isset($username)) {
            return false;
        }
        else {
            return true;
        }
    }

    protected static function checkRequiredArgs($required_args=[]) {
        foreach($required_args as $arg) {
            if ($arg == NULL) {
                return false;
            }
        }
        return true;
    }

    protected static function ensureAdmin() {
        if (!self::currIsAdmin()) {
            abort(401, 'User not admin.');
        }
        return true;
    }

    protected static function ensureLoggedIn() {
        if (!self::isLoggedIn()) {
            abort (401, 'User must be authenticated.');
        }
        return true;
    }
}
