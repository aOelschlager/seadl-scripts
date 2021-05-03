#!/usr/bin/env drush

/*
	Script to add video streaming model and datastream to object.
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

$csv = array_map('str_getcsv', file('/file/path/test_vid_files.csv'));
foreach ($csv as $row) {
	$id = $row[0];
	$link = $row[1];

	$object = $repository->getObject($id);
	if (!$object) {
		drupal_set_message("Fedora Object isn't in the repo!");
	} else {
		$object->relationships->add('info:fedora/fedora-system:def/model#', 'hasModel','islandora:sp_streaming');

		$dsid = 'STREAMING';

        	$datastream = isset($object[$dsid]) ? $object[$dsid] : $object->constructDatastream($dsid);
        	$datastream->label = 'Streaming Info';
        	$datastream->mimeType = 'application/xml';

 		$dom = new DomDocument();
 		$sources = $dom->appendChild($dom->createElement('sources'));
		$source = $sources->appendChild($dom->createElement('source'));
		$url = $source->appendChild($dom->createElement('url'));
		$url->appendChild($dom->createTextNode($link));
		$mime = $source->appendChild($dom->createElement('mime'));
		$mime->appendChild($dom->createTextNode('video/mp4'));

		$dom->formatOutput = true;
		$xml_string = $dom->saveXML();

        	$datastream->setContentFromString($xml_string);

        	if (!isset($object['STREAMING'])) {
                	$object->ingestDatastream($datastream);
        	}
	}
}
?>
