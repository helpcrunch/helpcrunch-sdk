<?php

namespace Helpcrunch\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Helpcrunch\Service\TokenAuthService\InternalAppAuthService;
use Psr\Http\Message\ResponseInterface;

abstract class RequestService
{
    const DEFAULT_DOMAIN = 'api';
    const ENDPOINTS_PREFIX = '/api';

    const DEFAULT_IP_PROTOCOL = 'v4';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected static $endpointsPrefix = self::ENDPOINTS_PREFIX;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $schema, string $domain, InternalAppAuthService $internalAppAuthService)
    {
        $this->key = $internalAppAuthService->getInternalAppToken();
        $this->schema = $schema;
        $this->domain = $domain;

        $this->client = new Client([
            'curl' => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false],
            'verify' => false,
            'headers' => $this->getHeaders(),
        ]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $domain
     * @param string $endpoint
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $domain, string $endpoint, array $options = [])
    {
        return $this->makeRequest('get', $domain, $endpoint, $options);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('post', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('put', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('patch', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('delete', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $method
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest(
        string $method,
        string $organizationDomain,
        string $endpoint,
        array $options = []
    ): ResponseInterface {
        $response = $this->client->request(
            $method,
            $this->getUrl($organizationDomain, $endpoint),
            $this->getOptions($options)
        );

        return $response;
    }

    protected function getUrl(string $organizationDomain, string $endpoint): string
    {
        return $this->schema . $organizationDomain . '.' . $this->domain . static::$endpointsPrefix . $endpoint;
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer helpcrunch-service="' . $this->key . '"',
            'Content-Type' => 'application/json'
        ];
    }

    protected function getOptions(array $options): array
    {
        return [
            RequestOptions::FORCE_IP_RESOLVE => self::DEFAULT_IP_PROTOCOL,
        ] + $options;
    }
}
