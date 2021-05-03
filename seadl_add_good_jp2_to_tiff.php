#!/usr/bin/env drush

/*
	Script to add the converted tifs back into the object's obj datastream.
   	4/15/2021
*/

<?php

$tuquePath = libraries_get_path('tuque') . '/*.php'; foreach ( glob($tuquePath) as $filename) {
    require_once($filename);
}

$serializer = new FedoraApiSerializer();
$cache = new SimpleCache();
$connection = new RepositoryConnection('fedora_url', 'user', 'password');
$api = new FedoraApi($connection, $serializer);
$repository = new FedoraRepository($api, $cache);

$csv = array_map('str_getcsv', file('/file/path/tiff_pids.csv'));
foreach ($csv as $row) {
	$id = $row[0];
	$file_name = $row[1];
	$file_path = '/file/path/on/server/';
	$file = $file_path . $file_name . ".tiff";
	print "Working on PID: " . $id . "\n";
	$object = $repository->getObject($id);
	$dsid = 'OBJ';
    	$datastream = isset($object[$dsid]) ? $object[$dsid] : $object->constructDatastream($dsid);
    	$datastream->label = 'OBJ Datastream';
    	$datastream->mimeType = 'image/tiff';
	
    	$datastream->setContentFromFile($file);
    	$object->ingestDatastream($datastream);

	print "Finished PID: " . $id . "\n";
}
?>
