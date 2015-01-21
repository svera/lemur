<?php

namespace Src\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="pullrequests")
 */
class PullRequest
{
    /** @ODM\Id */
    public $id;

    /** @ODM\String */
    public $title;

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

    /** @ODM\Int */
    public $repositoryId;

    /** @ODM\String */
    public $repositoryName;

    /** @ODM\Int */
    public $number;

    /** @ODM\String */
    public $vcs;

    /** @ODM\String */
    public $htmlUrl;
}
