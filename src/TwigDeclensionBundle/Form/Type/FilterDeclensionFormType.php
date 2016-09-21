<?php

namespace Bubnov\TwigDeclensionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterDeclensionFormType extends AbstractType
{
    const FORM_NAME = 'bubnovkelnik_twigdeclensionbundle_find';

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('infinitive', TextType::class, [
                'label' => 'twig-declension.forms.inf',
                'required' => false,
            ])
            ->add('needs_work', CheckboxType::class, [
                'label' => 'twig-declension.needs_work',
                'data' => false,
                'required' => false,
            ])
            ->setMethod('POST')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::FORM_NAME;
    }
}
