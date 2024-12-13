<?php

namespace NakalaImporter\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Job;
use Doctrine\ORM\Event\LifecycleEventArgs;
use DateTime;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class NakalaImporterImport extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="datetime")
     */
    protected $created;

    /**
     * @Column(type="json")
     * @JoinColumn(nullable=false)
     */
    protected $collectionsImported;

    /**
     * @OneToOne(targetEntity="Omeka\Entity\Job")
     * @JoinColumn(nullable=false)
     */
    protected $job;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    public function getCollectionsImported()
    {
        return $this->collectionsImported;
    }

    public function setCollectionsImported($collectionsImported)
    {
        $this->collectionsImported = $collectionsImported;

        return $this;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->created = new DateTime('now');
    }
}
