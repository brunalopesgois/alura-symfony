<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EspecialidadeRepository $especialidadeRepository;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $especialidadeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->especialidadeRepository = $especialidadeRepository;
    }
    
    /**
     * @Route("/especialidades", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $dadosEmJson = json_decode($dadosRequest);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dadosEmJson->descricao);

        $this->entityManager->persist($especialidade);
        $this->entityManager->flush();

        return new JsonResponse($especialidade->jsonSerialize(), 201);
    }

    /**
     * @Route("/especialidades", methods={"GET"})
     */
    public function index(): Response
    {
        $especialidadeList = $this->especialidadeRepository->findAll();

        return new JsonResponse($especialidadeList);
    }

    /**
     * @Route("/especialidades/{id}", methods={"GET"})
     */
    public function show(int $id): Response
    {
        $especialidade = $this->especialidadeRepository->find($id);

        if (is_null($especialidade)) {
            return new JsonResponse('', 204);
        }

        return new JsonResponse($especialidade->jsonSerialize());
    }

    /**
     * @Route("/especialidades/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $especialidadeExistente = $this->especialidadeRepository->find($id);

        if (is_null($especialidadeExistente)) {
            return new JsonResponse('', 404);
        }

        $dadosEmJson = json_decode($request->getContent());

        $especialidadeExistente
            ->setDescricao($dadosEmJson->descricao);
        $this->entityManager->flush();

        return new JsonResponse($especialidadeExistente->jsonSerialize());
    }

    /**
     * @Route("/especialidades/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $especialidade = $this->especialidadeRepository->find($id);

        if (is_null($especialidade)) {
            return new JsonResponse('', 404);
        }

        $this->entityManager->remove($especialidade);
        $this->entityManager->flush();

        return new JsonResponse('', 204);
    }
}
