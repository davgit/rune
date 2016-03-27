<?php

namespace uuf6429\Rune\example\Model;

use uuf6429\Rune\Model\AbstractModel;

/**
 * @property uuf6429\Rune\Example\Model\Category $parent
 */
class Category extends AbstractModel
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    protected $parentId;

    /** @var callable */
    protected $categoryProvider;

    /**
     * @param int      $id
     * @param string   $name
     * @param int      $parentId
     * @param callable $categoryProvider Returns category given $id as first param.
     */
    public function __construct($id, $name, $parentId, $categoryProvider)
    {
        $this->id = $id;
        $this->name = $name;

        $this->parentId = $parentId;
        $this->categoryProvider = $categoryProvider;
    }

    /**
     * @return Category
     */
    protected function getParent()
    {
        $call = $this->categoryProvider;

        return $call($this->parentId);
    }
}
