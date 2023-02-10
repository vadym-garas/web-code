<?php

namespace App\Services;

use App\Entity\UrlCodePair;
use App\Entity\UrlCodePair as UrlCodePairEntity;

use App\Repository\UrlRepository;
use App\UrlCoder\Exceptions\DataNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

class UrlService extends AbstractEntityService
{

    /**
     * @var UrlRepository
     */
    protected ObjectRepository $repository;

    protected function init()
    {
        parent::init();
        $this->repository = $this->doctrine->getRepository(UrlCodePair::class);
    }

    public function incrementUrlCounter(UrlCodePair $url): static
    {
        $url->incrementCounter();
        $this->save($url);

        return $this;
    }

    public function getUrlByCodeAndIncrement(string $code): UrlCodePair
    {
        try {
            $url = $this->getUrlByCode($code);
            $url->incrementCounter();
            $url->updateDateTime();
            $this->save();
            return $url;
        } catch (\Throwable) {
            throw new DataNotFoundException('Url not found by code from UrlService getUrlByCodeAndIncrement');
        }
    }

    public function getUrlByCode(string $code): UrlCodePair
    {
        try {
            return $this->repository->findOneBy(['code' => $code]);
        } catch (\Throwable) {
            throw new DataNotFoundException('Url not found by code from UrlService getUrlByCode code = ' . $code);
        }
    }

    public function getUrlsByUser(?User $user = null): array
    {
        try {
            return $this->repository->findAll();
        } catch (\Throwable) {
            echo 'throw';
            throw new DataNotFoundException('Data not found by code');
        }
    }
}