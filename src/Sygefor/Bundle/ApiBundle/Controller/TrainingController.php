<?php
namespace Sygefor\Bundle\ApiBundle\Controller;

use Elastica\Filter\BoolOr;
use Elastica\Filter\Nested;
use Elastica\Filter\Range;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Search;
use Sygefor\Bundle\CoreBundle\Search\SearchService;
use Sygefor\Bundle\TrainingBundle\Entity\DiverseTraining;
use Sygefor\Bundle\TrainingBundle\Entity\Internship;
use Sygefor\Bundle\TrainingBundle\Entity\Meeting;
use Sygefor\Bundle\TrainingBundle\Entity\SingleSessionTraining;
use Sygefor\Bundle\TrainingBundle\Entity\Training;
use Sygefor\Bundle\TrainingBundle\Entity\Session;
use Sygefor\Bundle\TrainingBundle\Entity\TrainingCourse;
use Sygefor\Bundle\TrainingBundle\Form\DiverseTrainingType;
use Sygefor\Bundle\TrainingBundle\Form\InternshipType;
use Sygefor\Bundle\TrainingBundle\Form\MeetingType;
use Sygefor\Bundle\TrainingBundle\Form\SessionType;
use Sygefor\Bundle\TrainingBundle\Form\TrainingCourseType;
use Sygefor\Bundle\TrainingBundle\Form\TrainingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/training")
 */
class TrainingController extends AbstractController
{
    static protected $authorizedFields = array(
        'session' => array(
          'id',
          'dateBegin',
          'dateEnd',
          'year',
          'semester',
          'semesterLabel',
          'limitRegistrationDate',
          'hourDuration',
          'numberOfParticipants',
          'price',
          'maximumNumberOfRegistrations',
          'place',
          'registration',
          'displayOnline',
          'availablePlaces',
          'participations',
          'schedule',
          'promote',
          'collegeYear',
          'collegeSemester'
        ),
        'training' => array(
          'id',
          'type',
          'typeLabel',
          'organization',
          'number',
          'serial',
          'theme',
          'tags',
          'name',
          'objectives',
          'program',
          'prerequisite',
          'interventionType',
          'publicTypes',
          'resources',
          'firstSessionPeriodSemester',
          'firstSessionPeriodYear',
          'doctoralSchools',
          'doctoralYears',
          'applicantOrganism',
          'ects',
          'collegeYear',
          'collegeSemester'
        )
    );

    /**
     * @Route("", name="api.training.search", defaults={"_format" = "json"})
     * @Rest\View(serializerGroups={"api", "api.training"}, serializerEnableMaxDepthChecks=true)
     */
    public function trainingSearchAction(Request $request)
    {
        /** @var SearchService $search */
        $search = $this->get('sygefor_training.search');
        $search->handleRequestBody($request);

        // limit available source fields
        $search->setSource(array_merge(
            self::buildAuthorizedFieldsArray('training'),
            self::buildAuthorizedFieldsArray('session', 'sessions')
        ));

        // the training must contain at least one session online displayed
        $filter = new Term(array('sessions.displayOnline' => true));
        $search->filterQuery($filter);

        return $search->search();
    }

    /**
     * @Route("/session", name="api.training.session.search", defaults={"_format" = "json"})
     * @Rest\View(serializerGroups={"api", "api.training"}, serializerEnableMaxDepthChecks=true)
     */
    public function sessionSearchAction(Request $request)
    {
        /** @var SearchService $search */
        $search = $this->get('sygefor_training.session.search');
        $search->handleRequestBody($request);
        $includePrivate = !!$request->query->get('private');

        // limit available source fields
        $search->setSource(array_merge(
          self::buildAuthorizedFieldsArray('session'),
          self::buildAuthorizedFieldsArray('training', 'training')
        ));

        // filter session by displayOnline
        $onlineFilter = new Term(array('displayOnline' => true));

        // include private sessions
        if ($includePrivate) {
            $orFilter = new BoolOr();
            $privateFilter = new Term(array('registration' => Session::REGISTRATION_PRIVATE));
            $orFilter->addFilter($onlineFilter);
            $orFilter->addFilter($privateFilter);
            $search->filterQuery($orFilter);
        }
        else {
            $search->filterQuery($onlineFilter);
        }

        return $search->search();
    }

    /**
     * Training REST API
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api.training.detail", defaults={"_format" = "json"})
     * @ParamConverter("training", class="SygeforTrainingBundle:Training", options={"id" = "id"})
     * @Rest\View(serializerGroups={"api", "api.training"}, serializerEnableMaxDepthChecks=true)
     */
    public function trainingAction(Training $training)
    {
        // only training with a online displayed session
        /** @var Session $session */
        foreach ($training->getSessions() as $session) {
            if ($session->getDisplayOnline() || $session->getRegistration() === Session::REGISTRATION_PRIVATE) {
                return $training;
            }
        }
        throw new NotFoundHttpException();
    }
}
