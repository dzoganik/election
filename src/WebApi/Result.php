<?php

declare(strict_types=1);

namespace App\WebApi;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Result
 * @package App\WebApi
 */
class Result
{
    /**
     * @param HttpClientInterface $client
     */
    public function __construct(private HttpClientInterface $client) {}

    /**
     * @return array
     */
    public function getAll(): array
    {
        $resultXml = $this->getXmlResponse();
        $regions = $this->getRegions($resultXml);
        $data = [];

        foreach ($regions as $region) {
            $data[] = [
                XmlConfig::REGION_TITLE_FIELD => $this->getRegionName($region),
                XmlConfig::REGION_PARTIES_FIELD => $this->getPartiesResults($region),
            ];
        }

        return $data;
    }

    /**
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getXmlResponse(): string
    {
        $response = $this->client->request('GET', XmlConfig::RESULTS_URL);
        return $response->getContent();
    }

    /**
     * @param string $xml
     * @return Crawler
     */
    protected function getRegions(string $xml): Crawler
    {
        $crawler = new Crawler($xml);
        return $crawler->filterXPath('//default:' . XmlConfig::ELEMENT_RESULTS)->children();
    }

    /**
     * @param DOMElement $region
     * @return string
     */
    protected function getRegionName(DOMElement $region): string
    {
        return $region->tagName === XmlConfig::ELEMENT_REGION_CR
            ? 'ČR'
            : $region->getAttribute(XmlConfig::ELEMENT_REGION_TITLE);
    }

    /**
     * @param DOMElement $region
     * @return array
     */
    protected function getPartiesResults(DOMElement $region): array
    {
        $parties = [];

        /** @var DOMElement $item */
        foreach ($region->getElementsByTagName(XmlConfig::ELEMENT_PARTY) as $party) {
            $partyValues = $party->getElementsByTagName(XmlConfig::ELEMENT_PARTY_VALUES)->item(0);

            $parties[] = [
                XmlConfig::PARTY_NUMBER_FIELD => $party->getAttribute(XmlConfig::ELEMENT_PARTY_NUMBER),
                XmlConfig::PARTY_TITLE_FIELD => $party->getAttribute(XmlConfig::ELEMENT_PARTY_TITLE),
                XmlConfig::PARTY_PERCENT_FIELD => (float) $partyValues->getAttribute(XmlConfig::ELEMENT_PARTY_PERCENT),
            ];
        }

        return $this->sortByPercent($parties);
    }

    /**
     * @param array $parties
     * @return array
     */
    protected function sortByPercent(array $parties): array
    {
        usort($parties, function ($item1, $item2) {
            return $item2[XmlConfig::PARTY_PERCENT_FIELD] <=> $item1[XmlConfig::PARTY_PERCENT_FIELD];
        });

        return $parties;
    }
}
