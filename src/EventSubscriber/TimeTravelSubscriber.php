<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TimeTravelSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (null === $backToTheFuture = $request->get('timetravel')) {
            return;
        }

        $conn = $this->em->getConnection();
        $conn->exec('SET SEARCH_PATH TO timetravel;');
        $conn->exec(sprintf('SET timetravel.timestamp=\'%s\';', $backToTheFuture));
    }
}
