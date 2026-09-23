<?php

namespace App\EventListener;

use App\Entity\Attribute;
use App\Entity\AttributeOption;
use App\Enum\AttributeType;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush)]
class AttributeOptionsListener
{
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $optionMetadata = $em->getClassMetadata(AttributeOption::class);
        $attributeMetadata = $em->getClassMetadata(Attribute::class);

        $entities = array_merge(
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityInsertions()
        );

        foreach ($entities as $entity) {
            if (!$entity instanceof Attribute) continue;
            if ($entity->getType() === AttributeType::SELECT) continue;

            $entity->setIsMultiple(false);

            foreach ($entity->getOptions() as $option) {
                if ($option->getId() !== null) {
                    $em->remove($option);
                    $uow->computeChangeSet($optionMetadata, $option);
                }
            }

            $entity->getOptions()->clear();
            $uow->recomputeSingleEntityChangeSet($attributeMetadata, $entity);
        }
    }
}
