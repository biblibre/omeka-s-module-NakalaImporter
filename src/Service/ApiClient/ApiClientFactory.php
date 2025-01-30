<?php
namespace NakalaImporter\Service\ApiClient;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Omeka\Service\Exception;
use NakalaImporter\ApiClient\ApiClient;

class ApiClientFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $settings = $serviceLocator->get('Omeka\Settings');
        $apiKey = $settings->get('nakalaimporter_api_key');
        if (!$apiKey) {
            throw new Exception\ConfigException('Missing Nakala API key in module config form');
        }
        $apiClient = new ApiClient($apiKey);

        return $apiClient;
    }
}
