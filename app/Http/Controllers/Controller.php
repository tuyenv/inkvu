<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\View;

class Controller extends BaseController {

    public function __construct() {
        if (!empty(session('username'))) {
            $user = UserHelper::getUserByUsername(session('username'));
            View::share ( 'user', $user );
        }
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
