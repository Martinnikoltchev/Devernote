<?php
	$input = $_REQUEST["content"];
	$id = "101";
	$tempLoc = "temp/";
	$scriptPath = $tempLoc.$id.".py";
	file_put_contents($scriptPath, $input);
	exec("python ".$scriptPath, $output);
	$res = "";
	foreach($output as $line){
		$res.=$line."\n";
	}
	echo($res);
?>
