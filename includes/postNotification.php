<?php

function sendNotificationToIOS($apn,$deviceToken)

{

	$passphrase = '123456';

	$ctx = stream_context_create();

	stream_context_set_option($ctx, 'ssl', 'local_cert', '/admin/certificate/Certificate_APNS.pem');

	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);



	// Open a connection to the APNS server

	$fp = stream_socket_client(

	//'ssl://gateway.push.apple.com:2195', $err,

	'ssl://gateway.sandbox.push.apple.com:2195', $err,

	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);



	$body['aps']= $apn;



	$payload = json_encode($body);

	

	// Build the binary notification

	$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

	

	// Send it to the server

	$result = fwrite($fp, $msg, strlen($msg));

	

	/*if (!$result)

	echo 'Message not delivered' . PHP_EOL;

	else

	echo 'Message successfully delivered' . PHP_EOL;*/

	// Close the connection to the server

	fclose($fp);

	return;

	//header('Location: myschool2_apps.php?');

}



function sendNotificationToAndroid($apn,$deviceToken)
{
		
	$apiKey = "AAAAR8_Iiwk:APA91bEAll8Rdw3tA-3aNIKDhZC1k5ipBC86XLw-S9zmU6PBwOiU6bvJkCTMDJmaMDyTIIQV6R7zYmxoH-ajnGxyhYsSFPoROckgs8Xf7jceLK5H0dHThe0ey4WF6gX8oniojTUMsLab";
	$registrationIDs = $deviceToken;

	$url = 'https://fcm.googleapis.com/fcm/send';
	$fields = array
			(
				'to'		=> $deviceToken,
				'notification'	=> $apn
			);
	$headers = array
			(
				'Authorization: key=' . $apiKey,
				'Content-Type: application/json'
			);
	
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

?>