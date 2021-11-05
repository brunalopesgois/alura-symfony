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
    private EntityManagerInterface $entityManager;
    private MedicoFactory $medicoFactory;
    private MedicosRepository $medicoRepository;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicosRepository $medicoRepository
    ) {
        parent::__construct($medicoRepository);
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
        $this->medicoRepository = $medicoRepository;
    }
    
    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);

        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico->jsonSerialize(), 201);
    }

    /**
     * @Route("/medicos/{id}", methods={"GET"})
     */
    public function show(int $id): Response
    {
        $medico = $this->buscaMedico($id);

        if (is_null($medico)) {
            return new JsonResponse('', 204);
        }

        return new JsonResponse($medico->jsonSerialize());
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
        $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome());
        $this->entityManager->flush();

        return new JsonResponse($medicoExistente->jsonSerialize());
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $medico = $this->buscaMedico($id);

        if (is_null($medico)) {
            return new JsonResponse('', 404);
        }

        $this->entityManager->remove($medico);
        $this->entityManager->flush();

        return new JsonResponse('', 204);
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function showBySpecialty(int $especialidadeId): Response
    {
        $medicos = $this->medicoRepository->findBy([
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
        return $this->medicoRepository->find($id);
    }
}
