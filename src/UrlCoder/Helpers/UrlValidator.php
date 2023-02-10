<?php

namespace App\UrlCoder\Helpers;

use App\UrlCoder\Interfaces\IUrlValidator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use InvalidArgumentException;

class UrlValidator implements IUrlValidator
{
    protected ClientInterface $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function urlFormatValidate(string $url): bool
    {
        if (empty($url)
            || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Url is broken');
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function urlExistsVerify(string $url): bool
    {
        $allowCodes = [
            200, 201, 301, 302
        ];
        try {
            $response = $this->client->request('GET', $url);
            return (!empty($response->getStatusCode()) && in_array($response->getStatusCode(), $allowCodes));
        } catch (ConnectException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode());
        }

    }
}