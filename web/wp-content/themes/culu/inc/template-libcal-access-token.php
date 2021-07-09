<?php

/**
 * Function partial for getting LibCal access token
 *
 * @package culu
 *
 *
 */

function get_libcal_token()
{

    $url = 'https://spaces.library.cornell.edu/1.1/oauth/token';
    $data = array(
        'client_id' => getenv("LIBCAL_CLIENT_ID"),
        'client_secret' => getenv("LIBCAL_CLIENT_SECRET"),
        'grant_type' => 'client_credentials'
    );

    $options = array(
        'http' => array( // use key 'http' even if you send the request to https://...
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if (!$result) die("$errstr ($errno)\n");

    $response = json_decode($result);

    return $response->access_token;
}
