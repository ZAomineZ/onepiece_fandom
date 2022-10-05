<?php

declare(strict_types=1);

namespace App\Controller;

use App\Concern\Scrapp\Characters\OnePieceCharactersFandom;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CharactersController extends AbstractController
{
    public function __construct(
        protected readonly OnePieceCharactersFandom $onePieceCharactersFandom
    )
    {
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/characters', name: 'characters')]
    public function index(): Response
    {
        $characters = $this->onePieceCharactersFandom->characters();

        return $this->render('pages/character/index.html.twig', compact('characters'));
    }
}