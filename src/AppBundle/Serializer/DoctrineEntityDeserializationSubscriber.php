<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 15.12.18
 * Time: 22:03
 */

namespace AppBundle\Serializer;


use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;
    /**
     * @var RegistryInterface
     */
    private $doctrineRegistry;

    public function __construct(Reader $annotationReader, RegistryInterface $doctrineRegistry)
    {

        $this->annotationReader = $annotationReader;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $deserializedType = $event->getType()['name'];

        if (!class_exists($deserializedType)) {
            return;
        }

        $data = $event->getData();
        $class = new \ReflectionClass($deserializedType);

        foreach ($class->getProperties() as $property) {
            if (!isset($data[$property->name])) {
                continue;
            }

            /** @var DeserializeEntity $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            $data[$property->name] = [
                $annotation->idField => $data[$property->name]
            ];
        }

        $event->setData($data);
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $deserializedType = $event->getType()['name'];
        if (!class_exists($deserializedType)) {
            return;
        }

        $object = $event->getObject();
        $reflaction = new \ReflectionObject($object);

        foreach ($reflaction->getProperties() as $property) {
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            if (!$reflaction->hasMethod($annotation->setter)) {
                throw new \LogicException(
                    "Object {$reflaction->getName()} does not have the {$annotation->setter} method."
                );
            }

            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);

            if (null === $deserializedEntity) {
                return;
            }

            $entityId = $deserializedEntity->{$annotation->idGetter}();
            $repository = $this->doctrineRegistry->getRepository($annotation->type);
            $entity = $repository->find($entityId);

            if (null === $entity) {
                throw new NotFoundHttpException("Resource {$reflaction->getShortName()}/$entityId");
            }

            $object->{$annotation->setter}($entity);
        }
    }
}