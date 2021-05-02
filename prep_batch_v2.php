/*
   Script to prep batches for ingest. I've settled on this version being my goto.
   4/17/2020
*/

<?php

    // gets file path from user, trims new line, and tests
    // if directory exist
    echo "type a file path: ";
    
    $dir = fgets(STDIN);
    $dir = trim($dir);
    
    if (!is_dir($dir)) {
        echo "The directory $dir does not exist\n";
    } else {
        #$dir = "/mnt/c/Users/oelsc/Desktop/EAP698_ThanhChau";
        
        // makes array to hold the error information from function
        // that gets written to a file. Creates variables for file
        // and directory names.
        $print_array = array();
        $end_array = array();
        $current_dir_name = " ";

        $dir_parts = explode("/", $dir);
        $directory_name = end($dir_parts);
        
        $other_files = $directory_name . "_not_batch_files";
        $other_files_folder = $dir . DIRECTORY_SEPARATOR . $other_files;
        $top_dir_name = $dir;
        
        if (!mkdir($other_files_folder, 0777)) {
            array_push($print_array, $directoryname, "Could not create folder" );
        } else {
            $top_dir_name = $other_files_folder . DIRECTORY_SEPARATOR . $directory_name;
        }
        
        print "\nWould you like to scan the whole directory? (y/n)";
        $answer = fgets(STDIN);
        $answer = trim($answer);
        $answer = strtolower($answer);
        if ($answer === "y" | $answer === "yes") {
            $end_array = dirToArray($print_array, $dir, $other_files_folder, $current_dir_name, $top_dir_name, $directory_name);
        }
        
        $write_file_name = $dir . DIRECTORY_SEPARATOR . $directory_name . "_batch_report.txt";
        $fp = fopen($write_file_name, 'w');
        fwrite($fp, print_r($end_array, TRUE));
        fclose($fp);
    }

    /* Loops through directories and preps files. */
    function dirToArray(&$print_array, $dir, $other_files_folder, $current_dir_name, $top_dir_name, $directory_name) {
        $cdir = scandir($dir);
        $counter = 0;
        
        // for each value check if it is a directory, tif file, xmll file,
        // or anything other formate. If it is a directory call this fuction
        // again. If it is a tif, xml, or other file formate prep the files.
        foreach ($cdir as $key => $value) {
            
            if (!in_array($value,array(".",".."))) {
                
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $current_dir_name = $dir . DIRECTORY_SEPARATOR . $value;
                    $counter = 0;
                    dirToArray($print_array ,$dir . DIRECTORY_SEPARATOR . $value, $other_files_folder, $current_dir_name, $top_dir_name, $directory_name);
                } else {
                    $val_exten =  substr(strrchr($value, '.'), 1);
                    $val_exten = strtolower($val_exten);
                    if ($val_exten === "tif" | $val_exten === "tiff") {
                        $file_path = $dir . DIRECTORY_SEPARATOR . $value;
                        
                        if (!$exif = exif_read_data($file_path, "FILE,COMPUTED,ANY_TAG,IFD0,THUMBNAIL,COMMENT,EXIF", true)) {
                            array_push($print_array, $file_path, "File is corrupted" );
                        }
                        $counter += 1;
                        $directoryname = $dir . DIRECTORY_SEPARATOR . $counter;
                        
                        if (!mkdir($directoryname, 0777)) {
                            array_push($print_array, $directoryname, "Could not create folder" );
                        }
                        $file_path = $dir . DIRECTORY_SEPARATOR . $value;
                        $file_name_change = $dir . DIRECTORY_SEPARATOR . $counter . DIRECTORY_SEPARATOR . "OBJ.tif";
                        
                        if (!rename($file_path,$file_name_change)) {
                            array_push($print_array, $file_path, "Name change failed" );
                            array_push($print_array, $file_path, "Could not move file" );
                        }
                        
                    } elseif ($val_exten === "xml") {
                        $file_path = $dir . DIRECTORY_SEPARATOR . $value;
                        $file_name_change = $dir . DIRECTORY_SEPARATOR . "MODS.xml";
                        
                        if (!rename($file_path,$file_name_change)) {
                            array_push($print_array, $file_path, "Name change failed" );
                        }
                        
                    } else {
                        $file_path = $dir . DIRECTORY_SEPARATOR . $value;
                        $dir_parts = explode("/", $file_path);
                        end($dir_parts);
                        $dir_name = prev($dir_parts);
                        $temp_dir = prev($dir_parts);
                        print "dir_name:  " . $dir_name . "\n";
                        print "temp_dir:  " . $temp_dir . "\n";
                        if (strcmp($temp_dir, $directory_name) !==0) {
                            print $temp_dir . " is not the same as " . $directory_name . "\n\n";
                            $directoryname = $other_files_folder . DIRECTORY_SEPARATOR . $dir_name;

                            if (!is_dir($directoryname)) {

                                if(strcmp($directoryname, $current_dir_name) !== 0 & strcmp($directoryname, $top_dir_name) !== 0) {

                                    if (!mkdir($directoryname, 0777)) {
                                        array_push($print_array, $directoryname, "Could not create folder" );
                                    } else {
                                        $file_name_change = $directoryname . DIRECTORY_SEPARATOR . $value;

                                        if (!rename($file_path,$file_name_change)) {
                                            array_push($print_array, $file_path, "Could not move file" );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $print_array;
    }
?>
