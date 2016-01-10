<?php

require './libs/Slim/Slim.php';
\Slim\Slim:: registerAutoloader();

$app= new \Slim\Slim();

 
/** Echoing json response to client */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}


$app->get('/twitter', function() use ($app){
	
	//get the request
	$req = $app->request();
	$screen_name = $req->get('screenName');
	//$name_start = strrpos($srch, 'id=') + 1; // +1 so we don't include the space in our result
	//$name_end = strrpos($srch, ' ')+1;
	//$name_width= name_end-name_start;
	//echo $name_width;
	//$cityname = substr($question, $last_word_start);
	echo $screen_name;
	
	
	//build signature
	$oauth_hash = '';
	$oauth_hash .= 'count=20&';
	$oauth_hash .= 'oauth_consumer_key=DwqmZvelWwN6DERiteqUuFbip&';
	$oauth_hash .= 'oauth_nonce=' . time() . '&';
	$oauth_hash .= 'oauth_signature_method=HMAC-SHA1&';
	$oauth_hash .= 'oauth_timestamp=' . time() . '&';
	$oauth_hash .= 'oauth_token=2860541348-EH8MJm85ffjfEtRpltqHnkNudlmmD2UMS32zP2U&';
	$oauth_hash .= 'oauth_version=1.0&';
	$oauth_hash .= 'screen_name='. $screen_name ;

	$base = '';
	$base .= 'GET';
	$base .= '&';
	$base .= rawurlencode('https://api.twitter.com/1.1/statuses/user_timeline.json');
	$base .= '&';
	$base .= rawurlencode($oauth_hash);
	$key = '';
	$key .= rawurlencode('g5YF0YUHjELSKAwoaCYTroBMVCwzdbTyQhGTQdvnB4sIVvhBhq');
	$key .= '&';
	$key .= rawurlencode('8pzJoo9ZaJVGU6ZFCnxJR43WSK55A1Grzg655FpGffm7R');
	$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
	$signature = rawurlencode($signature);
	
	
	//build cUrl header
	$oauth_header = '';
	$oauth_header .= 'count="20", ';
	$oauth_header .= 'oauth_consumer_key="DwqmZvelWwN6DERiteqUuFbip", ';
	$oauth_header .= 'oauth_nonce="' . time() . '", ';
	$oauth_header .= 'oauth_signature="' . $signature . '", ';
	$oauth_header .= 'oauth_signature_method="HMAC-SHA1", ';
	$oauth_header .= 'oauth_timestamp="' . time() . '", ';
	$oauth_header .= 'oauth_token="2860541348-EH8MJm85ffjfEtRpltqHnkNudlmmD2UMS32zP2U", ';
	$oauth_header .= 'oauth_version="1.0", ';
	$oauth_header .= 'screen_name="' . $screen_name . '"';
	$curl_header = array("Authorization: Oauth {$oauth_header}", 'Expect:');

	// hitting URL
	$curl_request = curl_init();
	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_header);
	curl_setopt($curl_request, CURLOPT_HEADER, false);
	curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=20&screen_name=' .$screen_name . "");
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$json = curl_exec($curl_request);
	curl_close($curl_request); 

	$data = json_decode($json);
	$response = [];
	$response["answer"]= $data;
	//echo $data;
	echo "I am here";
	echoRespnse(200, $response);

});



$app->run();