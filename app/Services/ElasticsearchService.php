<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $host = config('elasticsearch.host');

        // Parse URL: if it's a full URL like http://localhost:9200, extract host:port
        $parsedHost = $host;
        if (filter_var($host, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($host);
            $parsedHost = $parsed['host'];
            if (isset($parsed['port'])) {
                $parsedHost .= ':' . $parsed['port'];
            } else {
                $parsedHost .= ':9200';
            }
        }

        $this->client = ClientBuilder::create()
            ->setHosts([$parsedHost])
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

    public function ping(): bool
    {
        try {
            $response = $this->client->ping();

            return $response->asBool();
        } catch (\Exception $e) {
            \Log::error('Elasticsearch ping failed', [
                'message' => $e->getMessage(),
                'host' => config('elasticsearch.host'),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    public function indices()
    {
        return $this->client->indices();
    }

}
