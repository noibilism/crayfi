<?php

namespace Cray\Laravel\Http;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\ConnectionException;
use Cray\Laravel\Support\ResponseNormalizer;
use Cray\Laravel\Exceptions\CrayTimeoutException;

class CrayClient
{
    protected HttpFactory $http;
    protected ResponseNormalizer $normalizer;
    protected array $config;

    public function __construct(HttpFactory $http, ResponseNormalizer $normalizer, array $config)
    {
        $this->http = $http;
        $this->normalizer = $normalizer;
        $this->config = $config;
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('get', $endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('post', $endpoint, $data);
    }

    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->config['api_key'];
        $timeout = $this->config['timeout'] ?? 30;
        $retries = $this->config['retries'] ?? 2;

        try {
            $response = $this->http->baseUrl($baseUrl)
                ->withToken($apiKey)
                ->timeout($timeout)
                ->retry($retries, 100, null, false)
                ->asJson()
                ->acceptJson()
                ->$method($endpoint, $data);

            return $this->normalizer->normalize($response);

        } catch (ConnectionException $e) {
            throw new CrayTimeoutException("Request to Cray API timed out: {$e->getMessage()}", 0, $e);
        }
    }

    protected function getBaseUrl(): string
    {
        if (!empty($this->config['base_url'])) {
            return $this->config['base_url'];
        }

        $env = $this->config['env'] ?? 'sandbox';

        return match ($env) {
            'live' => 'https://pay.connectramp.com',
            default => 'https://dev-gateman.v3.connectramp.com',
        };
    }
}
