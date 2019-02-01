<?php


namespace Planck\Model\Traits;




trait IsTreeEntity
{

    /**
     * @var TreeObject[]
     */
    protected $children =null;

    /**
     * @var TreeObject[]
     */
    protected $descendants =null;


    /**
     * @var TreeObject
     */
    protected $parent;

    /**
     * @var TreeObject[]
     */
    protected $ancestors;





    public function getRoot($noException = false)
    {
        return $this->getRepository()->getRoot($noException);
    }


    public function setParent($family)
    {
        $this->parent = $family;
        $this->setValue('depth', (int) $this->parent->getValue('depth') + 1);
        $family->addChild($this);
        $this->setValue('parent_id', $family->getValue('id'));
        return $this;
    }


    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setValue('name', trim($name));
        $this->setValue('qname', strtolower(slugify($name)));
        return $this;
    }


    /**
     * @param GenericObject $family
     * @return $this
     */
    public function addChild($family)
    {
        $this->children[] = $family;
        if(!$family->getParent()) {
            $family->setParent($this);
        }
        return $this;
    }


    public function getChildren($autoload = true)
    {
        if($this->children === null && $autoload) {
            $this->children = array();
            $this->loadChildren();
        }
        return $this->children;
    }

    public function setChildren($children)
    {
        foreach ($children as $child) {
            $child->setParent($this);
        }
        return $this;
    }

    public function loadChildren()
    {
        $this->children = array();
        $children = $this->repository->getByParentId($this->getValue('id'));


        foreach ($children as $child) {
            $child->setParent($this);
            //$this->children[] = $child;
        }

        return $this;
    }

    public function hasChildren()
    {
        return ($this->getValue('rightbound') - $this->getValue('leftbound')) > 1;
    }



    /**
     * @return GenericObject
     */
    public function getParent()
    {
        if(!$this->parent && $this->getValue('parent_id'))
        {
            $this->parent = $this->repository->getById(
                $this->getValue('parent_id')
            );
        }
        return $this->parent;
    }

    public function reload($cascading = false)
    {
        parent::reload();

        if($this->parent && $cascading) {
            $this->parent->reload($cascading);
        }
        return $this;
    }



    public function setQName($qName)
    {
        $this->setValue('qname', $qName);
        return $this;
    }

    public function getName()
    {
        return $this->getValue('name');
    }


    public function getQName()
    {
        return $this->getValue('qname');
    }

    public function getAncestors()
    {
        if(!$this->ancestors) {
            $this->ancestors = $this->repository->getAncestors($this);
        }

        return $this->ancestors;
    }

    public function getDescendants()
    {
        if(!$this->descendants) {
            $this->descendants = $this->repository->getDescendants($this);
        }

        return $this->descendants;
    }


    public function getPath()
    {
        if($this->getValue('path')) {
            return $this->getValue('path');
        }

        return $this->computePath();

    }



    public function computePath()
    {
        $path= '';
        $ancestors = $this->getAncestors();
        array_reverse($ancestors);
        foreach ($ancestors as $ancestor) {
            $path = '/'.$ancestor->getValue('qname').$path;
        }

        $path.='/'.$this->getQName();

        return strtolower($path);
    }

    public function findInChildren($search, $strict = true)
    {
        return $this->repository->findInChildren($this, $search, $strict, get_class($this));
    }

    public function moveChildrenToParent()
    {
        $children = $this->getChildren();
        foreach ($children as $child) {
            $child->setParent($this->getParent());
            $child->store();
        }
        $this->repository->buildTree();
    }

    public function delete($dryRun =false)
    {
        $this->moveChildrenToParent();
        $this->repository->delete($this, $dryRun);
        return $this;
    }

    public function deleteBranch()
    {
        return $this->repository->deleteBranch($this);
    }


    public function isCluster()
    {
        return $this->getValue('iscluster');
    }


}








