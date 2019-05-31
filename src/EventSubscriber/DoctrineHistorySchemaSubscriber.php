<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Events;

class DoctrineHistorySchemaSubscriber implements EventSubscriber
{
    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $eventArgs): void
    {
        if ('sys_period' === $eventArgs->getTableColumn()['field']) {
            $eventArgs->preventDefault();
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onSchemaColumnDefinition
        ];
    }
}