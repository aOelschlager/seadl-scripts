/*
	Script to rename the cambodian files and folders. This arranges them for use with the prep batch script.
   	2/9/2021
*/

<?php

	$dir = 'D:\Transliteration of Manuscripts';
	$dir_xml = 'D:\bundles_mods';

	$bn_folder = $dir . DIRECTORY_SEPARATOR . "cambodian_covers";

	mkdir($bn_folder, 0777);

	$cdir = scandir($dir);
    $bn_name = "";
	$bn_title = "";
	
	$print_array = array();

	$write_file_name = $dir . DIRECTORY_SEPARATOR . $directory_name . "_batch_report.txt";

	foreach ($cdir as $key => $value) {            
		if (!in_array($value,array(".",".."))) {              
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
				$dir_name = $dir . DIRECTORY_SEPARATOR . $value;
				$temp_name = explode("-", $value);
				$bn_temp = trim($temp_name[0]);
				$b_t_arr = explode(".", $bn_temp);
				$bn_name = "BN_" . $b_t_arr[2];

				$size = sizeof($temp_name);
                if ($size <= 2) {
					if ($size > 1) {
							$bn_title = trim($temp_name[0]);
							$bn_name = $bn_name . "-MISSING";
						}
                } else {
					$bn_title = trim($temp_name[2]);
                }
				$current_dir_name = $dir . DIRECTORY_SEPARATOR . $bn_name;
				rename($dir_name, $current_dir_name);
				$sdir = scandir($current_dir_name);
				foreach ($sdir as $key2 => $value2) {
					if (!in_array($value2,array(".",".."))) {
						if (is_dir($current_dir_name . DIRECTORY_SEPARATOR . $value2)) {
							$sub_dir = $current_dir_name . DIRECTORY_SEPARATOR . $value2;
							
							if ($size <= 2) {
								print "The title name should stay the same.\n";
								$bn_title = $value2;
								$new_sub_dir = $sub_dir;
							} else{
								$new_sub_dir = $current_dir_name . DIRECTORY_SEPARATOR . $bn_title;
								rename($sub_dir, $new_sub_dir);
							}
							$sdir2 = scandir($new_sub_dir);
							foreach ($sdir2 as $key3 => $value3) {
								if (!in_array($value3,array(".",".."))) {
									if (is_dir($new_sub_dir . DIRECTORY_SEPARATOR . $value3)) {
										$sub_dir2 = $new_sub_dir . DIRECTORY_SEPARATOR . $value3;
										$temp_name_2 = explode("_", $value3);
										$size2 = sizeof($temp_name_2);
										$bn_title_temp = "-bundle_" . $value3;
										if ($size2 <= 2) {
											$new_sub_dir2 = $new_sub_dir . DIRECTORY_SEPARATOR . $bn_title . "_bundle_" . $value3;
											rename($sub_dir2, $new_sub_dir2);
											$bn_xml = $bn_name . ".xml";
											$bn_title_xml = $bn_name . $bn_title_temp . ".xml";
											print "BN XML: " . $bn_xml . "\n";
											print "BN Bundle XML: " . $bn_title_xml . "\n";
											$filename = $dir_xml . DIRECTORY_SEPARATOR . $bn_xml;
											$filename2 = $dir_xml . DIRECTORY_SEPARATOR . $bn_title_xml;
											if (file_exists($filename)) {
												print "The file $filename exists\n";
												$mods_location = $new_sub_dir2 . DIRECTORY_SEPARATOR . $bn_xml;
												rename($filename, $mods_location);
											} elseif (file_exists($filename2)) {
												print "The file $filename2 exists\n";
												$mods_location = $new_sub_dir2 . DIRECTORY_SEPARATOR . $bn_title_xml;
												rename($filename2, $mods_location);
											} else {
												print "Neither File Exists\n";
												array_push($print_array, $filename, $filename2, "Neither File Exists" );
											}
										} else {
											print "The subdirectory has already been changed.\n";
											$bn_xml = $bn_name . ".xml";
											$temp_bn_t_arr = explode("_", $value3);
											$size4 = sizeof($temp_bn_t_arr);
											print "Size: " . $size4 . "\n";
											if ($size4 > 1){
												$bn_title_xml = $bn_name . "-bundle_" . $temp_bn_t_arr[2] . ".xml";
											} else {
												$bn_title_xml = $bn_name . $bn_title_temp . ".xml";
											}
											print "BN XML: " . $bn_xml . "\n";
											print "BN Bundle XML: " . $bn_title_xml . "\n";
											$filename = $dir_xml . DIRECTORY_SEPARATOR . $bn_xml;
											$filename2 = $dir_xml . DIRECTORY_SEPARATOR . $bn_title_xml;
											if (file_exists($filename)) {
												print "The file $filename exists\n";
												$mods_location = $sub_dir2 . DIRECTORY_SEPARATOR . $bn_xml;
												rename($filename, $mods_location);
											} elseif (file_exists($filename2)) {
												print "The file $filename2 exists\n";
												$mods_location = $sub_dir2 . DIRECTORY_SEPARATOR . $bn_title_xml;
												rename($filename2, $mods_location);
											} else {
												print "Neither File Exists\n";
												array_push($print_array, $filename, $filename2, "Neither File Exists" );
											}
										}
									} else {
										$val_exten =  substr(strrchr($value3, '.'), 1);
										$val_exten = strtolower($val_exten);
										if ($val_exten === "tif" | $val_exten === "tiff") {
											$file_path = $new_sub_dir . DIRECTORY_SEPARATOR . $value3;
                                            $file_name_change = $bn_folder . DIRECTORY_SEPARATOR . $bn_name . "_cover.tif";
                                            print "Cover Title: " . $file_name_change . "\n";
                                			rename($file_path, $file_name_change);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$fp = fopen($write_file_name, 'w');
    fwrite($fp, print_r($print_array, TRUE));
    fclose($fp)
?>