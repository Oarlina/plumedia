<?php

namespace App\EventSubscriber;

use CalendarBundle\Entity\Event;
use App\Repository\ChapterRepository;
use CalendarBundle\Event\SetDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private ChapterRepository $chapterRepository;

    public function __construct(ChapterRepository $chapterRepository)
    {
        $this->chapterRepository = $chapterRepository;
    }
    public static function getSubscribedEvents()
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }
}