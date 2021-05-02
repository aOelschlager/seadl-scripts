/*
    Script to display all file nanes from the second hard drive of the cambodian manuscript project.
    It was used after the files names and files had been arranged for the prep batch script.
    2/9/2021
*/

<?php
    $di = new RecursiveDirectoryIterator('D:\Transliteration of Manuscripts');
    foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
        print $filename . "\n";
    }
?>