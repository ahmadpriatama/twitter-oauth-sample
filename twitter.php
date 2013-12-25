<?php
require "tmhOAuth.php";

session_start();

$config = array(
	// 'curl_proxy' => "localhost:8888",
	'curl_ssl_verifypeer' => false,
	'curl_ssl_verifyhost' => false,
	'consumer_key' => 'CONSUMER_KEY',
	'consumer_secret' => 'CONSUMER_SECRET',
);
$twt = new tmhOAuth($config);

if (!isset($_GET["oauth_verifier"])) {
	$twt->request("POST", $twt->url("oauth/request_token", ""), array(  
        'oauth_callback'    => "http://localhost/twitter.php"
    ));	
	
	if($twt->response["code"] == 200) {  
		  
		// get and store the request token  
		$response = $twt->extract_params($twt->response["response"]);  
		$_SESSION["authtoken"] = $response["oauth_token"];  
		$_SESSION["authsecret"] = $response["oauth_token_secret"];  

		// state is now 1  
		$_SESSION["authstate"] = 1;  

		// redirect the user to Twitter to authorize  
		$url = 'https://api.twitter.com/oauth/authorize?oauth_token=' . $response["oauth_token"];
		header("Location:$url");  
	} 
} else {
	$twt->config["user_token"] = $_SESSION["authtoken"];
	$twt->config["user_secret"] = $_SESSION["authsecret"];
	$twt->request("POST", $twt->url("oauth/access_token", ""), array(  
		'oauth_verifier'    => $_GET["oauth_verifier"]  
	));  
	if($twt->response["code"] == 200) {  
  
        // get the access token and store it in a cookie  
        $response = $twt->extract_params($twt->response["response"]);  
        echo "token: ".$response["oauth_token"] . "\nsecret: ". $response["oauth_token_secret"];  
  
        // state is now 2  
        $_SESSION["authstate"] = 2;  
  
        // redirect user to clear leftover GET variables  
    }  ;
} 