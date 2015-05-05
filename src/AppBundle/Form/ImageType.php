<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tmpFile', 'file')
            ->add('story', 'entity', array(
                    "class" => "AppBundle\Entity\Story",
                    'query_builder' => function ($er) {
                        return $er->createQueryBuilder('s')
                            ->andWhere('s.isPublished = 1')
                            ->orderBy('s.title', 'ASC');
                    },
                ))
            ->add('showPubOnly', 'checkbox', array(
                    "mapped" => false,
                ))
            ->add('Charger', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Image'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_image';
    }
}
