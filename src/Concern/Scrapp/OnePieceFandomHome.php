<?php

declare(strict_types=1);

namespace App\Concern\Scrapp;

use App\Concern\Api\Qwant;
use App\Exceptions\QwantNotAccess;
use GuzzleHttp\Exception\GuzzleException;

final class OnePieceFandomHome
{
    private Qwant $qwant;

    public function __construct()
    {
        $this->qwant = new Qwant();
    }

    /**
     * @throws QwantNotAccess
     * @throws GuzzleException
     */
    public function results(): array
    {
        $items = [
            ['name' => 'Roronoa Zoro', 'slug' => 'Roronoa_Zoro', 'description' => ""],
            ['name' => 'Sakazuki', 'slug' => 'Sakazuki', 'description' => ""],
            ['name' => 'Shanks', 'slug' => 'Shanks', 'description' => ""],
            ['name' => 'Kozuki Oden', 'slug' => 'Kozuki_Oden', 'description' => ""],
            ['name' => 'Gomu Gomu no Mi', 'slug' => 'Gomu_Gomu_no_Mi', 'description' => ""],
            ['name' => 'Gomu Gomu no Mi/Gear Fifth Techniques', 'slug' => 'Gear_Fifth_Techniques', 'description' => ""],
            ['name' => 'Monkey D. Luffy', 'slug' => 'Monkey_D._Luffy', 'description' => ""],
            ['name' => 'Kaidou', 'slug' => 'Kaidou', 'description' => ""],
            ['name' => 'Arc Pays des Wa', 'slug' => 'Arc_Pays_des_Wa', 'description' => ""],
            ['name' => 'Laugh Tale', 'slug' => 'Laugh_Tale', 'description' => ""],
        ];

        $results = [];
        foreach ($items as $key => $item) {
            $name = $item['name'] ?? "";
            $slug = $item['slug'] ?? "";
            $description = $item['description'] ?? "";

            $results[$key] = ['title' => $name, 'slug' => $slug, 'description' => $description, 'image' => $this->qwant->image($name)];
        }

        return $results;
    }
}