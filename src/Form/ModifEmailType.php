<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email' , EmailType::class, [
            'attr' => ['class' => 'form-control ', 
                        'placeholder' => 'new email...'],
            'constraints' => [new NotBlank([
                'message' => 'Please enter a mail',
            ]),],
        
            'label_attr' => ['class' => 'none'],
        ])

        ->add('change' , SubmitType::class , [
            'attr' => ['class' => 'btn btn-dark'],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
