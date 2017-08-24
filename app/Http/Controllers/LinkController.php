<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Redirect;

use App\Models\Link;
use App\Factories\LinkFactory;
use App\Helpers\CryptoHelper;
use App\Helpers\LinkHelper;
use App\Helpers\ClickHelper;
use Sunra\PhpSimple\HtmlDomParser;

class LinkController extends Controller {
    /**
     * Show the admin panel, and process admin AJAX requests.
     *
     * @return Response
     */

    private function renderError($message) {
        return redirect(route('index'))->with('error', $message);
    }

    public function getLinkInfo(Request $request) {

	    $long_url = $request->input("url");

        // get the open graph data
        $title = "";
        $description = "";
        $image = "";

        $domParser = HtmlDomParser::file_get_html($long_url);
        if (!empty($domParser)) {

            // title
            $arrFindTitleElement = array('title', 'meta[property=og:title]', 'meta[name=twitter:title]');
            foreach ($arrFindTitleElement as $find) {
                $titleElement = $domParser->find($find, 0);
                if (!empty($titleElement)) {
                    $title = $titleElement->getAttribute('content');
                    if (empty($image)) {
                        $title = html_entity_decode($titleElement->plaintext);
                    }
                    break;
                }
            }

            // description
            $arrFindDescElement = array('meta[name=description]', 'meta[property=og:description]', );
            foreach ($arrFindDescElement as $find) {
                $descElement = $domParser->find($find, 0);
                if (!empty($descElement)) {
                    $description = html_entity_decode($descElement->getAttribute('content'));
                    break;
                }
            }

            // image
            $arrFindImageElement = array('meta[property=og:image]', 'link[rel=apple-touch-icon]', 'link[rel=icon]');
            foreach ($arrFindImageElement as $find) {
                $imageElement = $domParser->find($find, -1);
                if (!empty($imageElement)) {
                    $image = $imageElement->getAttribute('href');
                    if (empty($image)) {
                        $image = $imageElement->getAttribute('content');
                    }
                }
            }

            if (empty($image)) {
                $arrValidExtension = array('png', 'jpg', 'jpeg');
                foreach ($domParser->find('img') as $imageElement) {
                    $ext = pathinfo($imageElement->src, PATHINFO_EXTENSION);
                    if (in_array($ext, $arrValidExtension, 1)) {
                        $image = $imageElement->src;
                    }
                }
            }

            if (!empty($image) && strpos($image, $long_url) === FALSE && strpos($image, 'http') === FALSE) {
                $image = rtrim($long_url, "/") . '/' . ltrim($image, "/");
                $image = $this->correctMediaUrl($image);
            }
        }

        $domParser->clear();
        unset($domParser);

	    $shell_url = escapeshellarg($long_url);

        $id = md5($long_url);
        // make the screenshot if there is no other image
        if(strlen($image) == 0) {
            if(!file_exists("/var/www/polr/public/screenshots/l$id.png")) {
                system("/opt/wkhtmltox/bin/wkhtmltoimage --crop-h 853 $shell_url /var/www/polr/public/screenshots/l$id.png");
                system("convert -resize 300x250 /var/www/polr/public/screenshots/l$id.png /var/www/polr/public/screenshots/$id.png");
                system("rm /var/www/polr/public/screenshots/l$id.png");
            }

            $image = "/screenshots/$id.png";
            if (!file_exists(public_path() . "/screenshots/$id.png")) {
                $image = '';
            }
        }

        return array(
            "title" => $title,
            "description" => $description,
            "image" => $image
        );
    }

