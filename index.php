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


$app->get('/active', function() use ($app){
	
	//get the request
	$req = $app->request();
	$screen_name = $req->get('twitter_id');
	$time_span=strtolower($req->get('time_span'));
	
	//build signature
	$oauth_hash = '';
	$oauth_hash .= 'count=200&';
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
	$oauth_header .= 'count="200", ';
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
	curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=200&screen_name=' .$screen_name . "");
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$json = curl_exec($curl_request);
	curl_close($curl_request); 

	$data = json_decode($json);
	$size_data=count($data);
	//echo $size_data;
	$response = [];
	
	//parse the data to get active hour
	 if($time_span == "day"){
		 $tweetArray=[];
		 $maxCount=0;
		 $daycount=array(0,0,0,0,0,0,0);
		 //$tweetArray=$data[0]->created_at;
		// echo $tweetArray;
		
		 for($i=0;$i<$size_data;$i++){
			  $temp= $data[$i]->created_at;
			  $tweetArray["$i"]=substr($temp,0,3);
				//echo $tweetArray[$i];
				if($tweetArray["$i"]=="Sun")
					$daycount[0]++;
				else if($tweetArray["$i"]=="Mon")
					$daycount[1]++;
				else if($tweetArray["$i"]=="Tue")
					$daycount[2]++;
				else if($tweetArray["$i"]=="Wed")
					$daycount[3]++;
				else if($tweetArray["$i"]=="Thu")
					$daycount[4]++;
				else if($tweetArray["$i"]=="Fri")
					$daycount[5]++;
				else if($tweetArray["$i"]=="Sat")
					$daycount[6]++;
		  }
		  $maxCount=max($daycount);
		  //echo $maxCount;
		   for($i=0;$i<7;$i++){
			   if($daycount[$i]==$maxCount)
				   $response["$i"] = $maxCount;
		   }	
	 }
	 else if($time_span == "hour"){
		 $tweetArray=[];
		 $maxCount=0;
		 $hourCount=array_fill(0,24,null);
		 for($i=0;$i<$size_data;$i++){
			  $temp= $data[$i]->created_at;
			  $temp = substr($temp,10,10);
			  $tweetArray["$i"]=intval(substr($temp,0,3));
			  //echo $tweetArray[$i]." ,";
			  //assigning frequency of tweets per hour
			  for($j=0;$j<24;$j++){
				  if($tweetArray["$i"]==$j){
					  $hourCount[$j]++;
				  }
			  }
		   }
		 $maxCount=max($hourCount);
		 //echo $maxCount;
		 //finding maximum frequency
		 for($k=0;$k<24;$k++){
			if($hourCount[$k]==$maxCount)
				$response["$k"] = $maxCount;
		 }  
	  }
	
	//$response["answer"]= $data;
	//echo $data;
	//echo "I am here";
	echoRespnse(200, $response);
});

$app->get('/hashtag', function() use ($app){
	
	//get the request
	$req = $app->request();
	$screen_name = $req->get('twitter_id');
	$n=$req->get('n');
	
	//build signature
	$oauth_hash = '';
	$oauth_hash .= 'count=200&';
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
	$oauth_header .= 'count="200", ';
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
	curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=200&screen_name=' .$screen_name . "");
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$json = curl_exec($curl_request);
	curl_close($curl_request); 
	$data = json_decode($json);
	
	$size_data=count($data);
	$temp=[];
	$response = [];
	$resultArray=[];
	
	for($i=0;$i<$size_data;$i++){
		$countTag=count($data[$i]->entities->hashtags);
		for($j=0;$j<$countTag;$j++)
			  $temp[]= $data[$i]->entities->hashtags[$j]->text;
			  //echo $temp;
	}
	for($i=0;$i<count($temp);$i++){
		$count=1;
		//echo $temp[$i];
		for($j=0;$j<$i;$j++){
			
			if($temp[$j]==$temp[$i]){
				//$a=$temp[$j];
				$count++;
			}	
		}
		$response["$temp[$i]"]=$count;
			
	}
	asort($response);
	$arrayResult=array_reverse($response);
	$arrayResult= array_slice($arrayResult, 0, $n); 
	//for($i=count($response);$i>count($response)-$n;$i--)
	
	// $resultArray = $resultArray.sort(function (a, b) {
    // return a.val.localeCompare( b.val );
	// });
	// $response=json_decode($resultArray);


	//$resultArray = array_slice($response, 0, $n); 
	 // foreach ($response as $key => $value) {
     // echo "$key: $value\n";
	 
	echoRespnse(200, $arrayResult);
});
$app->get('/authority', function() use ($app){
	
	//get the request
	$req = $app->request();
	$screen_name = $req->get('twitter_id');
	$tweet=strtolower($req->get('tweet'));
	
	//build signature
	$oauth_hash = '';
	$oauth_hash .= 'count=200&';
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
	$oauth_header .= 'count="200", ';
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
	curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=200&screen_name=' .$screen_name . "");
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
	$json = curl_exec($curl_request);
	curl_close($curl_request); 
	
	$data = json_decode($json);
	$size_data=count($data);
	$temp="";
	//fetching text from timeline
	 for($i=0;$i<$size_data;$i++){
			  $temp= $temp . $data[$i]->text;
			  //$temp = preg_replace('/[^A-Za-z0-9\-]/', '', $temp); // Removes special chars.
			  //echo $temp;
	 }
	 //counting frequency of words in timeline
	 $wordsInTimeline = str_word_count($temp, 1);
	 $frequencyInText = array_count_values($wordsInTimeline);
	 //counting frequency of words in tweet
	 $wordsInTweet= str_word_count($tweet,1);
	 $frequencyInTweet= array_count_values($wordsInTweet);
	 
	 echoRespnse(200, $frequencyInTweet);
});
$app->run();