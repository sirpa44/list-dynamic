<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CountryRepository $countryRepository, CityRepository $cityRepository): Response
    {
        $form = $this->createFormBuilder(['country' => $countryRepository->find(4)])
            ->add('name', TextType::class)
            ->add('age', IntegerType::class)
            ->add('price', NumberType::class, [
                'constraints' => new NotBlank(['message' => 'please enter your name.']),
            ])
            ->add('country', EntityType::class, [
                'constraints' => new NotBlank(['message' => 'please choose a country.']),
                'placeholder' => 'Choose a country',
                'class' => Country::class,
                'choice_label' => 'name',
                'query_builder' => function() use($countryRepository) {
                    return $countryRepository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                }
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use($cityRepository) {
                    $country = $event->getData()['country'] ?? null;

                    $event->getForm()->add('city', EntityType::class, [
                            'constraints' => new NotBlank(['message' => 'please choose a city.']),
                            'placeholder' => 'Choose a city',
                            'disabled' => $country === null,
                            'class' => City::class,
                            'choice_label' => 'name',
                            'choices' => $cityRepository->findByCountry($country)
                        ]);
            })
            
            ->add('message', TextareaType::class, [
                // 'constraints' => [
                    // new NotBlank(['message' => 'seems like your issue has been resolved :).']),
                    // new Length(['min' => 5]),
                // ]
            ])
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
