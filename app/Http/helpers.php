<?php

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            // 'i' => 'minute',
            //'s' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}

function renderSteemitTag($tags) {
    $arrTag = explode(',', $tags);
    if (!empty($arrTag)) {

    }
}


function removeHtmlScript($str)
{
    $REGEX_REMOVE_HTML_ATTRIBUTE = '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i';
    $REGEX_REPLACE_HTML_ATTRIBUTE = '<$1$2>';
    $str = htmlspecialchars_decode($str, ENT_QUOTES);
    $str = preg_replace('!\s+!', ' ', trim($str));
    $str = preg_replace($REGEX_REMOVE_HTML_ATTRIBUTE, $REGEX_REPLACE_HTML_ATTRIBUTE, $str);
    $str = trim(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str));
    $str = preg_replace('/(\s+|^)@\S+/', '', $str);

    return $str;
}