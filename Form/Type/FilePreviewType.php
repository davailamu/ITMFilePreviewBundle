<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 20.08.14
 * Time: 20:56
 */

namespace ITM\FilePreviewBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpKernel\Kernel;

class FilePreviewType extends AbstractType
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $curEntity = $form->getParent()->getData();
        $pathResolver = $this->container->get('itm.file.preview.path.resolver');

        if ( $pathResolver->isExists($curEntity, $form->getName()) )
        {
            $filePath = $pathResolver->getPath( $curEntity, $form->getName() );

            $view->vars['info'] = stat($filePath);
            $view->vars['info']['mime'] = mime_content_type($filePath);
        }
    }

    public function getParent()
    {
        return (intval(Kernel::VERSION[0]) >= 3) ? FileType::class : 'file';
    }

    public function getBlockPrefix()
    {
        return 'itm_file_preview';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
} 