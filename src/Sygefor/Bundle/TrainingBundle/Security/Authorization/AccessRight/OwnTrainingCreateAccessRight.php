<?php
/**
 * Created by PhpStorm.
 * User: maxime
 * Date: 20/03/14
 * Time: 15:42
 */

namespace Sygefor\Bundle\TrainingBundle\Security\Authorization\AccessRight;

use Sygefor\Bundle\UserBundle\AccessRight\AbstractAccessRight;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OwnTrainingCreateAccessRight extends AbstractAccessRight
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return "Créer les formations de sa propre URFIST";
    }

    /**
     * Checks if the access right supports the given class.
     *
     * @param string
     * @return Boolean
     */
    public function supportsClass($class)
    {
        if ($class == 'Sygefor\Bundle\TrainingBundle\Entity\Training' || $class == 'Sygefor\Bundle\TrainingBundle\Entity\Session') {
            return true;
        }
        try {
            $refl = new \ReflectionClass($class);

            return $refl ? $refl->isSubclassOf('Sygefor\Bundle\TrainingBundle\Entity\Training') : false;
        } catch (\ReflectionException $re){
            return false;
        }
    }

    /**
     * Returns the vote for the given parameters.
     */
    public function isGranted(TokenInterface $token, $object = null, $attribute)
    {
        if ($attribute != 'CREATE') return false;
        if ($object) {
            if (method_exists($object, 'getOrganization')) {
                return ($object->getOrganization() == $token->getUser()->getOrganization());
            } else {
                return ($object->getTraining()->getOrganization() == $token->getUser()->getOrganization());
            }
        } else {
            return true;
        }
    }
}
