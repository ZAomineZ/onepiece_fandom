<?php

declare(strict_types=1);

namespace App\Concern\Scrapp\Characters;

use App\Concern\Scrapp\OnepieceFandom;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

final class OnePieceCharactersFandom
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function characters(): array
    {
        $response =  $this->client
            ->request('GET', OnepieceFandom::CHARACTERS);
        $crawler = new Crawler($response->getBody()->getContents());


        $results = $crawler->filter('table.wikitable.sortable tbody tr')->each(function (Crawler $node) {
            try {
               $name = $node->filter('td:nth-child(2)')->text();
            } catch (InvalidArgumentException $exception) {
                $name = "";
            }

            return [
                'name' => $name,
                'chapter' => $name ? $node->filter('td:nth-child(3)')->text() : "",
                'episode' => $name ? $node->filter('td:nth-child(4)')->text() : "",
                'year' => $name ? $node->filter('td:nth-child(5)')->text() : "",
                'note' => $name ? $node->filter('td:nth-child(6)')->text() : ""
            ];
        });

        $results = array_filter($results, fn (array $data) => !empty($data['name']));

        return array_values($results);
    }
}