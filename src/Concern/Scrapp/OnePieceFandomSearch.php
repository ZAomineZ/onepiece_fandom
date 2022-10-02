<?php

declare(strict_types=1);

namespace App\Concern\Scrapp;

use App\Concern\Api\Qwant;
use App\Exceptions\QwantNotAccess;
use Cocur\Slugify\Slugify;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

final class OnePieceFandomSearch
{
    private Client $client;

    private Slugify $slugify;

    private Qwant $qwant;

    public function __construct(
        private readonly string $publicDir
    )
    {
        $this->client = new Client();
        $this->slugify = new Slugify();
        $this->qwant = new Qwant();
    }

    /**
     * @throws GuzzleException
     * @throws QwantNotAccess
     * @throws Exception
     */
    public function searchResults(string $search): array
    {
        $response = $this->client
            ->request('GET', OnepieceFandom::SEARCH . "&search=$search");
        $crawler = new Crawler($response->getBody()->getContents());

        $results = $crawler->filter('.unified-search__result article')->each(function ($node) {
            $title = $node->filter('.unified-search__result__header a')->text();
            $link = $node->filter('.unified-search__result__header a')->attr('href');
            $description = $node->filter('.unified-search__result__content')->text();

            $partsLink = explode('/', $link);
            $slug = end($partsLink) ?? "";

            return [
                'title' => $title,
                'link' => $link,
                'slug' => $slug,
                'description' => $description
            ];
        });

        foreach ($results as $key => $item) {
            $title = $item['title'] ?? "";
            $slug = $this->slugify->slugify($title) ?? "";

            $responsePage = $this->client->request('GET', $item['link']);
            $crawlerPage = new Crawler($responsePage->getBody()->getContents());
            try {
                $image = $crawlerPage
                    ->filter('.wds-tab__content.wds-is-current p a')
                    ->attr('href');
            } catch (InvalidArgumentException $exception) {
                $image = "";
            }

            // Upload and convert image
            $destinationFile = $this->publicDir . '/' . $slug . '.png';
            if (!file_exists($destinationFile)) {
                $images = $this->qwant->images($title);
                $randomNumber = random_int(0, 50);

                // Upload file
                $image = $images[$randomNumber]['media_fullsize'] ?? "";
                // file_put_contents($destinationFile, $imageQwant);
            }

            $results[$key] = [
                ...$item,
                'image' => $image
            ];
        }

        return $results;
    }
}