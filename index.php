<?php

include 'inc.php';

ini_set('display_errors', Config::$display_errors);
ini_set('display_startup_errors', Config::$display_errors);
error_reporting(Config::$error_reporting);

include 'vendor/autoload.php';

use JsonRPC\Client;

Flight::set('flight.log_errors', false);
Flight::set('flight.handle_errors', false);

Flight::route('/about', function(){
    Flight::redirect("/eb0c65722a14-Some%20words%20about%20telescr.in");
});

Flight::route('/orwell', function(){
    Flight::redirect("/a1863c2f9dd9-Hello%20world%2C%20or%20some%20words%20about%20orwell");
});

//+caching
Flight::route('/', function() {
    Flight::view()->set('title', 'Create page - ' . Config::$title);
    $error = array();

    /*
      AjaxEvent::dispatchEvent("image.upload", function($act, $data) {//todo
      print_r($_FILES);
      if ($_FILES['image']['tmp_name']) {
      if (stristr($_FILES['image']['type'], 'image/')) {
      $hex = unpack("H*", file_get_contents($_FILES['image']['tmp_name']));

      }
      }
      }); */

    AjaxEvent::dispatchEvent("content.save", function($act, $arr) use($page, $canEdit) {
        $result = checkToken($arr);
        if (!$arr['oid'] || $arr['oid'] == 0xffffffff) {
            $ownerKey = hash('sha256', hash('sha256', $arr['pageId']));

            if (mb_strlen($arr['title'], 'UTF-8') < 20) {
                $error[] = 'Title is too small';
            }

            $content = array(
                'title' => f($arr['title']),
                'ownerKey' => $ownerKey,
                'author' => f($arr['author']),
                'added' => time(),
                'content' => $arr['securityKey'] ? $arr['content'] : json_decode($arr['content'], true),
            );

            if ($arr['securityKey']) {
                $content['secure'] = 1;
                $content['secureIv'] = $arr['secureIv'];
                $content['securityKey'] = $arr['securityKey'];
                $content['secureAlgorithm'] = $arr['secureAlgorithm'];
            }

            if (!count($error)) {
                $client = Flight::get('rpc');

                try {
                    $res = $client->execute('dbwrite', array(
                        Config::$orwellWriterAddress,
                        Config::$orwellDbAddress,
                        Config::$pageDatasetName,
                        base64_encode(json_encode(array($content)))
                    ));

                    if (is_string($res)) {
                        $txid = $res;
                        $tx = $client->execute("printtx", array($txid));
                        $oid = $tx['dataScriptContent'][0]['content']['oid'];
                        $result['txid'] = $txid;
                        $result['oid'] = $oid;
                        $result['title_url'] = rawurlencode(mb_substr($arr['title'], 0, 128, 'UTF-8'));
                        //save oid->txid to something
                        if ($oid) {
                            keyvalStorage::get()->setKey($oid, $txid);
                            $_SESSION['page_' . $oid] = $arr['pageId'];
                        }

                        return $result;
                    } elseif ($res['error']) {
                        $error[] = $res['error']['error'];
                    }
                } catch (Exception $e) {
                    $error[] = 'cant connect to blockchain';
                }
            }

            $result['errors'] = $error;
            return $result;
        }
    });


    Flight::renderTemplate('index', array(
    ));
});

