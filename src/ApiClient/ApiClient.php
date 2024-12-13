<?php
namespace NakalaImporter\ApiClient;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Json\Json;

class ApiClient
{
    const API_BASE_URL = 'https://api.nakala.fr';
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $endpoint Endpoint to call
     * @param array $data Datas sent in request (only on POST).
     * @param string $method HTTP Method ('GET' or 'POST').
     * @return array|false Response datas array or false if error.
     */
    public function sendRequest($endpoint, $method, array $data = [])
    {
        $client = new Client();
        $url = self::API_BASE_URL . $endpoint;

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $client->setUri($url);
        $client->setMethod($method);

        $client->setHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);

        if ($method === 'POST' && !empty($data)) {
            $client->setRawBody(Json::encode($data));
        }

        try {
            $response = $client->send();
            if ($response->getStatusCode() === 200) {
                return Json::decode($response->getBody(), Json::TYPE_ARRAY);
            } else {
                $messageError = $response->getContent();
                return ['http_error' => $messageError];
            }
        } catch (\Exception $e) {
            return "Nakala API error: $e->getMessage()";
        }
    }
}
