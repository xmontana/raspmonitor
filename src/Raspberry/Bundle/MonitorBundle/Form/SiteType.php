<?php

namespace Raspberry\Bundle\MonitorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label_attr' => array(
                        'class' => 'col-md-2 control-label'
                    ),
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ))
            ->add('url', null, array(
                    'label_attr' => array(
                        'class' => 'col-md-2 control-label'
                    ),
                    'attr' => array(
                        'class' => 'form-control '
                    )
                ))
            ->add('icon','choice', array(
                    'label_attr' => array(
                        'class' => 'col-md-2 control-label'
                    ),
                    'choices'   => array(
                        'fa fa-linux'   => 'fa-linux',
                        'fa fa-windows' => 'fa-windows',
                        'fa fa-wordpress'   => 'fa-wordpress',
                        'fa fa-drupal'   => 'fa-drupal',
                        'fa fa-apple'   => 'fa-apple',
                        'fa fa-database'   => 'fa-database',
                        'fa fa-bank'   => 'fa-bank',
                        'fa fa-building'   => 'fa-building',
                        'fa fa-cloud'   => 'fa-cloud',

                    ),
                    'attr' => array('class' => 'selectpicker')
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Raspberry\Bundle\MonitorBundle\Entity\Site'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'raspberry_bundle_monitorbundle_site';
    }
}
