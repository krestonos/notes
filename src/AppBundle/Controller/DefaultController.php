<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $notesRepo = $em->getRepository(Note::class);
        $allNotes = $notesRepo->findAll();
        return $this->render('@App/index.html.twig', [
            'notes' => $allNotes,
        ]);
    }
    
    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('text', TextType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $newNote = new Note();
            $formData = $form->getData();
            $newNote->setText($formData['text']);
            $em->persist($newNote);
            $em->flush();
        }
        return $this->render('@App/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/show/{id}")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $notesRepo = $em->getRepository(Note::class);
        $note = $notesRepo->find($id);
        return $this->render('@App/show.html.twig', [
            'note' => $note,
        ]);
        
    
    }
}
