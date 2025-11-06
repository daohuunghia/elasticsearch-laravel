<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('elasticsearch.host')])
            ->build();
    }

    public function client()
    {
        return $this->client;
    }

    public function index(array $params): mixed
    {
        return $this->client->index($params);
    }

    public function search(array $params): mixed
    {
        return $this->client->search($params);
    }
}
