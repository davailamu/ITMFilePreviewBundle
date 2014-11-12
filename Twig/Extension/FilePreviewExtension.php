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
            new \Twig_SimpleFilter('readable_filesize', array($this, 'readableFilesize')),
        );
    }

    /**
     * Url к файлу
     * @param $entity
     * @param $field
     * @return string
     */
    public static function resolveUrl( $entity, $field )
    {
        return self::$pathResolver->getUrl($entity, $field);
    }

    /**
     * Путь к файлу
     * @param $entity
     * @param $field
     * @return string
     */
    public static function resolvePath( $entity, $field )
    {
        return self::$pathResolver->getPath($entity, $field, true);
    }

    /**
     * Проверка на существование файла
     * @param $entity
     * @param $field
     * @return bool
     */
    public static function fileExists( $entity, $field )
    {
        return self::$pathResolver->isExists($entity, $field);
    }

    /**
     * Размер файл в человека понятном виде
     * @param integer $size
     * @return string
     */
    public function readableFilesize($size)
    {
        if( $size <= 0 ) {
            return '0 KB';
        }

        if( $size === 1 ) {
            return '1 byte';
        }

        $mod = 1024;
        $units = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');

        for( $i = 0; $size > $mod && $i < count($units) - 1; ++$i ) {
            $size /= $mod;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getName()
    {
        return 'itm_file_preview_extension';
    }
}

