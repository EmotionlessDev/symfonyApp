<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Application;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use App\Enum\ActionEnum;


class ApplicationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', NumberType::class)
            ->add('quantity', IntegerType::class)   
            ->add('user_id', IntegerType::class)
            ->add(
                'action', EnumType::class, [
                    'class' => ActionEnum::class, 
                ]
            )
            ;
    } 

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Application::class,
            ]
        );
    }
}
