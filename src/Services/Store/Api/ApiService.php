<?php

namespace EdcCommon\ResourceManager\Services\Store\Api;

use EdcCommon\ResourceManager\Models\Config;

class ApiService extends \EdcCommon\ResourceManager\Services\Store\Contracts\StoreServiceAbstract
{
    protected $apiConfig;
    protected $host;
    protected $secret;
    protected $path;

    protected $storeUrl;

    protected $client;

    /**
     * @param Config $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->apiConfig = $config->getApiStore();

        $this->host = $this->apiConfig['host'];
        $this->secret = $this->apiConfig['secret_token'];
        $this->path = $this->apiConfig['path'];

        $this->storeUrl = $this->host . '/' . $this->path;
    }

    protected function initClient()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secret,
            'Accept: application/json',
        ]);

        $this->client = $ch;

        return $ch;
    }

    /**
     * @inheritDoc
     */
    public function store($uploadResource)
    {
        $data = $uploadResource->toArray();
        $client = $this->initClient();
        $url = $this->storeUrl . '?static_domain=' . $this->config->get('static_domain');

        curl_setopt($client, CURLOPT_URL, $url);
        curl_setopt($client, CURLOPT_POST, 1);
        curl_setopt($client, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($client);

        curl_close ($client);

        return json_decode($response, true);
    }
}
