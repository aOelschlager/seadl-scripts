#!/usr/bin/env drush

/*
	Script to remove the video streaming model and datastream from object.
   	5/20/2020
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

$csv = array_map('str_getcsv', file('/file/path/test_csv.csv'));
foreach ($csv as $row) {
    	$id = $row[0];
    	$link = $row[1];

	$object = $repository->getObject($id);
	$object->relationships->remove('info:fedora/fedora-system:def/model#', 'hasModel','islandora:sp_streaming');

	$api_m = $repository->api->m;

	$dsid = 'STREAMING';

	$api_m-> purgeDatastream($id, $dsid);

}
?>

