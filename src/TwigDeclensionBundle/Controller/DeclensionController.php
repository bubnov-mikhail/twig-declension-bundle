<?php

namespace Bubnov\TwigDeclensionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\DBALException;

use Bubnov\TwigDeclensionBundle\Entity\Declension;
use Bubnov\TwigDeclensionBundle\Form\Type\DeclensionType;
use Bubnov\TwigDeclensionBundle\Form\Type\FilterDeclensionFormType;

/**
 * Declension controller.
 * @Route("/admin/declension")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DeclensionController extends Controller
{
    /**
     * Lists all Declension entities.
     *
     * @Route("/", name="admin_twig_declension")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(FilterDeclensionFormType::class);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository(Declension::class)->getListQB($form->getData());

        $page = $request->query->get('page', 1);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $page,
            20
        );

        return [
            'pagination' => $pagination,
            'entities' => $pagination,
            'form' => $form->createView(),
        ];
    }
    
    /**
     * Creates a new Declension entity.
     *
     * @Route("/new", name="admin_twig_declension_new")
     * @Template("BubnovTwigDeclensionBundle:Declension:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $entity = new Declension();
        $form = $this->createForm(DeclensionType::class, $entity);
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

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Declension entity.
     *
     * @Route("/{id}/edit", name="admin_twig_declension_edit")
     * @Template()
     */
    public function editAction(Request $request, Declension $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Declension entity.');
        }

        $form = $this->createForm(DeclensionType::class, $entity, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
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

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Deletes a Declension entity.
     *
     * @Route("/{id}/delete", name="admin_twig_declension_delete")
     * @Template()
     */
    public function deleteAction(Request $request, Declension $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Declension entity.');
        }
        
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'twig-declension.deleted');

                return $this->redirectToRoute('admin_twig_declension');
            } catch (\Exception $e) {
                $error = new FormError($this->get('translator')->trans('twig-declension.errors.unknown_error'));
                $form->addError($error);
            }
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Lists all Declension entities.
     *
     * @Route("/guess", name="admin_twig_declension_guess", options={"expose"=true})
     * @Method("POST")
     * @return JsonResponse
     */
    public function guessDeclensionAction(Request $request)
    {
        $infinitive = trim($request->request->get('infinitive'));
        $declensions = $this->get('twig.declension')->getDeclensions($infinitive);

        if (!$infinitive || !$declensions) {
            $response = new JsonResponse();
            $response->setData(['status' => 'error']);

            return $response;
        }

        $response = new JsonResponse();
        $response->setData(['status' => 'success', 'declensions' => $declensions]);

        return $response;
    }

    /**
     * Creates a form to delete a Declension entity by id.
     *
     * @param Declension $declension
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Declension $declension)
    {
        return $this->createFormBuilder()
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
