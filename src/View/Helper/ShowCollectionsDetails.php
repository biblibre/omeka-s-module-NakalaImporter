<?php
namespace NakalaImporter\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ShowCollectionsDetails extends AbstractHelper
{
    public function __invoke($collectionsJSON)
    {
        $collections = json_decode($collectionsJSON);
        $omekaItemSets = [];
        foreach ($collections as $collection) {
            $id = $collection->id;
            $omekaItemSet = $this->getView()->api()->search('item_sets', ['id' => $id])->getContent();
            if ($omekaItemSet) {
                $omekaItemSets[$id] = $omekaItemSet[0];
            }
        }
        return $this->getView()->partial(
            'nakala-importer/partials/show-collection-details',
            [
                'collections' => $collections,
                'omekaItemSets' => $omekaItemSets,
            ]
        );
    }
}
