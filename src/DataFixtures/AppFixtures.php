<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Party;
use App\Entity\Territory;
use App\Exception\NoTerritoryRowException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures  extends Fixture
{
    public const PARTY_URL = 'https://volby.cz/opendata/ps2021/xml/psrkl.xml';
    public const TERRITORY_URL = 'https://volby.cz/opendata/ps2021/PS_nuts.htm';
    private const NUTS_KEY = 'nuts';
    private const TITLE_KEY = 'title';

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(protected HttpClientInterface $client) {}

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function load(ObjectManager $manager): void
    {
        $this->insertParties($manager);
        $this->insertTerritories($manager);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function insertParties(ObjectManager $manager): void
    {
        $partiesResponse = $this->getResponse(static::PARTY_URL);
        $xmlParties = $this->getParties($partiesResponse);

        foreach ($xmlParties as $xmlParty) {
            $party = new Party();
            $party->setTitle($xmlParty->getElementsByTagName('NAZEV_STRK')->item(0)->textContent);
            $manager->persist($party);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function insertTerritories(ObjectManager $manager): void
    {
        $territoriesResponse = $this->getResponse(static::TERRITORY_URL);
        $htmTerritoriesRows = $this->getTerritories($territoriesResponse);

        foreach ($htmTerritoriesRows as $row) {
            try {
                $territoryData = $this->getTerritoryFromRow($row);
            } catch (NoTerritoryRowException $exception) {
                continue;
            }

            $territory = new Territory();
            $territory->setNuts($territoryData[self::NUTS_KEY])
                ->setTitle($territoryData[self::TITLE_KEY]);

            $manager->persist($territory);
        }

        $manager->flush();
    }

    /**
     * @param string $url
     * @param string $method
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getResponse(string $url, string $method = 'GET'): string
    {
        $response = $this->client->request($method, $url);
        return $response->getContent();
    }

    /**
     * @param string $xml
     * @return Crawler
     */
    private function getParties(string $xml): Crawler
    {
        $crawler = new Crawler($xml);
        return $crawler->filterXPath('//default:PS_RKL')->children();
    }

    /**
     * @param string $html
     * @return Crawler
     */
    private function getTerritories(string $html): Crawler
    {
        $crawler = new Crawler($html);
        return $crawler->filter('table')->children();
    }

    /**
     * @param \DOMNode $row
     * @return string[]
     * @throws NoTerritoryRowException
     */
    private function getTerritoryFromRow(\DOMNode $row): array
    {
        if ($row->nodeName !== 'tr') {
            throw new NoTerritoryRowException();
        }

        $columns = $row->getElementsByTagName('td');

        if (!isset($columns[1], $columns[2]) || !str_starts_with($columns[1]->textContent, 'CZ')) {
            throw new NoTerritoryRowException();
        }

        return [
            self::NUTS_KEY => $columns[1]->textContent,
            self::TITLE_KEY => $columns[2]->textContent,
        ];
    }

}
