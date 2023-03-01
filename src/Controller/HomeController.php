<?php

namespace App\Controller;

use App\Form\TicketFormType;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CountryRepository $countryRepository): Response
    {
        $form = $this->createForm(TicketFormType::class, ['country' => $countryRepository->find(3)]);        

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->get('price')->getData(),
            $form->get('price')->getNormData(),
            $form->get('price')->getViewData());
        }

        return $this->renderForm('home.html.twig', compact('form'));
    }
}
