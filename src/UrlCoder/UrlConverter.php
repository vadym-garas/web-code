<?php

namespace App\UrlCoder;

use App\UrlCoder\Exceptions\DataNotFoundException;
use App\UrlCoder\Interfaces\{ICodeRepository, IUrlDecoder, IUrlEncoder, IUrlValidator};
use App\UrlCoder\ValueObject\UrlCodePair;
use InvalidArgumentException;


class UrlConverter implements IUrlDecoder, IUrlEncoder
{
    const CODE_LENGTH = 6;
    const CHAR_SET = '0123456789abcdfghjkmnpqrstvwxyzABCDFGHJKLMNPQRSTVWXYZ';

    protected ICodeRepository $repository;
    protected int $codeLength;
    protected IUrlValidator $validator;


    /**
     * @param ICodeRepository $repository
     * @param IUrlValidator $validator
     * @param int $codeLength
     */
    public function __construct(
        ICodeRepository $repository,
        IUrlValidator $validator,
        int $codeLength = self::CODE_LENGTH
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->codeLength = $codeLength;
    }

    /**
     * @param string $url
     * @throws InvalidArgumentException
     * @return string
     */
    public function encode(string $url): string
    {
        $this->validateUrl($url);
        try {
            $code = $this->repository->getCodeByUrl($url);
        } catch (DataNotFoundException $e) {
            $code = $this->generateAndSaveCode($url);
        }
        return $code;
    }

    /**
     * @param string $code
     * @throws InvalidArgumentException
     * @return string
     */
    public function decode(string $code): string
    {
        try {
            $result = $this->repository->getUrlByCode($code);
        } catch (DataNotFoundException $e) {
//            SingletonLogger::error($e->getMessage());
            throw new InvalidArgumentException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
        return $result;
    }

    /**
     * @description цей метод робить тето
     * @param string $url
     * @return string
     */
    protected function generateAndSaveCode(string $url): string
    {
        $code = $this->generateUniqueCode();
        $this->repository->saveEntity(new UrlCodePair($code, $url));
        return $code;
    }

    protected function validateUrl(string $url): bool
    {
        try {
            $result = $this->validator->urlFormatValidate($url);
            $this->validator->urlExistsVerify($url);
        } catch (InvalidArgumentException $e) {
//            SingletonLogger::error($e->getMessage() . ' - ' . $url);
            throw $e;
        }
        return $result;
    }

    protected function generateUniqueCode(): string
    {
        $date = new \DateTime();
        $str = static::CHAR_SET . mb_strtoupper(static::CHAR_SET) . $date->getTimestamp();
        return substr(str_shuffle($str), 0, $this->codeLength);
    }
}