    public function performShorten(Request $request) {
        if (env('SETTING_SHORTEN_PERMISSION') && !self::isLoggedIn()) {
            return redirect(route('index'))->with('error', 'You must be logged in to shorten links.');
        }

        // Validate URL form data
        $this->validate($request, [
            'link-url' => 'required|url',
            'custom-ending' => 'alpha_dash'
        ]);

        $long_url = $request->input('link-url');
        $custom_ending = $request->input('custom-ending');
        $offer_code = $request->input('offer_code');
        $is_secret = ($request->input('options') == "s" ? true : false);
        $creator = session('username');
        $link_ip = $request->ip();

        try {
            $link_object = LinkFactory::createLink($long_url, $is_secret, $custom_ending, $link_ip, $creator, true);
            if (!($link_object instanceof Link)) {
                throw new \Exception('Sorry, but your link already exist. ' . $link_object);
            }
	        $short_url = LinkFactory::formatLink($creator, $link_object->short_url, $link_object->secret_key);
        } catch (\Exception $e) {
            return self::renderError($e->getMessage());
        }

        $title = $request->input("title");
        $description = $request->input("description");
        $image = $request->input("image");

        $link_object->title = $title;
        $link_object->description = $description;
        $link_object->offer_code = $offer_code;
        $link_object->image = $image;
        $link_object->save();

        $short_url .= '?n=1';
        return redirect($short_url)->with('success', 'Post published.');

        return view('shorten_result', [
            'short_url' => $short_url,
            'screenshot' => "/screenshots/$id.png",
            'title' => $title,
            'description' => $description,
            'offer_code' => $offer_code,
            'image' => $image,
        ]);
    }

    public function performDeletion(Request $request) {
        $short_url = $request->input('short_url');
        $link = Link::where('short_url', $short_url)
            ->first();

        if ($link == null) {
        	return abort(404);
        }

	if ($link->creator != session('username')) {
		return abort(403);
	}

	$link->destroy($link->id);

	return redirect("/" . session('username'));
    }

    public function performRedirect(Request $request, $short_url, $secret_key=false) {
        $link = Link::where('short_url', $short_url)
            ->first();

        // Return 404 if link not found
        if ($link == null) {
        	return abort(404);
        }

        // Return an error if the link has been disabled
        // or return a 404 if SETTING_REDIRECT_404 is set to true
        if ($link->is_disabled == 1) {
            if (env('SETTING_REDIRECT_404')) {
                return abort(404);
            }

            return view('error', [
                'message' => 'Sorry, but this link has been disabled by an administrator.'
            ]);
        }

        // Return a 403 if the secret key is incorrect
        $link_secret_key = $link->secret_key;
        if ($link_secret_key) {
        	if (!$secret_key) {
        		// if we do not receieve a secret key
        		// when we are expecting one, return a 403
        		return abort(403);
        	}
        	else {
        		if ($link_secret_key != $secret_key) {
        			// a secret key is provided, but it is incorrect
        			return abort(403);
        		}
        	}
        }

        // Increment click count
        $long_url = $link->long_url;
        $clicks = intval($link->clicks);

        if (is_int($clicks)) {
            $clicks += 1;
        }
        $link->clicks = $clicks;
        $link->save();

        if (env('SETTING_ADV_ANALYTICS')) {
            // Record advanced analytics if option is enabled
            ClickHelper::recordClick($link, $request);
        }
        // Redirect to final destination

	if($request->input("go"))
	        return redirect()->to($long_url, 301);
	else {
		$ctrl = new IndexController();
		return $ctrl->userProfile($request, $link->creator, $link->short_url);

	}
    }

    private function correctMediaUrl($url)
    {
        $parseUrl = parse_url($url);
        $host = $parseUrl['scheme'] . '://' . $parseUrl['host'];
        $path = $parseUrl['path'];
        $arrPath = explode('/', trim($path, '/'));

        $lastKey = 0;
        $numberBack = 0;
        foreach ($arrPath as $key => $val) {
            if ($val == '..') {
                $numberBack ++;
                $lastKey = $key;
            }
        }

        $unsetKey = $lastKey - ($numberBack * 2);
        for ($i = $lastKey; $i >= $unsetKey; $i --) {
            unset($arrPath[$i]);
        }

        $correctUrl = '';
        foreach ($arrPath as $val) {
            $correctUrl .= '/' . $val;
        }

        return $host . $correctUrl;
    }

}
