<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic OWF2dEdjNWJOdmNpbndzckV6R0VhM3lOOUc3N1Jtc0Y6TlIwczU2RUppYzFQR2RidA==",
    "Cache-Control: no-cache",
    "Postman-Token: 3c6774c7-14a4-40ee-b225-a3ae85eac1d7"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

?>