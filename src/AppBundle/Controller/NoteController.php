<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Form\NoteType;
use AppBundle\Token\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/api")
 */
class NoteController extends Controller
{
    /**
     * @Route("/note/{id}", name="getnotepage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTicketAction(Request $request,$id)
    {
        $note = $this->getDoctrine()->getManager()->getRepository(Note::class)->find($id);
        if (!isset($note)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        $data = $this->get('jms_serializer')->serialize($note,'json', SerializationContext::create()->setGroups(array('note_view')));
        return new JsonResponse(json_decode($data),201);
    }
    /**
     * @Route("/ticket/{id}/note", name="addnotepage",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addNoteAction(Request $request,$id)
    {
        $ticket = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->find($id);
        if (!isset($ticket)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        $note = new Note();
        $body = $request->getContent();
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->loadUserByUsername(Token::getUser($request));
        $data = json_decode($body,true);
        $data["userId"] = $user->getId();
        $data["ticketId"] = $ticket->getId();
        $form = $this->createForm(NoteType::class,$note);
        $form->submit($data);
        if(!$form->isValid()){
            return $this->json($this->getErrorMessages($form),'400');
        }
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();
            $note = $this->get('jms_serializer')->serialize($note,'json', SerializationContext::create()->setGroups(array('note_view')));
            return new JsonResponse([
                "note" => json_decode($note),
                "error" => false,
                "message" => "Created successfully"
            ],200);
        }catch (\Exception $e){
            return $this->json(["error"=>true,"message"=>"Server Error"],501);
        }
    }

    /**
     * @Route("/note/{id}", name="deletenotepage",methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTicketAction(Request $request,$id)
    {
        $note = $this->getDoctrine()->getManager()->getRepository(Note::class)->find($id);
        if (!isset($note)) return $this->json(["error"=>true,"message"=>"Not found"],400);
        try{
            $em = $this->getDoctrine()->getManager();
            $em->remove($note);
            $em->flush();
            $data = $this->get('jms_serializer')->serialize($note,'json', SerializationContext::create()->setGroups(array('note_view')));
            return new JsonResponse([
                "error"=>false,
                "message"=>"Deleted successfully",
                "note"=>json_decode($data)
            ],201);
        }catch (\Exception $e){
            return $this->json([
                "error"=>true,
                "message"=>"Server error"
            ],500);
        }
    }

    /**
     * @param Form $form
     * @return array
     */
    private function getErrorMessages(Form $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }
        return $errors;
    }
}
