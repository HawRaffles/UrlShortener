<?php

namespace Study\UrlCompressor\Helpers;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Study\UrlCompressor\Interfaces\ICheckUrl;

class CheckUrl implements ICheckUrl
{
    protected ClientInterface $httpClient;
    protected int $timeout;
    protected array $statusCodes;
    protected string $userAgent;

    public function __construct(ClientInterface $httpClient, int $timeout, array $statusCodes, string $userAgent)
    {
        $this->httpClient = $httpClient;
        $this->timeout = $timeout;
        $this->statusCodes = $statusCodes;
        $this->userAgent = $userAgent;
    }

    public function GetInput(): string
    {
        do {
            $input = readline('Введіть корректний URL: ');
        } while (!filter_var($input, FILTER_VALIDATE_URL));
        return $input;
    }

    public function CheckUrl($url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL))
            throw new InvalidArgumentException('Невалідний URL: ' . $url);
        return $this->GetRequest($url);
    }

    protected function GetRequest($url): bool
    {
        try {
            $requestResult = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'User-Agent' => $this->userAgent,
                ],
                'timeout' => $this->timeout
            ]);
            $responseCode = $requestResult->getStatusCode();
        } catch (GuzzleException) {
            $responseCode = 0;
        }
        return $this->ValidateResponse($responseCode);
    }

    protected function ValidateResponse(int $code): bool
    {
        $validity = false;
        if (isset($this->statusCodes[$code]))
            $validity = true;
        return $validity;
    }
}
