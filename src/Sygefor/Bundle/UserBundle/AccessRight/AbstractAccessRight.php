<?php
/**
 * Auteur: Blaise de Carné - blaise@concretis.com
 */
namespace Sygefor\Bundle\UserBundle\AccessRight;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AbstractAccessRight
 * @package Sygefor\Bundle\UserBundle\AccessRight
 */
abstract class AbstractAccessRight implements AccessRightInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @return string
     */
    public abstract function getLabel();

    /**
     * Checks if the access right supports the given class.
     *
     * @param string
     * @return Boolean
     */
    public abstract function supportsClass($class);

    /**
     * Returns the vote for the given parameters.
     */
    public abstract function isGranted(TokenInterface $token, $object = null, $attribute);

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Checks if the access right supports the given attribute.
     *
     * @param string $attribute
     * @return Boolean
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array('VIEW', 'EDIT', 'ADD', 'REMOVE', 'CREATE', 'DELETE', 'MANAGEDUPLICATE'));
    }
}
