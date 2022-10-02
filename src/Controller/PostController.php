<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PostController extends AbstractController
{

    #[Route('/{slug}', name: 'post.show')]
    public function show(): Response
    {
        return $this->render('pages/post/show.html.twig');
    }
}