<?php
require_once __DIR__ . '/../bootstrap.php'; //credentials and such

/**
 * Very Simple Request Router
 *
 * @author Tim Lytle <tim@timlytle.net>
 */

//some common setup, not really needed for serving the UI, but whatever - can you say premature optimization?
$mongo = new MongoClient(MONGO);
$db = $mongo->proxy;
$nexmo = new Nexmo(NEXMO_KEY, NEXMO_SECRET);
$proxy = new Proxy($nexmo, NEXMO_FROM, $db);

//request looks to be from Nexmo
$request = array_merge($_GET, $_POST); //method configurable via Nexmo API / Dashboard
if(isset($request['msisdn'], $request['text'])){
    try{
        $proxy->processMessage($_REQUEST['msisdn'], $_REQUEST['text']);
    } catch (Exception $e) {
        error_log($e); //NOTE: if you want Nexmo to retry, just give a non-2XX response
    }
    return;
}

//request look to be for API
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
    echo  json_encode($proxy->__toArray());
    return;
}

//nothin' special, render admin view
include 'index.phtml';