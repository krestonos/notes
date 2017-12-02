<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class NotesController extends Controller
{
    /**
     * @Route("/", name="allNotes")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notesRepo = $em->getRepository(Note::class);
        $allNotes = $notesRepo->findAll();
        return $this->render('@App/index.html.twig', [
            'notes' => $allNotes,
        ]);
    }
    
    /**
     * @Route("/add", name="createNote")
     */
    public function createAction(Request $request)
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
            $this->addFlash('success', "Notice successfully created");
            $em->flush();
            return $this->redirectToRoute('showNote',[
                'id' => $newNote->getId()
            ]);
        }
        return $this->render('@App/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/show/{id}", name="showNote")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $notesRepo = $em->getRepository(Note::class);
        $note = $notesRepo->find($id);
        if (!$note) {
            $this->addFlash('danger', "Not find notice #{$id}");
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/show.html.twig', [
            'note' => $note,
        ]);
        
    
    }
    
    /**
     * @Route("/delete/{id}", name="deleteNote")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $notesRepo = $em->getRepository(Note::class);
        $note = $notesRepo->find($id);
        if (!$note) {
            $this->addFlash('danger', "Not find notice #{$id}");
        } else {
            $em->remove($note);
            $this->addFlash('warning', "Notice #{$id} was deleted");
            $em->flush();
        }
        return $this->redirectToRoute('allNotes');
    }
}
