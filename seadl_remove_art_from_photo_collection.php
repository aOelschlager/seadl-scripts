#!/usr/bin/env drush

/*
	Script to remove relationship to objects.
   	5/12/2021
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

$csv = array_map('str_getcsv', file('/file/path/art_photo_pids.csv'));
foreach ($csv as $row) {
	$id = $row[0];
	print "Working on PID: " . $id . "\n";
	$object = $repository->getObject($id);
	$object->relationships->remove('info:fedora/fedora-system:def/relations-external#', 'isMemberOfCollection','SEAImages:VNArtImages');
	print "Finished PID: " . $id . "\n";
}
?>
