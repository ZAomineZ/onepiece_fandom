<?php

declare(strict_types=1);

namespace App\Controller;

use App\Concern\Scrapp\OnePieceFandomWiki;
use App\Exceptions\QwantNotAccess;
use Cocur\Slugify\Slugify;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PostController extends AbstractController
{
    private OnePieceFandomWiki $onepieceFandomWiki;

    private Slugify $slugify;

    public function __construct()
    {
        $this->onepieceFandomWiki = new OnePieceFandomWiki();
        $this->slugify = new Slugify();
    }

    /**
     * @throws GuzzleException
     * @throws QwantNotAccess
     */
    #[Route('/{slug}', name: 'post.show')]
    public function show(string $slug): Response
    {
        $paragraphs = $this->onepieceFandomWiki->wikiPage($slug);
        $title = $this->onepieceFandomWiki->title($slug);
        $image = $this->onepieceFandomWiki->image($title);
        $informations = $this->onepieceFandomWiki->informations($slug);

        return $this->render('pages/post/show.html.twig', compact('paragraphs', 'image', 'title', 'informations'));
    }
}