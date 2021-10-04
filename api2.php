<?php

$server = "localhost";
$user = "root";
$password = "";
$nama_database = "db_cekresi";

$db = mysqli_connect($server, $user, $password, $nama_database);
mysqli_set_charset($db, "utf8");
if (!$db) {
	die("Gagal terhubung dengan database: " . mysqli_connect_error());
}



$asu = mysqli_query($db, "SELECT resi FROM tbl_resi WHERE status = ''");
while ($d = mysqli_fetch_array($asu)) {

	// $postdata = http_build_query(
	// 	array(
	// 		'method' => 'order.massOrderTrack',
	// 		'format' => 'json',
	// 		'v' => '1.0',
	// 		'data' => '{"parameter":"{\"billCodes\":\"' . $d['resi'] . '\",\"lang\":\"en\"}"}'
	// 	)
	// );

	$opts = array(
		'http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-Type: application/x-www-form-urlencoded',
			// 'content' => $postdata
		)
	);

	$context  = stream_context_create($opts);
	$result = file_get_contents('http://ayiip.com/tracking/jnt.php?resi='.$d['resi'], FALSE, $context);

	$json = json_decode($result, TRUE);
	
	if ($json['pesan'] == 'data ada') {
		$n = count($json['history']) - 1;
		$hasil = $json['history'][$n]['keterangan'];
		$myArray = explode(',', $hasil);
		mysqli_query($db, "UPDATE tbl_resi SET status= '$myArray[0]' WHERE resi = " . $d['resi']);
	} else {
		mysqli_query($db, "UPDATE tbl_resi SET status= 'belum ada' WHERE resi = " . $d['resi']);
	}
}
