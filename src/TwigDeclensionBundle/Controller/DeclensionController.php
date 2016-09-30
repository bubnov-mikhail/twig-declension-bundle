<?php

namespace Bubnov\TwigDeclensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\DBALException;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Bubnov\TwigDeclensionBundle\Entity\Declension;
use Bubnov\TwigDeclensionBundle\Form\Type\DeclensionType;
use Bubnov\TwigDeclensionBundle\Form\Type\FilterDeclensionFormType;

/**
 * Declension controller.
 * @Route("/admin/declension")
 */
class DeclensionController extends Controller
{
    /**
     * Creates a new Declension entity.
     *
     * @Route("/new", name="admin_twig_declension_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("BubnovTwigDeclensionBundle:Declension:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Declension();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            try {
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'twig-declension.added');

                return $this->redirectToRoute('admin_twig_declension');
                
            } catch (DBALException $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.errors.already_exist'));
                $form->get('infinitive')->addError($error);
            } catch (\Exception $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.errors.unknown_error'));
                $form->get('infinitive')->addError($error);
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Declension entity.
     *
     * @param Declension $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Declension $entity)
    {
        $form = $this->createForm(new DeclensionType(), $entity, array(
            'action' => $this->generateUrl('admin_twig_declension_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Declension entity.
     *
     * @Route("/new", name="admin_twig_declension_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Declension();
        $form   = $this->createCreateForm($entity);
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Declension entity.
     *
     * @Route("/{id}/edit", name="admin_twig_declension_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BubnovTwigDeclensionBundle:Declension')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Declension entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Declension entity.
    *
    * @param Declension $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Declension $entity)
    {
        $form = $this->createForm(new DeclensionType(), $entity, array(
            'action' => $this->generateUrl('admin_twig_declension_update', ['id' => $entity->getId()]),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Declension entity.
     *
     * @Route("/{id}/edit", name="admin_twig_declension_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("BubnovTwigDeclensionBundle:Declension:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BubnovTwigDeclensionBundle:Declension')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Declension entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            try {
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'twig-declension.edited');

                return $this->redirectToRoute('admin_twig_declension');
            } catch (DBALException $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.already_exist'));
                $form->get('infinitive')->addError($error);
            } catch (\Exception $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.errors.unknown_error'));
                $form->get('infinitive')->addError($error);
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Declension entity.
     *
     * @Route("/{id}/delete", name="admin_twig_declension_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BubnovTwigDeclensionBundle:Declension')->find($id);

        if ($form->isValid()) {
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Declension entity.');
            }
            try {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'twig-declension.deleted');

                return $this->redirectToRoute('admin_twig_declension');
            } catch (\Exception $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.errors.unknown_error'));
                $form->get('infinitive')->addError($error);
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to delete a Declension entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_twig_declension_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Lists all Declension entities.
     * 
     * @Route("/", name="admin_twig_declension")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $form = $this->createForm(new FilterDeclensionFormType());
        $form->bind($this->get('request'));
        $formData = $form->getData();
        
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('BubnovTwigDeclensionBundle:Declension')->getListQB($formData);

        $page = $this->get('request')->query->get('page', 1);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $page,
            20
        );

        return array(
            'pagination' => $pagination,
            'entities' => $pagination,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Lists all Declension entities.
     * 
     * @Route("/guess", name="admin_twig_declension_guess", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @return JsonResponse
     */
    public function guessDeclensionAction(Request $request){
        $infinitive = trim($request->request->get('infinitive'));
        $declensions = $this->get('twig.declension')->getDeclensions($infinitive);
        
        if(!$infinitive || !$declensions){
            $response = new JsonResponse();
            $response->setData(['status' => 'error']);
            
            return $response;
        }
        
        $response = new JsonResponse();
        $response->setData(['status' => 'success', 'declensions' => $declensions]);
        
        return $response;
    }
}
