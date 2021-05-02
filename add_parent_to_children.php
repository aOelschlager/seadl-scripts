#!/usr/bin/env drush

/*
	Script to add the compound model parent to the book model child.
   	9/28/2020
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

$csv = array_map('str_getcsv', file('/file/path/test_parent_child_compound.csv'));
foreach ($csv as $row) {
        $id = $row[0];
        $parent_id = $row[1]; //ex. seadl:5646
        $sequenceNumber = $row[2]; // whatever the bundle number is

        print "working on PID: $id \n";
        $object = $repository->getObject($id);
        if (!$object) {
                drupal_set_message("Fedora Object isn't in the repo!");
        } else {
                $object->relationships->add('info:fedora/fedora-system:def/relations-external#', 'isConstituentOf', $parent_id);
                $escaped_pid = str_replace(':', '_', $parent_id);
                $object->relationships->add('http://islandora.ca/ontology/relsext#', "isSequenceNumberOf$escaped_pid", $sequenceNumber, TRUE); 
                print "Finished adding $id to $parent_id \n";
        }
}
?>