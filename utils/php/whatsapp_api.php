<?php

function sendWhatsAppMessage($phoneNumber, $message) {
    $password = "UAl6f2hDoDnRziDIUhDNiaBFbP3cTWb4eLWQr5xmliEJU8PBx2";
    $encodedMessage = urlencode($message);
    $endpointUrl = "https://api-itjbot.teamitj.tech/send?phoneNumber=91$phoneNumber&message=$encodedMessage&password=$password";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpointUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'status' => 'error',
            'message' => "cURL error: $error_msg"
        ];
    }

    curl_close($ch);

    return [
        'status' => 'success',
        'response' => $response
    ];
}
