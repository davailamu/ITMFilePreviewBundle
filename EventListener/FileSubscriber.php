<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 31.07.14
 * Time: 20:33
 */

namespace ITM\FilePreviewBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use ITM\FilePreviewBundle\Annotations\FilePreview;

class FileSubscriber implements EventSubscriber
{
    const ANNOTATION_FILEPREVIEW = "\ITM\FilePreviewBundle\Annotations\FilePreview";

    private $container;
    private $config;
    private $files = [];
    private $oldFiles = [];
    private $reader;

    public function __construct(ContainerInterface $container)
    {
        $this->reader = new AnnotationReader();
        $this->container = $container;
        $this->config = $this->container->getParameter('ITMFilePreviewBundleConfiguration');
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postPersist,
            Events::postUpdate,
            Events::postLoad,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->preUpload($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->preUpload($args);
    }

    /**
     * Сохраняем начальные значения полей сущностей
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $doctrine = $this->container->get('doctrine');
        $accessor = PropertyAccess::createPropertyAccessor();
        $curEntity = $args->getEntity();

        $reflectionProperties = new \ReflectionClass($curEntity);
        $properties = $reflectionProperties->getProperties();
        foreach ($properties as $propertie) {
            $annotation = $this->reader->getPropertyAnnotation($propertie, self::ANNOTATION_FILEPREVIEW);
            if (isset($annotation)) {
                $entityClass = get_class($curEntity);
                $fieldName = $propertie->getName();
                // Получаем имя файла и сохраняем в subscriber
                $filename = $accessor->getValue( $curEntity, $fieldName );
                if($filename)
                {
                    $this->oldFiles[$entityClass][$fieldName] = $filename;
                }
            }
        }

    }

    /**
     * Генерируем новые значения для полей сущностей
     *
     * @param LifecycleEventArgs $args
     */
    private function preUpload(LifecycleEventArgs $args)
    {
        $doctrine = $this->container->get('doctrine');
        $accessor = PropertyAccess::createPropertyAccessor();
        $curEntity = $args->getEntity();

        $reflectionProperties = new \ReflectionClass($curEntity);
        $properties = $reflectionProperties->getProperties();
        foreach ($properties as $propertie) {
            $annotation = $this->reader->getPropertyAnnotation($propertie, self::ANNOTATION_FILEPREVIEW);
            if (isset($annotation)) {
                // Получаем загруженный файл и сохраняем в subscriber
                $entityClass = get_class($curEntity);
                $fieldName = $propertie->getName();

                $file = $accessor->getValue( $curEntity, $propertie->getName() );
                if( $file instanceof UploadedFile )
                {
                    $this->files[$entityClass][$fieldName] = $file;

                    // Генерируем уникальное имя для загруженного файла
                    $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->getClientOriginalExtension();
                    $accessor->setValue( $curEntity, $fieldName, $filename );
                }
                elseif(!empty($this->oldFiles[$entityClass][$fieldName]))
                {
                    // Сохраняем старое имя файла
                    $accessor->setValue( $curEntity, $fieldName, $this->oldFiles[$entityClass][$fieldName] );
                }
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->upload($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->upload($args);
    }

    /**
     * Перемещение загруженного файла в хранилище
     *
     * @param LifecycleEventArgs $args
     */
    private function upload(LifecycleEventArgs $args)
    {
        $curEntity = $args->getEntity();

        // Пропускаем сущности, для которых не были загружены файлы
        if( !in_array( get_class($curEntity), array_keys($this->files) ) ) return;

        $pathResolver = $this->container->get('itm.file.preview.path.resolver');
        $uploadPath = $pathResolver->getUploadPath($curEntity);

        $fs = new Filesystem();
        try
        {
            $fs->mkdir($uploadPath);
        }
        catch (IOExceptionInterface $e)
        {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }

        $files = $this->files[get_class($curEntity)];
        foreach( $files as $field => $file )
        {
            if( $file instanceof UploadedFile )
            {
                // Копируем загруженный файл в хранилище
                $fs->copy( $file->getPathname(), $pathResolver->getPath($curEntity, $field) );

                // Удаляем старый файл
                if( !empty($this->oldFiles[get_class($curEntity)][$field]) )
                {
                    $oldFilePath = $pathResolver->getPath( $curEntity, $this->oldFiles[get_class($curEntity)][$field] );
                    if( $fs->exists($oldFilePath) ) $fs->remove( $oldFilePath );
                }
            }
        }
    }
}