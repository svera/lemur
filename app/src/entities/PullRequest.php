<?php

namespace Src\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class PullRequest
{
    /** @ODM\Id(strategy="NONE") */
    public $id;

    /** @ODM\String */
    public $name;

    /** @ODM\Int */
    public $numberComments;

    /** @ODM\Int */
    public $numberApprovals;

    /** @ODM\Int */
    public $numberDisapprovals;

    /** @ODM\String */
    public $createdBy;

    /** @ODM\String */
    public $createdAt;

    /** @ODM\String */
    public $updatedAt;

    /** @ODM\String */
    public $status;

    /** @ODM\String */
    public $vcs;

    /** @ODM\String */
    public $htmlUrl;
}
