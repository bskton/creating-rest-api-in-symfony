<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 18.12.18
 * Time: 21:46
 */

namespace AppBundle\Entity;


use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\Id;

class EntityMerger
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * EntityMerger constructor.
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param $entity
     * @param $changes
     */
    public function merge($entity, $changes): void
    {
        $entityClassName = get_class($entity);

        if (false === $entityClassName) {
            throw new \InvalidArgumentException('$entity is not an object');
        }

        $changesClassName = get_class($changes);

        if (false === $entityClassName) {
            throw new \InvalidArgumentException('$changes is not an object');
        }

        if (!is_a($changes, $entityClassName)) {
            throw new \InvalidArgumentException(
                "Cannot merge object of class $changesClassName with object of class $entityClassName");
        }

        $entityReflection = new \ReflectionObject($entity);
        $changesReflection = new \ReflectionObject($changes);

        foreach ($changesReflection->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            $changedPropertyValue = $changedProperty->getValue($changes);

            if (null === $changedPropertyValue) {
                continue;
            }

            if (!$entityReflection->hasProperty($changedProperty->getName())) {
                continue;
            }

            $entityProperty = $entityReflection->getProperty($changedProperty->getName());
            $annotation = $this->annotationReader->getPropertyAnnotation($entityProperty, Id::class);

            if (null !== $annotation) {
                continue;
            }

            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }
}