<?php
namespace Planck\Model\Traits;



use Planck\Exception;

Trait IsTreeRepository
{


    private $leftboundFieldName = 'leftbound';
    private $rightboundFieldName = 'rightbound';
    private $parentIdFieldName = 'parent_id';
    private $depthFieldName = 'depth';
    private $idFieldName = 'id';









    public function getRoot($noException = false)
    {
        $query = 'SELECT * FROM '.$this->getTableName().' WHERE parent_id IS null';
        $values = $this->queryAndFetchOne($query);
        if(!empty($values)) {
            $instance = $this->getEntityInstance();
            $instance->setValues($values);
            return $instance;
        }
        else {
            if($noException) {
                return false;
            }
            throw new Exception('This tree has no root node. You have to initialize tree');


        }
    }

    public function getByPath($path)
    {
        $query = 'SELECT * FROM '.$this->getTableName().' WHERE path = :path';
        $values = $this->queryAndFetchOne(
            $query,
            array(':path' => $path)
        );

        $instance = $this->getEntityInstance();
        $instance->setValues($values);
        return $instance;
    }



    /**
     * @param GenericObjectEntity $family
     * @return GenericObjectEntity
     */
    public function insert($family, $dryRun = false)
    {

        if($parent = $family->getParent()) {

            $leftBound = $parent->getValue('rightbound');
            $rightBound = $leftBound + 1;
            $this->updateBoundsForInsert($parent);

        }
        else {
            $leftBound = 0;
            $rightBound = 1;
        }


        $family->setValue('leftbound', $leftBound);
        $family->setValue('rightbound', $rightBound);
        if($family->getValue('depth') ===null) {
            $family->setValue('depth', 0);
        }

        parent::insert($family, $dryRun);

        $family->reload();
        return $family;
    }

    public function getTree($nodeId = 1, $depth = 1, $flattened = false)
    {


        $rootQuery = "parent.parent_id IS NULL";
        $parameters = array();
        if($nodeId) {
            $rootQuery = "parent.parent_id = :id";
            $parameters = array(
                ':id' => $nodeId,
            );
        }

        $fields = $this->getEntityFieldsString('child.');

        $query = "
            SELECT
                ".$fields."
                ,parent.depth as parentDepth,
                (child.rightbound - child.leftbound)>2 as childCount
                ,(child.depth - parent.depth) as difference
            FROM ".$this->getTableName()." parent
            JOIN ".$this->getTableName()." child
                ON parent.leftbound<=child.leftbound
                AND parent.rightbound>=child.rightbound
            WHERE 

                ".$rootQuery."

                 
            ORDER BY child.depth ASC, childCount DESC
        ";

        $rows = $this->queryAndFetch($query, $parameters);


        $root=array();
        $index = array();
        $failed = array();



        foreach ($rows as $values) {
            if($values['difference'] <= $depth || !$depth) {
                $instance = $this->getEntityInstance();
                $instance->setValues($values);

                $index[$values['id']] = $instance;

                if(!$values['parent_id']) {
                    $root[$values['id']] = $instance;
                }
                else if(!isset($index[$values['parent_id']])) {
                    $failed[] = $instance;
                }
                else {
                    $instance->setParent($index[$values['parent_id']]);
                }
            }
        }


        foreach ($failed as $instance) {
            $parentId = $instance->getValue('parent_id');
            if(isset($index[$parentId])) {
                $instance->setParent($index[$parentId]);
            }
        }


        if($flattened) {
            return $this->flattenTree($root);
        }


        return $root;
    }


    public function flattenTree($roots)
    {

        if(!is_array($roots)) {
            $roots = array($roots);
        }

        $flattenedTree = array();



        foreach ($roots as $root) {
            $flattenedTree[] = $root;


            foreach ($root->getChildren() as $child) {

                //$flattenedTree[] = $child;
                $subTree = $this->flattenTree($child);

                $flattenedTree = array_merge($flattenedTree, $subTree);
            }
        }


        return $flattenedTree;
    }






    public function getInPathBy($path, $field, $value)
    {
        $fields = $this->getEntityFieldsString('child');

        $query = "
            SELECT
              ".$fields."              
              
            FROM ".$this->getTableName()." instance
            JOIN  ".$this->getTableName()." child
                ON child.leftbound > instance.leftBound
                AND child.rightbound < instance.rightBound
                AND child.".$field." = :value
            WHERE instance.path = :path
        ";

        $rows = $this->queryAndFetch($query, array(
            ':path' => $path,
            ':value' => $value,
        ));

        return $this->getDataset($rows);


    }


    public function findInChildren($instance, $search, $strict = true, $cast = null)
    {

        $fields = $this->getEntityFieldsString('instance');
        //no binded query because does not work in like %% (with sqlite)
        $query = "
            SELECT
              ".$fields."              
              
            FROM ".$this->getTableName()." instance
            WHERE
            path LIKE :path AND
            (
                instance.name LIKE :name
                OR instance.qname LIKE :qname
            )
            ORDER by depth DESC
        ";


        $path = $instance->getPath().'/%';

        $name = '' . $search . '';
        $qname = '' . slugify($search) . '';

        if(!$strict) {
            $name = '%' . $search . '%';
            $qname = '%' . slugify($search) . '%';
        }



        $rows = $this->queryAndFetch($query, array(
            ':path' => $path,
            ':name' => $name,
            ':qname' => $qname
        ));

        return $this->getDataset($rows, $cast);

    }



    /**
     * @param $search
     * @return GenericObjectEntity[]
     */
    public function searchInTree($search, $cast = null)
    {

        $fields = $this->getEntityFieldsString('instance');

        $query = "
            SELECT

               ".$fields."
              
            FROM ".$this->getTableName()." instance
            JOIN  product parent
                ON parent.leftbound<instance.leftbound
                AND parent.rightbound>instance.rightbound
            WHERE
            (
                instance.name LIKE ".$this->escape('%'.$search.'%')." COLLATE NOACCENTS COLLATE NOCASE
                OR instance.qname LIKE ".$this->escape('%'.slugify($search).'%')."
            )
            -- AND (NOT(instance.hidden))
            GROUP BY instance.id
            HAVING SUM(parent.searchdisabled) = 0
            ORDER by instance.depth DESC
        ";

        $rows = $this->queryAndFetch($query);
        return $this->getDataset($rows, $cast);

    }




    /**
     * @param GenericObjectEntity $object
     * @return GenericObjectEntity[]
     */
    public function getDescendants($object)
    {

        $fields = $this->getEntityFieldsString( 'child.');
        $query = "
            SELECT
              ".$fields."              
            FROM ".$this->getTableName()." parent
            JOIN  ".$this->getTableName()." child
                ON parent.leftbound<child.leftbound
                AND parent.rightbound>child.rightbound
              WHERE parent.id = :id
            ORDER BY parent.depth DESC
        ";

        $rows = $this->queryAndFetch($query, array(':id' => $object->getId()));
        return $this->getDataset($rows);
    }

    /**
     * @param GenericObjectEntity $object
     * @return GenericObjectEntity[]
     */
    public function getAncestors($object)
    {

        $fields = $this->getEntityFieldsString('parent.');
        $query = "
            SELECT
              ".$fields."
              
              
            FROM ".$this->getTableName()." parent
            JOIN  ".$this->getTableName()." child
                ON parent.leftbound<child.leftbound
                AND parent.rightbound>child.rightbound
              WHERE child.id = :id
            ORDER BY parent.depth DESC
        ";

        $rows = $this->queryAndFetch($query, array(':id' => $object->getId()));
        return $this->getDataset($rows);

    }












    public function getByParentId($id)
    {
        $rows = $this->queryAndFetch(
            'SELECT * FROM '.$this->getTableName().' WHERE '.$this->parentIdFieldName.' = :id',
            array(':id' => $id)
        );
        return $this->getDataset($rows);
    }





    protected function updateBoundsForInsert($parent)
    {

        $leftBound = $parent->getValue($this->rightboundFieldName);


        $query = "
                UPDATE ".$this->getTableName()."
                SET
                    ".$this->rightboundFieldName." = ".$this->rightboundFieldName."+2
                 WHERE
                    ".$this->rightboundFieldName.">=:leftbound
            ";

        $this->query($query, array(
            ':leftbound' => $leftBound,
        ));

        $query = "
                UPDATE ".$this->getTableName()."
                SET
                    ".$this->leftboundFieldName." = ".$this->leftboundFieldName."+2
                 WHERE
                    ".$this->leftboundFieldName.">:leftbound
            ";

        $this->query($query, array(
            ':leftbound' => $leftBound,
        ));

        $parent->reload();


        return $this;
    }



    public function deleteBranch($object)
    {

        $this->startTransaction();

        $children = $object->getDescendants();
        foreach ($children as $child) {
            $this->delete($child, false);
        }
        $this->delete($object, false);
        $this->commit();

        $this->buildTree();
        return $object;
    }


    public function delete($entity, $dryRun = false)
    {


        $query = "
            DELETE FROM ".$this->getTableName()."
            WHERE ".$this->idFieldName." = :id
        ";

        $this->query($query, array(':id' => $entity->getId()));
        $this->buildTree();

        return $entity;
    }





    public function buildTree($family = null, &$bound = 0, $depth =0, $useCache = true )
    {

        static $sortedFamilies;
        if(!$sortedFamilies) {
            $allFamilies = $this->getAll();
            foreach ($allFamilies as $tempFamily) {
                $sortedFamilies[$tempFamily->getValue('id')] = $family;
            }

            foreach ($allFamilies as $tempFamily) {
                if(isset($sortedFamilies[$tempFamily->getValue('parent_id')])) {
                    $tempFamily->setParent(
                        $sortedFamilies[$tempFamily->getValue('parent_id')]
                    );
                }
            }
        }


        if($depth === 0) {
            $this->startTransaction();
        }



        if(!$family) {
            $root = $this->getById(1);
            $family = $root;
        }

        $family->setValue($this->leftboundFieldName, $bound);
        $family->setValue($this->depthFieldName, $depth);



        $bound++;
        $depth++;


        if(isset($sortedFamilies[$family->getValue('id')]) && $useCache) {
            $children = $sortedFamilies[$family->getValue('id')]->getChildren();
        }
        else {
            $children = $family->getChildren();
        }


        foreach ($children as $child) {
            $this->buildTree($child, $bound, $depth, $useCache);
        }



        $depth--;
        $family->setValue($this->rightboundFieldName, $bound);
        $bound++;
        $family->store();


        if($depth === 0) {
            $this->commit();
        }



    }








}

