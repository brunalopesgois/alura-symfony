<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected ObjectRepository $repository;
    protected EntityManagerInterface $entityManager;
    protected EntidadeFactory $factory;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }

    public function create(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return new JsonResponse($entidade->jsonSerialize(), 201);
    }

    public function index(Request $request): Response
    {
        $informacoesDeordenacao = $request->query->get('sort');
        
        $entityList = $this->repository->findBy([], $informacoesDeordenacao);

        return new JsonResponse($entityList);
    }

    public function show(int $id): Response
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return new JsonResponse('', 204);
        }

        return new JsonResponse($entity->jsonSerialize());
    }

    public function update(int $id, Request $request): Response
    {
        $entidadeExistente = $this->repository->find($id);

        if (is_null($entidadeExistente)) {
            return new JsonResponse('', 404);
        }

        $dadosRequest = $request->getContent();

        $entidadeEnviada = $this->factory->criarEntidade($dadosRequest);
        $this->atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);
        $this->entityManager->flush();

        return new JsonResponse($entidadeExistente->jsonSerialize());
    }

    public function remove(int $id): Response
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return new JsonResponse('', 404);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse('', 204);
    }

    abstract public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada): void;
}
