#!/usr/bin/env drush

/*
	Script to add video model to video objects.
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
        if (!$object) {
                drupal_set_message("Fedora Object isn't in the repo!");
        } else {
                $object->relationships->add('info:fedora/fedora-system:def/model#', 'hasModel','islandora:sp_videoCModel');
        }
}
?>
