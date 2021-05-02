#!/usr/bin/env drush

/*
	Script to delete the bad jp2 datastreams.
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
$count = 1;

$csv = array_map('str_getcsv', file('/file/path/jp2_pids.csv'));
foreach ($csv as $row) {
	$id = $row[0];
	$file_name = $row[1];

	$object = $repository->getObject($id);
	if (!$object) {
		drupal_set_message("Fedora Object isn't in the repo!");
	} else {
		foreach ($object as $datastream) {
			if ($datastream->id == 'JP2') {
				print $count . "\n";
				$count += 1;
				print "PID: " . $id;
				print "\nDSID: " . $datastream->id;
				print "\nLabel: " . $datastream->label;
				print "\nMimetype: " . $datastream->mimetype;
				print "\n\n";
				$dsid = $datastream->id;
				print "Deleting " . $id . " " . $dsid . " file\n\n";
				$object->purgeDatastream($dsid);
			}
		}	
	}
}
?>
