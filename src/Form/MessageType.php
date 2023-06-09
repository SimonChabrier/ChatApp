<?php

namespace App\Form;

use App\Entity\Message;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class,
            [   
                'label' => 'Poster un message',
            ],
            [
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'Bla bla bla...',
                    'required'    => true,
                ],
            ]
            )
            //->add('channel')
            //->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
