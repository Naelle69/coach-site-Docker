<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Surnom (affiché publiquement)',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Votre note',
                'expanded' => true,   // radios
                'multiple' => false,
                'choices' => [
                    '★★★★★' => 5,
                    '★★★★☆' => 4,
                    '★★★☆☆' => 3,
                    '★★☆☆☆' => 2,
                    '★☆☆☆☆' => 1,
                ],
                'constraints' => [new Assert\NotBlank(), new Assert\Range(min:1, max:5)],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre avis',
                'attr' => ['rows' => 5],
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 10)],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Review::class]);
    }
}
