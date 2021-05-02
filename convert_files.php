/*
	Converts downloaded jp2 files into tifs using imageMagick.
    These were files that were not diplaying on the website.
   	4/21/2021
*/

<?php

$csv = array_map('str_getcsv', file('file_urls4.csv'));
foreach ($csv as $row) {
	$pid = $row[0];
	$file_name = $row[1];
    $url = $row[2];

    $new_file_name = $file_name . ".tiff";

    $jp2_img = $file_name . " " . $new_file_name;

    print "Converting: " . $file_name . "\n";
    exec("convert $jp2_img");

}

?>