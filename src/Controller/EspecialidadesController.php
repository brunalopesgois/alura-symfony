<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $especialidadeRepository,
        EspecialidadeFactory $especialidadeFactory
    ) {
        parent::__construct($especialidadeRepository, $entityManager, $especialidadeFactory);
    }

    /**
     * @Route("/especialidades/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $especialidadeExistente = $this->repository->find($id);

        if (is_null($especialidadeExistente)) {
            return new JsonResponse('', 404);
        }

        $dadosRequest = $request->getContent();

        $especialidadeExistente = $this->factory->criarEntidade($dadosRequest);
        $this->entityManager->flush();

        return new JsonResponse($especialidadeExistente->jsonSerialize());
    }
}
