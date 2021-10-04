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

	$postdata = http_build_query(
		array(
			'method' => 'order.massOrderTrack',
			'format' => 'json',
			'v' => '1.0',
			'data' => '{"parameter":"{\"billCodes\":\"' . $d['resi'] . '\",\"lang\":\"en\"}"}'
		)
	);

	$opts = array(
		'http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-Type: application/x-www-form-urlencoded',
			'content' => $postdata
		)
	);

	$context  = stream_context_create($opts);
	$result = file_get_contents('http://jk.jet.co.id:22234/jandt-app-ifd-web/router.do', FALSE, $context);

	$json = json_decode($result, TRUE);
	$x = json_decode($json['data'], TRUE);

	$cek = $x['bills'][0]['details'];
	if ($cek != NULL) {
		$hasil = $x['bills'][0]['details'][0]['scanstatus'];
		mysqli_query($db, "UPDATE tbl_resi SET status= '$hasil' WHERE resi = " . $d['resi']);
	} else {
		mysqli_query($db, "UPDATE tbl_resi SET status= 'belum ada' WHERE resi = " . $d['resi']);
	}
}
