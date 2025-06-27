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

    public function onCalendarSetData(SetDataEvent $setDataEvent) 
    {
        $start = $setDataEvent->getStart();
        $end = $setDataEvent->getEnd();
        $filters = $setDataEvent->getFilters();
        // You may want to make a custom query from your database to fill the calendar

        // $setDataEvent->addEvent(new Event(
        //     'Event 1',
        //     new \DateTime('Tuesday this week'),
        //     new \DateTime('Wednesdays this week')
        // ));
        $chapters = $this->chapterRepository->findAll();
        foreach($chapters as $chapter){
            $setDataEvent->addEvent(new Event($chapter->getName(),
             new \DateTime ($chapter->getPublish())
            ));
        }
    //     // If the end date is null or not defined, it creates a all day event
        // $setDataEvent->addEvent(new Event(
        //     'All day event',
        //     new \DateTime('Friday this week')
        // ));
    }
}