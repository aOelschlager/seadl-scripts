/*
   Script to check tif files for errors.
   2/2/2021
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
        
        // for each value check if it is a directory, tif file. Then
        // checks tif file for errors.
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
                    }     
                }
            }
        }
        return $print_array;
    }
?>
