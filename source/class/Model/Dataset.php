<?php


namespace Planck\Model;

use Planck\Traits\IsApplicationObject;

class Dataset extends \Phi\Model\Dataset
{

    use IsApplicationObject;

    /** @var  Repository */
    protected $repository;

    public function loadForeignEntity($foreignRepositoryName, $foreignKey, $targetFieldName)
    {

        $foreignRepository = $this->getApplication()->getModelRepository($foreignRepositoryName);

        $values = array();
        foreach ($this->getAll() as $entity) {
            $values[$entity->getValue($foreignKey)] = $entity->getValue($foreignKey);
        }


        $query ="
            SELECT * FROM ".$foreignRepository->getTableName()."
            WHERE id IN (".implode(',', $values).")
        ";

        $dataset = $foreignRepository->queryAndGetDataset($query)->indexBy('id');

        foreach ($this->getAll() as $entity) {
            if(isset($dataset[$entity->getValue($foreignKey)])) {
                $foreignEntity = $dataset[$entity->getValue($foreignKey)];
                $entity->setValue($targetFieldName, $foreignEntity);
            }
        }

        return $dataset;
    }


    /*
    public function loadUsers()
    {
        return $this->loadForeignEntity('User', 'user_id', '__user');
    }
    */


    public function wrapEntity(Entity $entity)
    {
        $this->setValues(array($entity));
    }



}


