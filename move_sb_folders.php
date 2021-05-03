/*
	Script to move book models folders that would not have a compound model parent into their own directory. 
   	3/16/2021
*/

<?php
$dir = "D:/single_books_ex_hd_2";
$cdir = scandir($dir);
foreach ($cdir as $key => $value) {
        if (!in_array($value,array(".",".."))) {
            	if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                	$dir2 = $dir . DIRECTORY_SEPARATOR . $value;
                	$cdir2 = scandir($dir2);
                	foreach ($cdir2 as $key2 => $value2) {
                    		if (!in_array($value2,array(".",".."))) {
                        		if (is_dir($dir2 . DIRECTORY_SEPARATOR . $value2)) {
                            		$dir3 = $dir2 . DIRECTORY_SEPARATOR . $value2;
                            		print $dir3 . "\n";
                        		}
                    		}
                	}
            	}
        }
}               
?>
