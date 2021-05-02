#!/usr/bin/env drush

/*
	Script to add thumbnails to compound object parents.
   	5/1/2021
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

//get csv of parents and loop
$csv = array_map('str_getcsv', file('/file/path/parent_pids_thumbs.csv'));
foreach ($csv as $row) {
	$id = $row[0];

	$object = $repository->getObject($id);
	if (!$object) {
		drupal_set_message("Fedora Object isn't in the repo!");
	} else {

		if (!$object['TN']) {
			print "Thumbnail made for $id \n";
			islandora_compound_object_update_parent_thumbnail($object);
		}else {
			print "$id skipped \n";
        	}
	}
}

?>
