<?php

declare(strict_types=1);

namespace App\Controller;

use App\Concern\Scrapp\OnePieceFandomPopular;
use App\Concern\Scrapp\OnePieceFandomSearch;
use App\Exceptions\QwantNotAccess;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly OnePieceFandomSearch $onePieceFandomSearch,
        private readonly OnePieceFandomPopular $onePieceFandomPopular
    )
    {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws GuzzleException
     * @throws QwantNotAccess
     */
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $results = [];

        if ($request->query->has('search')) {
            $search = $request->query->get('search');
            $results = $this->onePieceFandomSearch->searchResults($search);
        }

        $popularResults = $this->onePieceFandomPopular->popularity();

        return $this->render('pages/index.html.twig', compact('results', 'popularResults'));
    }
}
