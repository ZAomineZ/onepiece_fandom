<?php

declare(strict_types=1);

namespace App\Concern\Scrapp;

use App\Concern\Api\Qwant;
use App\Exceptions\QwantNotAccess;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

final class OnePieceFandomPopular
{
    private Client $client;

    private Qwant $qwant;

    public function __construct()
    {
        $this->client = new Client();
        $this->qwant = new Qwant();
    }

    /**
     * @throws GuzzleException
     * @throws QwantNotAccess
     */
    public function popularResults(): array
    {
        $response =  $this->client
            ->request('GET', OnepieceFandom::WIKI_ONEPIECE);
        $crawler = new Crawler($response->getBody()->getContents());

        return $crawler->filter('.popular-pages__item')->each(function ($node) {
            $title = $node->filter('span')->text();
            $link = $node->filter('a')->attr('href');
            $image = $this->image($title);

            return ['title' => $title, 'image' => $image];
        });
    }

    /**
     * @return array
     */
    public function popularity(): array
    {
        $items = [
            ['name' => 'Vegapunk', 'slug' => 'Vegapunk'],
            ['name' => 'Joy Boy', 'slug' => 'Joy_Boy'],
            ['name' => "L'Équipage du Chapeau de Paille", 'slug' => "L%27Équipage_du_Chapeau_de_Paille"],
            ['name' => "Monkey D. Luffy", "slug" => "Monkey_D._Luffy"]
        ];

        $results = [];
        foreach ($items as $key => $item) {
            $name = $item['name'] ?? "";
            $slug = $item['slug'] ?? "";

            $results[$key] = ['title' => $name, 'slug' => $slug];
        }

        return $results;
    }

    /**
     * @throws QwantNotAccess
     * @throws GuzzleException
     * @throws Exception
     */
    protected function image(string $title): string
    {
        $images = $this->qwant->images($title);
        $randomNumber = random_int(0, 50);

        return $images[$randomNumber]['media_fullsize'] ?? "";
    }
}