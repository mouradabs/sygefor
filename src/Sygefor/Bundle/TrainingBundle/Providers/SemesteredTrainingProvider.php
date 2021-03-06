<?php
/**
 * Created by PhpStorm.
 * User: maxime
 * Date: 23/04/14
 * Time: 11:30
 */

namespace Sygefor\Bundle\TrainingBundle\Providers;


use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\ElasticaBundle\Doctrine\AbstractProvider;
use FOS\ElasticaBundle\Doctrine\ORM\Provider;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use FOS\ElasticaBundle\Provider\IndexableInterface;
use FOS\ElasticaBundle\Provider\ProviderInterface;
use Sygefor\Bundle\TrainingBundle\Entity\Training;
use Sygefor\Bundle\TrainingBundle\Model\SemesteredTraining;

/**
 * Class SemesteredTrainingProvider
 * @package Sygefor\Bundle\TrainingBundle\Providers
 */
class SemesteredTrainingProvider extends Provider
{

    /**
     * @param ObjectPersisterInterface $persister
     * @param ManagerRegistry $registry
     */
    public function __construct(ObjectPersisterInterface $objectPersister, IndexableInterface $indexable, ManagerRegistry $managerRegistry)
    {
        $options = array(
          'indexName' => 'sygefor3',
          'typeName' => 'semestered_training'
        );
        parent::__construct($objectPersister, $indexable, 'Sygefor\Bundle\TrainingBundle\Entity\Training', $options, $managerRegistry);
    }


    /**
     * @param object $queryBuilder
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchSlice($queryBuilder, $limit, $offset)
    {
        $trainings = parent::fetchSlice($queryBuilder, $limit, $offset);
        $semTrains = array();

        foreach ($trainings as $train) {

            $tmpSemTrains = SemesteredTraining::getSemesteredTrainingsForTraining($train);
            $semTrains = array_merge($semTrains,$tmpSemTrains);
        }

        return $semTrains;
    }

}
