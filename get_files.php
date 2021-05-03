/*
    Script to download jp2 files from a collection.
    4/21/2021
*/

<?php

$csv = array_map('str_getcsv', file('file_urls4.csv'));
foreach ($csv as $row) {
	$pid = $row[0]; //unique id of object
	$file_name = $row[1]; //title of object
    	$url = $row[2]; //url for download

    	if(file_put_contents($file_name, file_get_contents($url))) {
        	echo "$file_name downloaded successfully";
    	}
    	else {
        	echo "$file_name downloading failed.";
    	}
}

?>
