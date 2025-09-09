<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CityController extends AbstractController
{
    #[Route('/agence/geneve', name: 'app_city_geneva')]
    public function agencyGeneva(): Response
    {
        return $this->render('city/geneva.html.twig');
    }
}
