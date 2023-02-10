<?php

namespace App\UrlCoder\Interfaces;


use App\UrlCoder\Exceptions\DataNotFoundException;
use App\UrlCoder\ValueObject\UrlCodePair;


interface ICodeRepository
{
    /**
     * @param UrlCodePair $urlCodePairVO
     * @return bool
     */
    public function saveEntity(UrlCodePair $urlCodePairVO): bool;

    /**
     * @param string $code
     * @return bool
     */
    public function codeIsset(string $code): bool;

    /**
     * @param string $code
     * @throws DataNotFoundException
     * @return string url
     */
    public function getUrlByCode(string $code): string;

    /**
     * @param string $url
     * @throws DataNotFoundException
     * @return string code
     */
    public function getCodeByUrl(string $url): string;

//    /**
//     * @return array
//     */
//    public function loadData(): array;
//
//    /**
//     * @param string $strKeyUrl
//     * @param string $strValStr
//     */
//    public function saveData(string $strKeyUrl, string $strValStr): void;
}