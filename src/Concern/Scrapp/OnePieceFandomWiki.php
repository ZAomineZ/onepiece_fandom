<?php

declare(strict_types=1);

namespace App\Concern\Scrapp;

use App\Concern\Api\Qwant;
use App\Exceptions\QwantNotAccess;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

final class OnePieceFandomWiki
{
    private Client $client;

    private Qwant $qwant;

    protected ?Crawler $crawler = null;

    public function __construct()
    {
        $this->client = new Client();
        $this->qwant = new Qwant();
    }

    /**
     * @throws GuzzleException
     */
    public function wikiPage(string $wikiSearch): array
    {
        $crawler = $this->responseWiki($wikiSearch);

        // Paragraph
        $paragraphs = $crawler->filter('.cquote ~ p')
            ->each(fn($node) => $node->text());
        $paragraphs = array_filter($paragraphs);
        if (empty($paragraphs)) {
            $paragraphs = $crawler->filter('p')
                ->each(fn($node) => $node->text());
            $paragraphs = array_filter($paragraphs);
        }
        return array_slice($paragraphs, 0, 5);
    }

    /**
     * @throws GuzzleException
     */
    public function informations(string $wikiSearch): array
    {
        $crawler = $this->responseWiki($wikiSearch);

        // Get data on information
        $informations = $crawler
            ->filter('.pi-item.pi-data.pi-item-spacing.pi-border-color')
            ->each(function ($node) {
                return $node->text();
            });

        $newInformations = [];
        foreach ($informations as $information) {
            $parts = explode(' : ', $information);
            $key = $parts[0] ?? "";
            $value = $parts[1] ?? "";
            $newInformations[$key] = $value;
        }

        return $newInformations;
    }

    /**
     * @throws GuzzleException
     */
    public function title(string $wikiSearch): string
    {
        $crawler = $this->responseWiki($wikiSearch);

        $title = $crawler
            ->filter('.page-header__title-wrapper .page-header__title')
            ->text();
        return trim($title);
    }

    /**
     * @throws QwantNotAccess
     * @throws GuzzleException
     * @throws Exception
     */
    public function image(string $wikiSearch)
    {
        $images = $this->qwant->images($wikiSearch);
        $randomInt = random_int(0, 50);
        $image = $images[$randomInt];

        return $image['media_fullsize'] ?? "";
    }

    /**
     * @throws GuzzleException
     */
    protected function responseWiki(string $wikiSearch): Crawler
    {
        $response = $this->client
            ->request('GET', OnepieceFandom::WIKI . "/$wikiSearch");
        if (!$this->crawler) {
            $this->crawler = new Crawler($response->getBody()->getContents());
        }
        return $this->crawler;
    }
}