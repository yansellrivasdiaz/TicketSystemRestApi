<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Token\Token;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 * @package AppBundle\Controller
 * @Route("/api")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="loginpage",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $data = json_decode($request->getContent(),true);
        if(!is_array($data)){
            return $this->json(["message"=>"Invalid credentials","error"=>true],401);
        }
        if(!isset($data["email"]) && !isset($data["password"])){
            return $this->json(["message"=>"Invalid credentials","error"=>true],401);
        }
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => $data["email"]]);
        if (!$user) {
            return $this->json(["message"=>"Invalid credentials","error"=>true],401);
        }
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $data["password"]);
        if (!$isValid) {
            return $this->json(["message"=>"Invalid credentials","error"=>true],401);
        }
        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $user->getEmail(),
                'exp' => time() + 86400 // 1 day expiration
            ]);
        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/verifytoken", name="verifytokenpage",methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyToken(Request $request)
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->loadUserByUsername(Token::getUser($request));
        $user = $this->get('jms_serializer')->serialize($user,'json', SerializationContext::create()->setGroups(array('authenticated_user_info')));
        return $this->json([
            "authenticated"=>true,
            "message"=>"Authenticated",
            "user" => json_decode($user)
        ]);
    }
}
