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
        $config = $serviceLocator->get('Config');
        if (!isset($config['nakala-importer'])) {
            throw new Exception\ConfigException('Missing nakala-importer configuration');
        }
        if (!isset($config['nakala-importer']['api_key'])) {
            throw new Exception\ConfigException('Missing Nakala API key');
        }
        $apiKey = $config['nakala-importer']['api_key'];
        $apiClient = new ApiClient($apiKey);

        return $apiClient;
    }
}
