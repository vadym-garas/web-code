<?php

namespace App\UrlCoder\Interfaces;

use InvalidArgumentException;

interface IUrlValidator
{
    /**
     * @param string $url
     * @throws InvalidArgumentException
     * @return bool
     */
    public function urlFormatValidate(string $url): bool;

    /**
     * @param string $url
     * @return bool
     */
    public function urlExistsVerify(string $url): bool;
}