<?php
namespace NakalaImporter\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use DateTime;

class ShowSetDetails extends AbstractHelper
{
    public function __invoke($set)
    {
        $title = $set['metas'][0]['value'];
        $creationDate = new DateTime($set['creDate']);
        $formattedDate = $creationDate->format('Y-m-d');

        return $this->getView()->partial(
            'nakala-importer/partials/show-set-details',
            [
                'identifier' => $set['identifier'],
                'status' => $set['status'],
                'title' => $title,
                'uri' => $set['uri'],
                'creation_date' => $formattedDate,
            ]
        );
    }
}
