<?php

session_start();

Class Config {

    public static $error_reporting = E_ERROR;
    public static $display_errors = 1;
    public static $title = 'telescreen';
    public static $nodeRpcHost = '127.0.0.1';
    public static $nodeRpcPort = 49999;
    public static $sitename = 'telescreen';
    public static $memcache_host = 'localhost';
    public static $memcache_port = 11211;
    public static $onpage = 100;
    public static $orwellDbAddress = 'oKumN5H6dSccwFRv2cd19adcG6CjnJEcE4';
    public static $orwellWriterAddress = 'oZmpGpF9QFBhqksZWqRpnUv95k7pdiRDrc'; //you need to have private key from this address in your wallet to send data.
    public static $pageDatasetName = 'pages';
    public static $imagesDatasetName = 'images';
    public static $storagePath = '';
    public static $storageFile = "simpledb"; //just to save txid and show it to user.
    public static $cookieTime = 224985600;

}

Config::$storagePath = getcwd() . "/../";

require 'lib/keyvalstorage.class.php';
require 'lib/ajaxevent.class.php';
keyvalStorage::get();

updateCookie();

function updateCookie() {
    //dd($_SESSION, $_COOKIE);
    $pages_cookie = $_COOKIE;
    $pages_session = $_SESSION;

    $clist = array();
    foreach ($pages_cookie as $i => $v) {
        if (stristr($i, 'page_'))
            $clist[$i] = $v;
    }

    $slist = array();
    foreach ($pages_session as $i => $v) {
        if (stristr($i, 'page_'))
            $slist[$i] = $v;
    }

    $pages_cookie = $clist;
    $pages_session = $slist;

    $arr = array();

    foreach ($pages_cookie as $k => $v) {
        if ($v && !$arr[$k])
            $arr[$k] = $v;
    }
    
    foreach ($pages_session as $k => $v) {
        if ($v && !$arr[$k])
            $arr[$k] = $v;
    }

    foreach ($arr as $k => $v) {

        $cookie = $_COOKIE[$k];
        $session = $_SESSION[$k];
        if (!$cookie && $session) {
            setcookie("{$k}", $session, time() + Config::$cookieTime, '/');
        }

        if ($cookie && !$session) {
            $_SESSION[$k] = $cookie;
        }


        if ($cookie && $session) {
            setcookie("{$k}", $session, time() + Config::$cookieTime, '/');
        }

        if (!$cookie && !$session) {
            unset($_SESSION[$v]);
            setcookie($k);
            continue;
        }
    }
}

function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'minute'),
        array(1, 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
    return $print;
}

function cache($id, $data, $exp = 3600) {
    if ($d = memget($id))
        return $d;

    $d = $data($id);
    memset($id, $d, $exp);
    return $d;
}

function cacheNo($id, $data) {
    return $data($id);
}

function memset($key, $val, $exp = 3600) {
    if (class_exists('Memcache')) {
        if (!Flight::has('memcache')) {
            Flight::set('memcache', new Memcache());
            Flight::get('memcache')->connect(Config::$memcache_host, Config::$memcache_port);
        }

        return Flight::get('memcache')->set(Config::$sitename . "_" . $key, $val, false, $exp);
    }

    return false;
}

function memget($key) {
    if (class_exists('Memcache')) {
        if (!Flight::has('memcache')) {
            Flight::set('memcache', new Memcache());
            Flight::get('memcache')->connect(Config::$memcache_host, Config::$memcache_port);
        }

        return Flight::get('memcache')->get(Config::$sitename . "_" . $key);
    }

    return false;
}

function memfree($key = '') {
    if (class_exists('Memcache')) {
        if (!Flight::has('memcache')) {
            Flight::set('memcache', new Memcache());
            Flight::get('memcache')->connect(Config::$memcache_host, Config::$memcache_port);
        }

        if ($key)
            return Flight::get('memcache')->delete(Config::$sitename . "_" . $key);
        else
            return Flight::get('memcache')->flush();
    }

    return false;
}

function f($val) {
    $text = strip_tags($val);
    $text = preg_replace('/[\'"<>\\\\]/i', "", $text);
    return $text;
}
