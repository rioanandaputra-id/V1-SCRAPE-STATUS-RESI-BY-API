<?php 

/*
just change the root link and rn the srcipt
*/

$root='http://localhost/v3polsektelukbetungselatan/assets/backend/img/'; // your link here
$fileRoot= get($root);
$links= getlinks($fileRoot);

aspire($root,$links);

// the recursive function that do the job 
function aspire($root,$links){

	foreach($links as $link){
		$newRoot=$root.$link;
		$fileRoot=get($newRoot);
		$newlinks=getlinks($fileRoot);
		aspire($newRoot,$newlinks);
	}

}

// function that gets all links in a html file -you may need to change the regEx-
function getlinks($fileRoot){
	if( strpos($fileRoot, '.html' ) !== false ){
		$content = file_get_contents($fileRoot);
		preg_match_all("/href=\"(.*?)\"/", $content , $links_array);
		//foreach($links_array[1] as $link){
		//	//if($link == '../')
		//	//	continue;
		//	echo '------> '.$link.PHP_EOL;
		//}
		array_shift($links_array[1]); // to delete '../' from links
		return $links_array[1];
	}
	return array();
}

// function that downloads files from index
function get($link){
	echo '**** wgeting '.$link.PHP_EOL;
	
	$file = trim($link,'/');
	$fileArr = explode('/',$file);
	$time=time();
	$file = end($fileArr);
	$file = $time.'_'.$file; // because we may find 2 different files with same names 
	exec("wget -q -O {$file} '{$link}'");
	$r = shell_exec("file '{$file}' | grep 'HTML document'"); 
	$res = $file;
	if( !empty($r) ){
		$res = "{$file}.html";
		system("mv {$file} {$res}");
	}
	return $res;
}

?>