Flight::route('/(@title)', function($title) {

    list($oid) = explode("-", $title);
    try {
        if ($_GET['key']){
            $_SESSION['page_'.$oid] = $_GET['key'];
            Flight::redirect("/$title");
         }

        $client = Flight::get('rpc');
        $res = $client->execute('dbgetbyid', array(
            Config::$orwellDbAddress,
            Config::$pageDatasetName,
            $oid,
            $rev
        ));

        if (!$res || is_array($res['error'])) {
            throw new Exception('Sorry, entry ' . $title . ' not found');
        }

        $page = $res;
        $canEdit = $_SESSION['page_' . $page['oid']];
        AjaxEvent::dispatchEvent("content.save", function($act, $data) use($page, $canEdit) {

            $result = checkToken($data);

            if (!$page['oid'] || $page['oid'] == "ffffffff")
                return $result;

            if (!$canEdit || !(hash('sha256', hash('sha256', $canEdit)) == $page['ownerKey']))
                return $result;

            if ($data['title'] == $page['title'] && ($data['content'] == json_encode($page['content'] || $data['content'] == $page['content'])))
                $error[] = 'Havent changes';

            $page['content'] = $page['secure'] ? $data['content'] : json_decode($data['content'], 1);
            if ($page['secure'])
                $page['author'] = $data['author'];

            $page['title'] = f($data['title']);
            if ($data['secureIv'])
                $page['secureIv'] = $data['secureIv'];

            if ($data['securityKey'] != $page['securityKey'])
                $page['securityKey'] = $data['securityKey'];

            if ((!$page['secure']) && mb_strlen($page['title'], 'UTF-8') < 20)
                $error[] = 'title too small';

            if (!count($error)) {

                $client = Flight::get('rpc');

                try {
                    $res = $client->execute('dbwrite', array(
                        Config::$orwellWriterAddress,
                        Config::$orwellDbAddress,
                        Config::$pageDatasetName,
                        base64_encode(json_encode($page))
                    ));

                    if (is_string($res)) {
                        $txid = $res;
                        $oid = $page['oid'];
                        $result['txid'] = $txid;
                        $result['oid'] = $oid;
                        $result['title_url'] = rawurlencode(mb_substr($page['title'], 0, 128, 'UTF-8'));
                        //save oid->txid to something
                        $a = keyvalStorage::get()->getKey("revisions/$oid");
                        if (!$a)
                            $a = array();
                        $a[] = $txid;
                        keyvalStorage::get()->setKey("revisions/$oid", $a);
                        setcookie("page_$oid", $canEdit, time() + Config::$cookieTime);
                        die(json_encode($result));
                    } elseif ($res['error']) {
                        $error[] = $res['error']['error'];
                    }
                } catch (Exception $e) {
                    $error[] = 'cant connect to blockchain';
                }
            }

            $result['errors'] = $error;
            return $result;
        });


        if (!$page['added'])
            $page['added'] = 1508518800;
        $page['canEdit'] = $canEdit;
        Flight::view()->set('title', !$page['secure'] ? $page['title'] : 'Encrypted content');
        Flight::view()->set('metatitle', !$page['secure'] ? $page['title'] : 'Encrypted content');
        Flight::view()->set('metacanonical', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $page['oid'] . (!$page['secure'] ? ('-' . rawurlencode(mb_substr($page['title'], 0, 128, 'UTF-8'))) : ''));
    } catch (Exception $ex) {
        Flight::renderTemplate('error', array(
            'error' => $ex->getMessage()
        ));
        return;
    }

    $revs = keyvalStorage::get()->getKey("revisions/" . $page['oid']);
    if (!$revs)
        $revs = array();

    $rev = array_reverse($revs);
    $gen = keyvalStorage::get()->getKey($page['oid']);

    Flight::renderTemplate('page', array(
        'page' => $page,
        'revisions' => $revs,
        'genesis' => $gen
    ));
});

Flight::map('renderTemplate', function($template, $data) {
    Flight::view()->set('metaimage', '/assets/img/promo.jpg');
    Flight::view()->set('metatitle', 'Telescr.in - store your information in blockchain!');
    Flight::view()->set('metadescription', 'Its a web-application, based on orwell blockchain, which can keep any type of information inside of a p2p database, called blockchain');
    Flight::render($template, $data, 'content');
    Flight::render('layout', $data);
});

Flight::set('rpc', new Client('http://' . Config::$nodeRpcHost . ':' . Config::$nodeRpcPort));
Flight::start();

function pd($var) {
    ob_start();
    var_dump($var);
    $v = ob_get_clean();
    $v = highlight_string("<?\n" . $v . '?>', true);
    $v = preg_replace('/=&gt;\s*<br\s*\/>\s*(&nbsp;)+/i', '=&gt;' . "\t" . '&nbsp;', $v);
    $v = '<div style="margin-bottom:5px;padding:10px;background-color:#fcfab6;border:1px solid #cc0000;">' . $v . '</div>';
    return $v;
}

function d() {
    $arr = func_get_args();
    foreach ($arr as $var) {
        echo pd($var);
    }
}

function dd() {
    $arr = func_get_args();
    call_user_func_array("d", $arr);
    die;
}

function checkToken($data) {


    $result = array('status' => 1,
        'token' => array(
            'time' => time(),
            'value' => md5($_SERVER['HTTP_USER_AGENT'] . '/' . $_SERVER['REMOTE_ADDR'] . '/' . (time() / 60)
            )
    ));

    $token = md5($_SERVER['HTTP_USER_AGENT'] . '/' . $_SERVER['REMOTE_ADDR'] . '/' . ($data['token_time'] / 60));
    if ($data['token'] != $token) {
        $result['status'] = 0;
        $result['error'] = 'invalid token';
    }

    return $result;
}
