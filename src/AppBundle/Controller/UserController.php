<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class UserController extends Controller
{
    /**
     * @Route("/employees", name="getemployeespage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployeesAction(Request $request)
    {
        $employees = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findAll();
        $data = $this->get('jms_serializer')->serialize($employees,'json', SerializationContext::create()->setGroups(array('list_employee')));
       return new JsonResponse(json_decode($data),201);
    }

    /**
     * @Route("/employee/{id}", name="getemployeepage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployeeAction(Request $request,$id)
    {
        $employee = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        if (!isset($employee)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        $data = $this->get('jms_serializer')->serialize($employee,'json', SerializationContext::create()->setGroups(array('employee_view')));
        return new JsonResponse(json_decode($data),201);
    }

    /**
     * @Route("/employee", name="postemployeepage",methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function postEmployeeAction(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $body = $request->getContent();
        $employee = new User();
        $data = json_decode($body,true);
        $form = $this->createForm(UserType::class,$employee );
        $form->submit($data);
        if(!$form->isValid()){
            return $this->json($this->getErrorMessages($form),'400');
        }
        try{
            $employee->setPassword($passwordEncoder->encodePassword($employee,$employee->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($employee);
            $entityManager->flush();
            $user = $this->get('jms_serializer')->serialize($employee,'json', SerializationContext::create()->setGroups(array('list_employee')));
            return new JsonResponse([
                "employee" => json_decode($user),
                "error" => false,
                "message" => "Inserted successfully"
            ],200);
        }catch (\Exception $e){
            return $this->json(["error"=>true,"message"=>"Server Error"],501);
        }
    }

    /**
     * @Route("/employee/{id}", name="updateemployeepage",methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateEmployeeAction(Request $request,User $employee,UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!isset($employee)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        $body = $request->getContent();
        $data = json_decode($body,true);
        $form = $this->createForm(UserType::class,$employee);
        $form->submit($data);
        if(!$form->isValid()){
            return $this->json($this->getErrorMessages($form),'400');
        }
        try{
            if(isset($data["password"]))$employee->setPassword($passwordEncoder->encodePassword($employee,$employee->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $user = $this->get('jms_serializer')->serialize($employee,'json', SerializationContext::create()->setGroups(array('list_employee')));
            return new JsonResponse([
                "employee" => json_decode($user),
                "error" => false,
                "message" => "Updated successfully"
            ],200);
        }catch (\Exception $e){
            return $this->json(["error"=>true,"message"=>"Server Error"],501);
        }
    }

    /**
     * @Route("/employee/{id}/lock", name="blockemployeepage",methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function blockEmployeeAction(Request $request,$id)
    {
        $employee = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        if (!isset($employee)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        try{
            $employee->setIsActive(false);
            $em = $this->getDoctrine()->getManager();
            $em->flush($employee);
            $data = $this->get('jms_serializer')->serialize($employee,'json', SerializationContext::create()->setGroups(array('list_employee')));
            return new JsonResponse([
                "error"=>false,
                "message"=>"Lock successfully",
                "employee"=>json_decode($data)
            ],201);
        }catch (\Exception $e){
            return $this->json([
                "error"=>true,
                "message"=>"Server error"
            ],500);
        }
    }

    /**
     * @Route("/employee/{id}/unlock", name="unlockemployeepage",methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function unlockEmployeeAction(Request $request,$id)
    {
        $employee = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        if (!isset($employee)) return $this->json(["error"=>true,"message"=>"Not result"],400);
        try{
            $employee->setIsActive(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush($employee);
            $data = $this->get('jms_serializer')->serialize($employee,'json', SerializationContext::create()->setGroups(array('list_employee')));
            return new JsonResponse([
                "error"=>false,
                "message"=>"Unlock successfully",
                "employee"=>json_decode($data)
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
