<?php
namespace NakalaImporter\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class ImportRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLd()
    {
        return [
            'o:job' => $this->job()->getReference(),
            'collections_identifiers' => $this->collectionsImported(),
        ];
    }

    public function getJsonLdType()
    {
        return 'o:NakalaImporterImport';
    }

    public function job()
    {
        return $this->getAdapter('jobs')
            ->getRepresentation($this->resource->getJob());
    }

    public function collectionsImported()
    {
        return $this->resource->getCollectionsImported();
    }
}
