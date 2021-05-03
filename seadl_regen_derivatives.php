#!/usr/bin/env drush

/*
	Script to regenerate derivates of an object. Ran after script to ingest fixed tif obj datastream.
   	4/15/2021
*/

<?php

$tuquePath = libraries_get_path('tuque') . '/*.php'; foreach ( glob($tuquePath) as $filename) {
    require_once($filename);
}

module_load_include('inc', 'islandora_paged_content', 'includes/derivatives');
module_load_include('inc', 'islandora_paged_content', 'includes/utilities');
module_load_include('inc', 'islandora_paged_content', 'includes/batch');

$serializer = new FedoraApiSerializer();
$cache = new SimpleCache();
$connection = new RepositoryConnection('fedora_url', 'user', 'password');
$api = new FedoraApi($connection, $serializer);
$repository = new FedoraRepository($api, $cache);

$csv = array_map('str_getcsv', file('/file/path/tiff_pids.csv'));
foreach ($csv as $row) {
	$id = $row[0];
    	$file_name = $row[1];
    	print "Working on PID: " . $id . "\n";
	$object = $repository->getObject($id);
	islandora_paged_content_page_derive_image_datastreams($object);
}

?>
