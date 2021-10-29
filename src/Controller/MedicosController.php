<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MedicoFactory $medicoFactory;
    
    public function __construct(EntityManagerInterface $entityManager, MedicoFactory $medicoFactory)
    {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
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
     * @Route("/medicos", methods={"GET"})
     */
    public function index(): Response
    {
        $repositorioDeMedicos = $this->getDoctrine()->getRepository(Medico::class);

        $medicoList = $repositorioDeMedicos->findAll();

        return new JsonResponse($medicoList);
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

        $medicoExistente->crm = $medicoEnviado->crm;
        $medicoExistente->nome = $medicoEnviado->nome;
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
     * @param integer $id
     * @return Medico|null
     */
    private function buscaMedico(int $id)
    {
        $repositorioDeMedicos = $this->getDoctrine()->getRepository(Medico::class);
        /** @var Medico */
        $medico = $repositorioDeMedicos->find($id);

        return $medico;
    }
}
