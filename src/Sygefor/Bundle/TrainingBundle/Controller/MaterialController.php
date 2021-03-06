<?php
/**
 * Created by PhpStorm.
 * User: maxime
 * Date: 10/07/14
 * Time: 15:23
 */

namespace Sygefor\Bundle\TrainingBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sygefor\Bundle\TrainingBundle\Entity\FileMaterial;
use Sygefor\Bundle\TrainingBundle\Entity\LinkMaterial;
use Sygefor\Bundle\TrainingBundle\Entity\Material;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\SecurityExtraBundle\Annotation\SecureParam;


/**
 * @Route("/training/material/")
 */
class MaterialController extends Controller {

    /**
     * @Route("{entity_id}/add/{type_entity}/{material_type}/", name="material.add", options={"expose"=true}, defaults={"_format" = "json", "material_type"="file"})
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function addAction($entity_id, $type_entity, $material_type, Request $request)
    {
        $entity = null;
        switch ($type_entity) {
            case 'internship':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:Internship')->find($entity_id);
                break;
            case 'doctoraltraining':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:DoctoralTraining')->find($entity_id);
                break;
            case 'trainingcourse':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:TrainingCourse')->find($entity_id);
                break;
            case 'diversetraining':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:DiverseTraining')->find($entity_id);
                break;
            case 'meeting':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:Meeting')->find($entity_id);
                break;
            case 'session':
                $entity = $this->getDoctrine()->getRepository('SygeforTrainingBundle:Session')->find($entity_id);
                break;
            default:
                throw \Exception($type_entity . ' is not managed for materials');
        }

        if(!$this->get("security.context")->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException('Accès non autorisé');
        }

        $setEntityMethod = $type_entity === "session" ? 'setSession' : 'setTraining';

        // a file is sent : creating a file material
        if($material_type == "file") {
            $material = new FileMaterial();
            $material->$setEntityMethod($entity);
            $form = $this->createForm('material', $material);

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                $fileInfos = array();
                if ($request->files->count() != 0 ){

                    foreach ($request->files as $file) {
                        //we have to test it in another
                        if ($file[0]->getSize() <= FileMaterial::getMaxFileSize()) {
                            $material = new FileMaterial();

                            $material->$setEntityMethod($entity);
                            $material->setFile($file[0]);

                            $em = $this->getDoctrine()->getManager();

                            //persisting material calls move method on file, that can throw an exception if file size limit
                            //is too small in server config
                            try {
                                $em->persist($material);
                            } catch (FileException $e) {
                                return array('error' => "Le fichier n'a pu être téléchargé");
                            }
                            $em->flush();
                            $fileInfos[] = array('id' => $material->getId(), 'name' => $material->getFileName());
                        } else {
                            $fileInfos[] = array('error' => "fichier trop volumineux", 'name' => $file[0]->getClientOriginalName());
                        }
                    }
                    return array('material' => $fileInfos);
                } else {//files could be stripped by web server (eg by php.ini's limitations) : we can't get any infos about it
                    return array('error' => "Le fichier n'a pu être téléchargé");
                }
            }
        }
        else if($material_type === "link") { // no file sent : a link material is sent
            $material = new LinkMaterial();
            $material->$setEntityMethod($entity);
            $form = $this->createFormBuilder($material)
                ->add('name', 'text', array("label" => "Nom", "required" => "true"))
                ->add('url', 'text', array("label" => "Lien"))
                ->getForm();

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $material->$setEntityMethod($entity);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($material);
                    $em->flush();
                    return array('material' => $material);
                }
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("{id}/remove/", name="material.remove", options={"expose"=true}, defaults={"_format" = "json"})
     * @Rest\View
     * @ParamConverter("material", class="SygeforTrainingBundle:Material", options={"id" = "id"})
     */
    public function deleteAction(Material $material)
    {
        if(($material->getTraining() && $this->get("security.context")->isGranted('EDIT', $material->getTraining())) ||
            ($material->getSession() && $this->get("security.context")->isGranted('EDIT', $material->getSession()))) {
            /** @var $em */
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($material);
                $em->flush();
            }catch (Exception $e) {
                return array("error" => $e->getMessage());
            }
            return array();
        } else {
            throw new AccessDeniedException('Accès non autorisé');
        }
    }

    /**
     * @Route("{id}/get/", name="material.get", options={"expose"=true}, defaults={"_format" = "json"})
     * @Rest\View
     * @ParamConverter("material", class="SygeforTrainingBundle:Material", options={"id" = "id"})
     */
    public function getAction($material)
    {
        if(($material->getTraining() && $this->get("security.context")->isGranted('EDIT', $material->getTraining())) ||
            ($material->getSession() && $this->get("security.context")->isGranted('EDIT', $material->getSession()))) {

            if ($material->getType() == "file"){
                return $material->send();
            }
            else if ($material->getType() == "link") {
                return $material->getUrl();
            }

        } else {
            throw new AccessDeniedException('Accès non autorisé');
        }
    }

}
