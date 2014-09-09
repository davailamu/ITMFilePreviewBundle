<?php
/**
 * Created by PhpStorm.
 * User: yanker
 * Date: 09.09.14
 * Time: 14:30
 */
namespace ITM\FilePreviewBundle\Twig\Extension;

use ITM\FilePreviewBundle\Resolver\PathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;


class FilePreviewExtension extends \Twig_Extension
{
    private static $pathResolver;
    private static $container;

    public function __construct(PathResolver $pathResolver, ContainerInterface $container)
    {
        self::$pathResolver = $pathResolver;
        self::$container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('itm_file_url', array($this, 'resolveUrl')),
            new \Twig_SimpleFilter('itm_file_path', array($this, 'resolvePath')),
            new \Twig_SimpleFilter('itm_file_exists', array($this, 'fileExists')),
        );
    }


    public static function resolveUrl( $entity, $field )
    {
        return self::$pathResolver->getUrl($entity, $field);
    }

    public static function resolvePath( $entity, $field )
    {
        return self::$pathResolver->getPath($entity, $field, true);
    }

    public static function fileExists( $entity, $field )
    {
        return self::$pathResolver->isExists($entity, $field);
    }


    public function getName()
    {
        return 'itm_file_preview_extension';
    }
}

