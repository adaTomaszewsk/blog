<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;


class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if(null === $user){
            return $this->json(
                ['message' => 'missing credentials'],
                Response::HTTP_UNAUTHORIZED
            );

        }
        $token = '1234';

        return $this->json([
            'user' => $user-> getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
