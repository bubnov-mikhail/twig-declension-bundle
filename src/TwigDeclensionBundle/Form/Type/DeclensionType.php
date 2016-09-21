<?php

namespace Bubnov\TwigDeclensionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Bubnov\TwigDeclensionBundle\Entity\Declension;

class DeclensionType extends AbstractType
{
    const FORM_NAME = 'bubnovkelnik_twigdeclensionbundle_declension';
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach(Declension::$forms as $form => $fullForm){
            if($form === Declension::PLURAL){
                continue;
            }
            
            $required = $form === Declension::INFINITIVE 
                      ? true 
                      : false
            ;
            
            $builder->add($fullForm, null, [
                'label' => 'twig-declension.forms.' . $form,
                'required' => $required,
                'attr' => ['twig-declension-form' => $form],
            ]);
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Declension::class
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
