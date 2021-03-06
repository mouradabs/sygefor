<?php
namespace Sygefor\Bundle\TraineeBundle\Entity\Term;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sygefor\Bundle\TaxonomyBundle\Vocabulary\VocabularyProviderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Sygefor\Bundle\TaxonomyBundle\Entity\AbstractTerm;
use Sygefor\Bundle\TaxonomyBundle\Vocabulary\VocabularyInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Critère d'évaluation
 *
 * @ORM\Table(name="evaluation_criterion")
 * @ORM\Entity
 */
class EvaluationCriterion extends AbstractTerm implements VocabularyInterface
{
    /**
     * @return string
     */
    public function getVocabularyName()
    {
        return "Critère d'évaluation";
    }

    public static function getVocabularyStatus()
    {
        return VocabularyInterface::VOCABULARY_NATIONAL;
    }
}
