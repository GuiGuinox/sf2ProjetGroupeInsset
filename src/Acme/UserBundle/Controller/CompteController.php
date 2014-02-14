<?php

namespace Acme\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

use Acme\UserBundle\Form\Type\CompteType;
use Acme\UserBundle\Entity\Compte;


class CompteController extends Controller
{
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $compte = $this->getDoctrine()
            ->getRepository('UserBundle:Compte')
            ->findByUser($user);

        var_dump($compte);
        die();

        if (!$compte)
        {
            throw $this->createNotFoundException(
                'Aucun compte trouvé pour cet id : '.$id
            );
        }

        return $this->render('UserBundle:Compte:show.html.twig');
    }
    public function creerAction(Request $request)
    {
        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $compte = new Compte();
        $compte->setActif('1');
        $user = $this->container->get('security.context')->getToken()->getUser();
        $compte->setUser($user);


        $form = $this->createForm(new CompteType(), $compte);
        $form->add('save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            // fait quelque chose comme sauvegarder la tâche dans la bdd
            $em = $this->getDoctrine()->getManager();
            $reg = $form->getData();
            $em->persist($reg);
            $em->flush();
            $idCompte = $reg->getId();
            return $this->redirect($this->generateUrl('creerOperation', array('id' => $idCompte)));
        }
        return $this->render('UserBundle:Compte:creer.html.twig', array(
            'form' => $form->createView(),
        ));
        /*return $this->render(
            'UserBundle:Default:index.html.twig',
            array('name' => $name)
        );
        /return new Response('<html><body>Hello '.$name.'!</body></html>');*/
    }

    public function modifAction($id){
        $compte = $this->getDoctrine()
            ->getRepository('UserBundle:Compte')
            ->find($id);


        if (!$compte)
        {
            throw $this->createNotFoundException(
                'Aucun produit trouvé pour cet id : '.$id
            );
        }

        $form = $this->createForm(new CompteType(), $compte);
        $form->add('actif', 'checkbox', array(
            'label'     => 'Activez ',
            'required'  => false,
        ) )
            ->add('save', 'submit');


        $request = $this->get('request');
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($compte);
                $em->flush();

                return $this->redirect($this->generateUrl('task_success'));
            }


        }

        return $this->render('UserBundle:Compte:creer.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function supprAction($id)
    {
        $compte = $this->getDoctrine()
            ->getRepository('UserBundle:Compte')
            ->find($id);

        if (!$compte)
        {
            throw $this->createNotFoundException(
                'Aucun produit trouvé pour cet id : '.$id
            );
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($compte);
        $em->flush();
    }

}