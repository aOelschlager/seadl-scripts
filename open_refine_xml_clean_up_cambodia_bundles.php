/*
   Script to clean up text file from open refine. 
   This was used with the first Cambodian manuscripts hard drive.
   9/11/2020
*/

<?php

    $title_array = array();    
    $csv = array_map('str_getcsv', file('title_names.csv'));
    foreach ($csv as $row) {
      array_push($title_array, $row[0]);
    }

    print "type a file path:  ";

    $dir = fgets(STDIN);
    $dir = trim($dir);

    if (!is_dir($dir)) {
        print "The directory $dir does not exist.\n";
        print "Exiting program.\n";

    } else {

        dirToArray($dir, $title_array);
    }
        
    function dirToArray($dir, &$title_array) {
        
      $cdir = scandir($dir);

      foreach ($cdir as $key => $value) {

         if (!in_array($value,array(".",".."))) {

            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

               dirToArray($dir . DIRECTORY_SEPARATOR . $value, $title_array);

            } else {
               $val_exten =  substr(strrchr($value, '.'), 1);
               $val_exten = strtolower($val_exten);

               if ($val_exten === "txt") {
                   
                   write_file($value, $title_array);
               }
            }
         }
      }
   }

   function write_file($value, &$title_array) {

      $delim = "*";
      $lines = file($value);
      $arr_count = 0;

      $file_name = pathinfo($value, PATHINFO_FILENAME);
      $new_file =  $file_name . "_" . $title_array[$arr_count] . ".xml";

      $file = fopen($new_file, 'w');

      foreach($lines as $line) {

      	 $next_line = next($lines);

         if (trim($delim) === trim($line)) {
            $arr_count += 1;
            fclose($file);
            
            clean_xml_null($new_file);

            if(trim($next_line) != '') {
            	$file_name = pathinfo($value, PATHINFO_FILENAME);
            	$new_file =  $file_name . "_" . $title_array[$arr_count] . ".xml";
            	$file = fopen($new_file, 'w');
            }

         } else {
            $line = preg_replace('~(?<=>)(")|(")(?=<)~', '', $line);
            fwrite($file, $line);
         }
      }
   }
   
   function clean_xml_null($new_file) {
      
      $xml = simplexml_load_file($new_file, null, LIBXML_NOBLANKS);
      
      $remove = $xml->xpath("//mods:titleInfo[mods:title='null']");
      
      foreach ( $remove as $item ) {
      	unset($item[0]);
      }

      if ($remove2 = $xml->xpath("//mods:name[mods:namePart='null']")) {
      	$remove3 = $xml->xpath("//mods:name[@type='personal']");
      	foreach ( $remove3 as $item ) {
          unset($item[0]);
      	}
      }

      $remove4 = $xml->xpath("///mods:dateCreated[text()='null']");
      
      foreach ( $remove4 as $item ) {
      	unset($item[0][0]);
      }

      $remove5 = $xml->xpath("///mods:form[text()='null']");
      
      foreach ( $remove5 as $item ) {
      	unset($item[0][0]);
      }

      $remove6 = $xml->xpath("//mods:identifier[text()='null']");

      foreach ( $remove6 as $item ) {
         unset($item[0]);
      }

      $remove7 = $xml->xpath("//mods:subject[mods:geographic='null']");
      
      foreach ( $remove7 as $item ) {
      	unset($item[0]);
      }

      $domDocument = dom_import_simplexml($xml)->ownerDocument;
      $domDocument->formatOutput = true;
      $domDocument->save($new_file);

      $config = array(
        'indent' => true,
        'indent-spaces' => 16,
        'indent-with-tabs' => true,
        'tab-size' => 16,
        'clean' => true,
        'input-xml'  => true,
        'output-xml' => true,
        'wrap'       => false
      );

      $tidy = new Tidy();

      $repaired = $tidy->repairfile($new_file, $config);

      file_put_contents($new_file, $repaired);
   }
?>
