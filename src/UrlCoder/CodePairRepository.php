<?php

namespace App\UrlCoder;

use App\Entity\UrlCodePair as UrlCodePairEntity;
use App\UrlCoder\Exceptions\DataNotFoundException;
use App\UrlCoder\ValueObject\UrlCodePair;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

class CodePairRepository implements Interfaces\ICodeRepository
{
    protected ObjectRepository $cpRepository;
    protected ObjectManager $em;

    public function __construct(protected ManagerRegistry $doctrine)
    {
        $this->em = $this->doctrine->getManager();
        $this->cpRepository = $this->doctrine->getRepository(UrlCodePairEntity::class);
    }

    /**
     * @inheritDoc
     */
    public function saveEntity(UrlCodePair $urlCodePairVO): bool
    {
        try {
            $result = true;
            $codePair = new UrlCodePairEntity($urlCodePairVO->getUrl(), $urlCodePairVO->getCode());
            $this->em->persist($codePair);
            $this->em->flush();
        } catch (\Throwable) {
            $result = false;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function codeIsset(string $code): bool
    {
        return (bool)$this->cpRepository->findOneBy(['code' => $code]);
    }

    /**
     * @inheritDoc
     */
    public function getUrlByCode(string $code): string
    {
        try {
            /**
             * @var UrlCodePairEntity $codePair
             */
            $codePair = $this->cpRepository->findOneBy(['code' => $code]);
            return $codePair->getUrl();
        } catch (\Throwable) {
            throw new DataNotFoundException('Url not found by code from CodePairRepository getUrlByCode');
        }
    }

    /**
     * @inheritDoc
     */
    public function getCodeByUrl(string $url): string
    {
        try {
            /**
             * @var UrlCodePairEntity $codePair
             */
            $codePair = $this->cpRepository->findOneBy(['url' => $url]);
            return $codePair->getCode();
        } catch (\Throwable) {
            throw new DataNotFoundException('Code not found by url from CodePairRepository');
        }
    }
}