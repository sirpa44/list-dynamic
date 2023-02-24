<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder(['availableAt' => new DateTimeImmutable('+5 dsys')])
            ->add('name', TextType::class)
            ->add('price', NumberType::class, [
                // 'constraints' => new NotBlank(['message' => 'please enter your name.']),
            ])
            ->add('country', EntityType::class, [
                // 'constraints' => new NotBlank(['message' => 'please choose a country.']),
                'placeholder' => 'Choose a country',
                'class' => Country::class,
                'choice_label' => 'name',
                'query_builder' => function(CountryRepository $countryRepository) {
                    return $countryRepository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                }
            ])
            ->add('city', EntityType::class, [
                // 'constraints' => new NotBlank(['message' => 'please choose a city.']),
                'placeholder' => 'Choose a city',
                // 'disabled' => true,
                'class' => City::class,
                'choice_label' => 'name',
                'query_builder' => function(CityRepository $countryRepository) {
                    return $countryRepository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                }
            ])
            ->add('message', TextareaType::class, [
                // 'constraints' => [
                    // new NotBlank(['message' => 'seems like your issue has been resolved :).']),
                    // new Length(['min' => 5]),
                // ]
            ])
            ->add('availableAt', DateTimeType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function () {
                dump('test');
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function () {
                dd('test1');
            })
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->get('price')->getData(),
            $form->get('price')->getNormData(),
            $form->get('price')->getViewData());
        }

        return $this->renderForm('home.html.twig', compact('form'));
    }
}
