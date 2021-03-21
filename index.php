<?php

    $authorize_url = "https://account.withings.com/oauth2_user/authorize2";
    $token_url = "https://wbsapi.withings.net/v2/oauth2";
    $callback_uri = "http://localhost/";
    $api_url = "https://wbsapi.withings.net/measure";
    $client_id = "e7b9a30e8c8319884f84eb1642a462a17f8a9b7a9240c25bfe05fce442a309c8";
    $client_secret = "9ccc7b1fc33c390a6e13f7cebd2183a39656a708eb94040a2b716b0157ea18e9";

    if ($_GET["code"]) {
        $access_token = getAccessToken($_GET["code"]);
        echo 'access_token = '.$access_token.'<br />';
        $resource = getWeight($access_token);
        echo 'last weight recorded = '.$resource;
    } else {
        getAuthorizationCode();
    }

    function getAuthorizationCode() {
        global $authorize_url, $client_id, $callback_uri;

        $authorization_redirect_url = $authorize_url . "?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $callback_uri . "&scope=user.metrics&state=test";

        header("Location: " . $authorization_redirect_url);
    }

    function getAccessToken($authorization_code) {
        global $token_url, $client_id, $client_secret, $callback_uri;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $token_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([ 
            'action' => 'requesttoken',
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $authorization_code,
            'redirect_uri' => $callback_uri
        ]));

        $rsp = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($rsp);
        return $response->body->access_token;
    }

    function getWeight($access_token) {
        global $api_url;


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$access_token
        ]);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([ 
            'action' => 'getmeas',
            'meastype' => '1',
            'category' => '1',
        ]));
        
        $rsp = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($rsp);
        return $response->body->measuregrps[0]->measures[0]->value;
    }

?>