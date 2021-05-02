#!/usr/bin/env drush

/*
	Script to delete jp2s from the book model. Only need jp2 for pages.
   	1/7/2021
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
$count = 1;

$csv = array_map('str_getcsv', file('/file/path/seadl_book_models_3.csv'));
foreach ($csv as $row) {
	$id = $row[0];

	$object = $repository->getObject($id);
	if (!$object) {
		drupal_set_message("Fedora Object isn't in the repo!");
	} else {
		$models = $object->models;
		$bookcm = $models[0];
		if ($bookcm == 'islandora:bookCModel') {
			print "Using model " . $bookcm . "\n";
			foreach ($object as $datastream) {
				if ($datastream->id == 'JP2') {
					print $count . "\n";
					$count += 1;
					print "PID: " . $id;
					print "\nModel: " . $models[0];
					print "\nDSID: " . $datastream->id;
					print "\nLabel: " . $datastream->label;
					print "\nMimetype: " . $datastream->mimetype;
					print "\n\n";
					print "Deleting " . $id . " JP2 file\n\n";
					$dsid = $datastream->id;
					$object->purgeDatastream($dsid);
				}
			}
		}
	}
}
?>
