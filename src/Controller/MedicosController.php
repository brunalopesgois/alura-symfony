<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Medico $entidadeExistente
     * @param Medico $entidadeEnviada
     */
    public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada): void
    {
        $entidadeExistente
            ->setCrm($entidadeEnviada->getCrm())
            ->setNome($entidadeEnviada->getNome())
            ->setEspecialidade($entidadeEnviada->getEspecialidade());
    }
}
