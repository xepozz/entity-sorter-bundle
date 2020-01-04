<?php

namespace Xepozz\SorterBundle\Tests\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Xepozz\SorterBundle\EventListener\SortListener;
use Xepozz\SorterBundle\Tests\Mock\AbstractSortMock;

class SortListenerTest extends TestCase
{
    private $repo;
    private $em;
    private $event;
    private $updateEvent;

    public function testPrePersistWithEmptyRepository()
    {
        $this->repo
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $abstractSort = new AbstractSortMock();
        $abstractSort->setSort(0);

        $listener = new SortListener();
        $listener->prePersist($abstractSort, $this->event);

        $this->assertEquals(1, $abstractSort->getSort(), 'Inserting into an empty repository, should set sort to 1');
    }

    public function testPrePersistWithNonEmptyRepository()
    {
        $firstElement = new AbstractSortMock();
        $firstElement->setSort(1);

        $this->repo
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($firstElement);

        $abstractSort = new AbstractSortMock();
        $abstractSort->setSort(0);

        $listener = new SortListener();
        $listener->prePersist($abstractSort, $this->event);

        $this->assertEquals(
            2,
            $abstractSort->getSort(),
            'Inserting into a non empty repository, should set sort to max sort + 1'
        );
    }

    protected function setUp()
    {
        $this->repo = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->repo);

        $this->event = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event->method('getEntityManager')
            ->will($this->returnValue($this->em));

        $this->updateEvent = $this
            ->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->updateEvent->method('getEntityManager')
            ->will($this->returnValue($this->em));
    }

//    public function testPreUpdateWithNonEmptyRepository()
//    {
//        $firstElement = new AbstractSortMock();
//        $firstElement->setSort(1);
//
//        $this->repo
//            ->expects($this->once())
//            ->method('findOneBy')
//            ->willReturn($firstElement);
//
//        $this->updateEvent
//            ->expects($this->once())
//            ->method('hasChangedField')
//            ->willReturn(true);
//
//
//        $abstractSort = new AbstractSortMock();
//        $abstractSort->setSort(0);
//
//        $listener = new SortListener();
//        $listener->prePersist($abstractSort, $this->event);
//
//        $this->assertEquals(2, $abstractSort->getSort(), 'Inserting into a non empty repository, should set sort to max sort + 1');
//    }
}