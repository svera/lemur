<?php
namespace Src\Models;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Request
{
    /** @ODM\Id */
    private $id;

    /** @ODM\String */
    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
