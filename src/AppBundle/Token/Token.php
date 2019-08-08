<?php
namespace AppBundle\Token;
use Symfony\Component\HttpFoundation\Request;
class Token
{
    public static function getUser(Request $request){
        $token = $request->headers->get('Authorization');
        $token = explode(' ',$token)[1];
        $tokenParts = explode(".",$token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        return $jwtPayload->username;
    }
}