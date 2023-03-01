<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketFormType extends AbstractType
{
    public function __construct(
        protected CityRepository $cityRepository,
        protected CountryRepository $countryRepository,
        ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => new NotBlank(['message' => 'please choose a city.']),
        ])
        ->add('age', IntegerType::class)
        ->add('price', NumberType::class, [
            'constraints' => new NotBlank(['message' => 'please enter your name.']),
        ])
        ->add('country', EntityType::class, [
            'constraints' => new NotBlank(['message' => 'please choose a country.']),
            'placeholder' => 'Choose a country',
            'class' => Country::class,
            'choice_label' => 'name',
            'choices' => $this->countryRepository->findAllOrderByASCName(),
        ])
        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $country = $event->getData()['country'] ?? null;

                $event->getForm()->add('city', EntityType::class, [
                        'constraints' => new NotBlank(['message' => 'please choose a city.']),
                        'placeholder' => 'Choose a city',
                        'disabled' => $country === null,
                        'class' => City::class,
                        'choice_label' => 'name',
                        'choices' => $this->cityRepository->findByCountry($country),
                    ]);
        })
        ->add('message', TextareaType::class, [
            'constraints' => [
                new NotBlank(['message' => 'seems like your issue has been resolved :).']),
                new Length(['min' => 5]),
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
