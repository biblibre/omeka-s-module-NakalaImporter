<?php

namespace NakalaImporter\Job;

use Omeka\Job\AbstractJob;

class ImportJob extends AbstractJob
{
    const MIGRATION_IDENTIFIER = 'MIG_NAKALA';
    const MIGRATION_TITLE = 'Migration – Nakala';
    protected $propertiesMap;
    protected $itemSetsMap;
    protected $api;
    protected $logger;
    protected $apiClient;
    protected $itemSetsImported = [];

    public function perform()
    {
        $services = $this->getServiceLocator();
        $this->logger = $services->get('Omeka\Logger');
        $this->api = $services->get('Omeka\ApiManager');
        $this->apiClient = $services->get('NakalaImporter\ApiClient');

        $this->propertiesMap = [];
        $properties = $this->api->search('properties')->getContent();
        foreach ($properties as $property) {
            $this->propertiesMap[$property->term()] = $property->id();
        }

        $setsToImport = $this->getArg('selectedSets');
        if (!isset($setsToImport)) {
            throw new \Exception('None set selected');
        }

        if ($this->shouldStop()) {
            return;
        }

        $migrationItemSet = $this->findOrNewItemSet(self::MIGRATION_IDENTIFIER, self::MIGRATION_TITLE);

        foreach ($setsToImport as $identifier => $title) {
            $this->logger->info(sprintf("Import of '%s'", $title));
            $setItemSet = $this->findOrNewItemSet($identifier, $title);
            $this->importRecords($identifier, $setItemSet, $migrationItemSet);
        }

        $itemSetsImported = $this->getItemSetsImported();
        $nakalaImportJson = [
            'o:job' => ['o:id' => $this->job->getId()],
            'collections_imported' => json_encode($itemSetsImported),
        ];

        $this->api->create('nakala_importer_import', $nakalaImportJson);
    }

    protected function importRecords(string $identifier, $setItemSet, $migrationItemSet)
    {
        $currentPage = 1;
        $endpoint = "/collections/$identifier/datas";
        do {
            $data = $this->apiClient->sendRequest($endpoint, 'GET', ['page' => $currentPage]);
            if (empty($data['data'])) {
                $this->logger->info(sprintf('None data into this collection, item set "%s" will be deleted', $setItemSet->id()));
                $this->api->delete('item_sets', $setItemSet->id(), [], []);
            } else {
                $this->logger->info($setItemSet->id());
                foreach ($data['data'] as $record) {
                    $item = $this->getItemByIdentifier($record['identifier']);
                    if ($item) {
                        $this->logger->info(sprintf('Item with identifier %s already exists (%s)', $record['identifier'], $item[0]->id()));
                        continue;
                    } else {
                        $itemData = [];
                        $itemData['o:item_set'][] = ['o:id' => $migrationItemSet->id()];
                        $itemData['o:item_set'][] = ['o:id' => $setItemSet->id()];
                        $itemData['dcterms:identifier'][] = [
                        'property_id' => $this->propertiesMap['dcterms:identifier'],
                        'type' => 'literal',
                        'is_public' => '1',
                        '@value' => $record['identifier'],
                    ];
                        $itemData = $this->addMetadatas($record['metas'], $itemData);
                        $item = $this->api->create('items', $itemData)->getContent();
                        $this->logger->info('Created item ' . $item->id());

                        $this->addMedias($record, $item->id());
                        $this->logger->info('Append medias to item ' . $item->id());
                    }
                }
                $this->addToItemSetsImported(['id' => $setItemSet->id(), 'title' => $setItemSet->displayTitle(), 'identifier' => $identifier]);
            }
            $currentPage++;
        } while (isset($data['lastPage']) && $currentPage < $data['lastPage']);
    }

    protected function findOrNewItemSet($identifier, $title)
    {
        $data = [
            'property' => [
                [
                    'property' => 'dcterms:identifier',
                    'text' => $identifier,
                    'type' => 'eq',
                ],
            ],
        ];
        $itemSet = $this->api->search('item_sets', $data, ['limit' => 1])->getContent();
        if (!empty($itemSet)) {
            return $itemSet[0];
        } else {
            $newItemSet = $this->api->create('item_sets', [
                'dcterms:title' => [
                    [
                        'property_id' => $this->propertiesMap['dcterms:title'],
                        'type' => 'literal',
                        'is_public' => '1',
                        '@value' => $title,
                    ],
                ],
                'dcterms:identifier' => [
                    [
                        'property_id' => $this->propertiesMap['dcterms:identifier'],
                        'type' => 'literal',
                        'is_public' => '1',
                        '@value' => $identifier,
                    ],
                ],
            ]);
            return $newItemSet->getContent();
        }
    }

    protected function addMetadatas(array $metas, array $itemData)
    {
        foreach ($metas as $meta) {
            $value = $meta['value'];
            if (isset($meta['propertyUri'])) {
                if (str_starts_with($meta['propertyUri'], 'http://purl.org/dc/terms/')) {
                    $targetProperty = end(explode('/', $meta['propertyUri']));
                } elseif (str_starts_with($meta['propertyUri'], 'http://nakala.fr/terms#')) {
                    $targetProperty = end(explode('#', $meta['propertyUri']));
                }
                if (isset($targetProperty)) {
                    if ($targetProperty == 'creator' && isset($value['fullName'])) {
                        $value = $value['fullName'];
                    }
                    $omekaProperty = "dcterms:$targetProperty";
                    $itemData[$omekaProperty][] = [
                        'property_id' => $this->propertiesMap[$omekaProperty],
                        'type' => 'literal',
                        'is_public' => '1',
                        '@value' => $value,
                    ];
                }
            }
        }
        return $itemData;
    }
    protected function getItemByIdentifier(string $identifier)
    {
        $data = [
            'property' => [
                [
                    'property' => 'dcterms:identifier',
                    'text' => $identifier,
                    'type' => 'eq',
                ],
            ],
        ];
        $item = $this->api->search('items', $data, ['limit' => 1])->getContent();
        return $item;
    }

    protected function addMedias(array $record, $itemId)
    {
        $newItemData = [];

        $identifier = $record['identifier'];
        $files = $record['files'];
        if (empty($files)) {
            return;
        }
        foreach ($files as $file) {
            $fileTitle = $file['name'];
            $fileIdentifier = $file['sha1'];
            $iiifJSON = "https://api.nakala.fr/iiif/$identifier/$fileIdentifier/info.json";
            $newItemData['o:media'][] = [
                'o:ingester' => 'iiif',
                'o:source' => $iiifJSON,
                'dcterms:title' => [
                    [
                        'property_id' => $this->propertiesMap['dcterms:title'],
                        'type' => 'literal',
                        'is_public' => '1',
                        '@value' => $fileTitle,
                    ],
                ],
            ];
        }
        try {
            $this->api->update('items', $itemId, $newItemData, [], ['collectionAction' => 'append', 'isPartial' => true]);
        } catch (\Exception $e) {
            $this->logger->warn(($e));
        }
    }

    protected function getItemSetsImported()
    {
        return $this->itemSetsImported;
    }

    protected function addToItemSetsImported($itemSet)
    {
        $this->itemSetsImported[] = $itemSet;
        return $this->itemSetsImported;
    }
}
