<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicosRepository $medicoRepository
    ) {
        parent::__construct($medicoRepository, $entityManager, $medicoFactory);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $medicoExistente = $this->buscaMedico($id);

        if (is_null($medicoExistente)) {
            return new JsonResponse('', 404);
        }

        $corpoRequisicao = $request->getContent();
        $medicoEnviado = $this->factory->criarMedico($corpoRequisicao);

        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome());
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente->jsonSerialize());
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function showBySpecialty(int $especialidadeId): Response
    {
        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);

        if (empty($medicos)) {
            return new JsonResponse('', 204);
        }

        return new JsonResponse($medicos);
    }

    /**
     * @param integer $id
     * @return Medico|null
     */
    private function buscaMedico(int $id)
    {
        return $this->repository->find($id);
    }
}
