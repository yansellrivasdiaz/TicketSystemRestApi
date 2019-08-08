<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Form\TicketType;
use AppBundle\Token\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/api")
 */
class TicketController extends Controller
{
    /**
     * @Route("/tickets", name="getticketspage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTicketsAction(Request $request)
    {
        $tickets = $this->getDoctrine()
            ->getManager()
            ->getRepository(Ticket::class)
            ->findAll();
        $data = $this->get('jms_serializer')->serialize($tickets,'json', SerializationContext::create()->setGroups(array('list_ticket')));
       return new JsonResponse(json_decode($data),201);
    }

    /**
     * @Route("/ticket/{id}", name="getticketpage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTicketAction(Request $request,$id)
    {
        $ticket = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->find($id);
        if (!isset($ticket)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        $data = $this->get('jms_serializer')->serialize($ticket,'json', SerializationContext::create()->setGroups(array('ticket_view')));
        return new JsonResponse(json_decode($data),201);
    }

    /**
     * @Route("/ticket", name="postticketpage",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postTicketAction(Request $request)
    {
        $body = $request->getContent();
        $ticket = new Ticket();
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->loadUserByUsername(Token::getUser($request));
        $data = json_decode($body,true);
        $data["userId"] = $user->getId();
        $ticket->setUserId($user);
        $form = $this->createForm(TicketType::class,$ticket);
        $form->submit($data);
        if(!$form->isValid()){
            return $this->json($this->getErrorMessages($form),'400');
        }
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();
            $ticket = $this->get('jms_serializer')->serialize($ticket,'json', SerializationContext::create()->setGroups(array('list_ticket')));
            return new JsonResponse([
                "ticket" => json_decode($ticket),
                "error" => false,
                "message" => "Inserted successfully"
            ],200);
        }catch (\Exception $e){
            return $this->json(["error"=>true,"message"=>"Server Error"],501);
        }
    }

    /**
     * @Route("/ticket/{id}", name="updateticketpage",methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateTicketAction(Request $request,$id)
    {
        $ticket = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->find($id);
        if (!isset($ticket)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        if($ticket->getStatus() == 'Close') return $this->json(["error"=>true,"message"=>"Ticket is close can't be edit!"],201);
        $body = $request->getContent();
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->loadUserByUsername(Token::getUser($request));
        $data = json_decode($body,true);
        $data["userId"] = $user->getId();
        $form = $this->createForm(TicketType::class,$ticket);
        $form->submit($data);
        if(!$form->isValid()){
            return $this->json($this->getErrorMessages($form),'400');
        }
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $ticket = $this->get('jms_serializer')->serialize($ticket,'json', SerializationContext::create()->setGroups(array('list_ticket')));
            return new JsonResponse([
                "ticket" => json_decode($ticket),
                "error" => false,
                "message" => "Updated successfully"
            ],200);
        }catch (\Exception $e){
            return $this->json(["error"=>true,"message"=>"Server Error"],501);
        }
    }
    /**
     * @Route("/ticket/{id}", name="deleteticketpage",methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTicketAction(Request $request,$id)
    {
        $ticket = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->find($id);
        if (!isset($ticket)) return $this->json(["error"=>true,"message"=>"Not found"],400);
        try{
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush();
            $data = $this->get('jms_serializer')->serialize($ticket,'json', SerializationContext::create()->setGroups(array('list_ticket')));
            return new JsonResponse([
                "error"=>false,
                "message"=>"Deleted successfully",
                "ticket"=>json_decode($data)
            ],201);
        }catch (\Exception $e){
            return $this->json([
                "error"=>true,
                "message"=>"Server error"
            ],500);
        }
    }

    /**
     * @Route("/reports/tickets", name="reportsticketspage",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function reportsTicketsAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body,2);
        $startdate = isset($data["startdate"])?$data["startdate"]:null;
        $enddate = isset($data["enddate"])?$data["enddate"]:null;
        $tickets = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->getByDateRange($startdate,$enddate);
        $data = $this->get('jms_serializer')->serialize($tickets,'json', SerializationContext::create()->setGroups(array('ticket_report')));
        return new JsonResponse(json_decode($data),201);
    }

    /**
     * @Route("/ticket/{id}/close", name="closeticketpage",methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function closeTicketAction(Request $request,$id)
    {
        $ticket = $this->getDoctrine()->getManager()->getRepository(Ticket::class)->find($id);
        if (!isset($ticket)) return $this->json(["error"=>true,"message"=>"Not found"],400);
        try{
            $em = $this->getDoctrine()->getManager();
            $ticket->setStatus('Close');
            $now = new \DateTime();
            $diff = $now->diff($ticket->getCreatedAt());
            $daytoHour = (double)($diff->days * 24);
            $hours = $diff->h;
            $minutes = (double)($diff->i /60) + (double) ($diff->s/60/60);
            $totaltime = (double)$hours + (double)$daytoHour + (double)$minutes;
            $ticket->setEndedAt(new \DateTime());
            $ticket->setTimehours($totaltime);
            $em->flush();
            $data = $this->get('jms_serializer')->serialize($ticket,'json', SerializationContext::create()->setGroups(array('ticket_view')));
            return new JsonResponse([
                "error"=>false,
                "message"=>"Ticket closed successfully!",
                "ticket"=>json_decode($data)
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
