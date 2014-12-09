<?php

namespace Src\Models;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class PullRequest
{
    /** @ODM\Id */
    private $id;

    /** @ODM\String */
    private $name;

    /** @ODM\Int */
    private $numberComments;

    /** @ODM\Int */
    private $numberApprovals;

    /** @ODM\Int */
    private $numberDisapprovals;

    /** @ODM\String */
    private $createdBy;

    /** @ODM\String */
    private $createdAt;

    /** @ODM\String */
    private $vcs;

    public function __construct()
    {
        $this->numberComments = 0;
        $this->numberApprovals = 0;
        $this->numberDisapprovals = 0;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setNumberComments($number)
    {
        $this->numberComments = $number;
    }

    public function getNumberComments()
    {
        return $this->numberComments;
    }

    public function setNumberApprovals($number)
    {
        $this->numberApprovals = $number;
    }

    public function getNumberApprovals()
    {
        return $this->numberApprovals;
    }

    public function setNumberDisapprovals($number)
    {
        $this->numberDisapprovals = $number;
    }

    public function getNumberDisapprovals()
    {
        return $this->numberDisapprovals;
    }

    public function setCreatedBy($name)
    {
        $this->createdBy = $name;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedAt($dateTime)
    {
        $this->createdAt = $dateTime;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setVcs($name)
    {
        $this->vcs = $name;
    }

    public function getVcs()
    {
        return $this->vcs;
    }
}
