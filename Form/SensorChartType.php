<?php

namespace Bluemesa\Bundle\SensorBundle\Form;

use Bluemesa\Bundle\CoreBundle\Entity\DatePeriod;
use Bluemesa\Bundle\FormsBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SensorChartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('start', DatePickerType::class)
            ->add('end', DatePickerType::class)
            ->add('period', ChoiceType::class, array(
                    'choices'  => array(
                        'last day' => DatePeriod::LastDay,
                        'last week' => DatePeriod::LastWeek,
                        'last month' => DatePeriod::LastMonth,
                    ),
                    'placeholder' => 'custom (use dates)',
                    'required' => false
                )
            )
            ->add('submit', SubmitType::class, array('label' => 'Display selected period'))
            ->addEventListener(
                FormEvents::SUBMIT,
                array($this, 'onSubmit')
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'show_legend' => false,
            'render_fieldset' => false
        ));

    }

    public function onSubmit(FormEvent $event)
    {
        /** @var DatePeriod $period */
        $period = $event->getData();

        if (null !== $period->getPeriod()) {
            $period->setStart(null);
            $period->setEnd(null);
        }
    }
}
