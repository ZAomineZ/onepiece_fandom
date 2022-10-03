<?php

declare(strict_types=1);

namespace App\Concern\Api;

use App\Exceptions\QwantNotAccess;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class Qwant
{
    const API = 'https://api.qwant.com/v3';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws QwantNotAccess
     * @throws GuzzleException
     */
    public function images(string $search): array
    {
        $root = self::API;
        $search .= ' one piece';
        $search = urlencode($search);
        try {
            $response = $this->client
                ->request('GET', "$root/search/images?t=img&q=$search&imagetype=all&size=large&count=50&locale=fr_FR&offset=100&device=desktop&safesearch=1");
        } catch (GuzzleException $e) {
            throw new QwantNotAccess('The access to qwant api is blocked !');
        }

        $json = json_decode($response->getBody()->getContents(), true);
        $status = $json['status'] ?? 'error';

        if ($status === "success") {
            if (empty($json['data']['result']['items'])) {
                $response = $this->client
                    ->request('GET', "$root/search/images?t=images&q=$search&imagetype=all&count=50&locale=fr_FR&offset=100&device=desktop&safesearch=1");

                $json = json_decode($response->getBody()->getContents(), true);
                return $json['data']['result']['items'];
            }

            return $json['data']['result']['items'];
        }

        if ($status === "error") {
            return [
                "status" => "error",
                "message" => "API Error, a error is occurred !"
            ];
        }

        return [];
    }

    /**
     * @throws QwantNotAccess
     * @throws GuzzleException
     * @throws Exception
     */
    public function image(string $search): string
    {
        $images = $this->images($search);
        $randomInt = random_int(0, 50);

        return $images[$randomInt]['media_fullsize'] ?? "";
    }
}
