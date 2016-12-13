<?php

namespace Imamuseum\Harvester2\Contracts;

/**
 * Interface HarvesterInterface
 * @package Imamuseum\Harvester2
 */
interface HarvesterInterface
{
    public function getAllIDs($source);
    public function getUpdateIDs($source);
    public function getObject($uid, $source);
    public function initialOrUpdateObject($source, $uids);
    public function initialOrUpdateRelations($source, $uids);

    // Part of the HavesterAbstract
    public function createTypes();
    public function deleteOldObjects($source);
    public function createOrUpdateObjects($source, $raw_objects_results);
    public function createOrUpdateTerms($source, $raw_objects_results);
    public function createOrUpdateActors($source, $raw_objects_results);
    public function createOrUpdateTexts($source, $raw_objects_results);
    public function createOrUpdateLocations($source, $raw_objects_results);
    public function createOrUpdateDates($source, $raw_objects_results);
    public function createOrUpdateAssets($source, $raw_objects_results);

    // public function createTypes();
    // public function createOrFindTerms($fields);
    // public function createOrFindDates($fields);
    // public function createOrFindLocations($fields);
    // public function createOrUpdateTexts($object_id, $texts);
    // public function createOrUpdateAssetSource($object_id, $images);
    // public function createOrUpdateActors($actors);
}
