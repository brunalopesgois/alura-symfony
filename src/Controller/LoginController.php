<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }
    
    public function login(Request $request): Response
    {
        $dadosEmJson = json_decode($request->getContent());

        if (is_null($dadosEmJson->usuario) || is_null($dadosEmJson->senha)) {
            return new JsonResponse(['erro' => 'Usuário e senha requeridos'], 400);
        }

        /** @var User */
        $user = $this->userRepository->findOneBy([
            'username' => $dadosEmJson->usuario
        ]);

        if (!$this->hasher->isPasswordValid($user, $dadosEmJson->senha)) {
            return new JsonResponse(['erro' => 'Usuário ou senha inválidos'], 401);
        }

        $token = JWT::encode(['username' => $user->getUserIdentifier()], 'chave');

        return new JsonResponse([
            'access_token' => $token
        ]);
    }
}
