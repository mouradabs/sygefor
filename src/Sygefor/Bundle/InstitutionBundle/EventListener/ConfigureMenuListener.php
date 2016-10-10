<?php

/**
 * Created by PhpStorm.
 * User: maxime
 * Date: 20/05/14
 * Time: 11:54.
 */
namespace Sygefor\Bundle\InstitutionBundle\EventListener;

use Sygefor\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class ConfigureMenuListener.
 */
class ConfigureMenuListener
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     * @var Router
     */
    private $router;

    /**
     * Construct.
     */
    public function __construct(SecurityContext $securityContext, Router $router) {
        $this->securityContext = $securityContext;
        $this->router          = $router;
    }

    /**
     * @param $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        try {
            if($this->securityContext->isGranted('VIEW', 'SygeforInstitutionBundle:AbstractInstitution')) {
                $menu->addChild('institutions', array(
                    'label' => 'Etablissements',
                    'icon'  => 'university',
                    'uri'   => $this->router->generate('core.index') . '#/institution',
                ));
            }
        } catch(AuthenticationCredentialsNotFoundException $e) {

        }
    }
}