<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Helper\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected ObjectRepository $repository;
    protected EntityManagerInterface $entityManager;
    protected EntidadeFactory $factory;
    protected ExtratorDadosRequest $extratorDadosRequest;
    protected CacheItemPoolInterface $cache;
    protected LoggerInterface $logger;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function create(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $entidade->getId());
        $cacheItem->set($entidade);
        $this->cache->save($cacheItem);

        $this->logger->notice(
            'Novo registro de {entidade} adicionado com id: {id}',
            [
                'entidade' => get_class($entidade),
                'id' => $entidade->getId()
            ]
        );

        return new JsonResponse($entidade->jsonSerialize(), 201);
    }

    public function index(Request $request): Response
    {
        $informacoesDeordenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        $informacoesDeFiltro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        [$paginaAtual, $itensPorPagina] = $this->extratorDadosRequest->buscaDadosPaginacao($request);
        
        $entityList = $this->repository->findBy(
            $informacoesDeFiltro,
            $informacoesDeordenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        $fabricaResposta = new ResponseFactory(
            true,
            $entityList,
            200,
            $paginaAtual,
            $itensPorPagina
        );

        return $fabricaResposta->getResponse();
    }

    public function show(int $id): Response
    {
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $this->repository->find($id);

        $fabricaResposta = is_null($entity)
            ? new ResponseFactory(true, $entity, 204)
            : new ResponseFactory(true, $entity);

        return $fabricaResposta->getResponse();
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

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
        $cacheItem->set($entidadeEnviada);
        $this->cache->save($cacheItem);

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

        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new JsonResponse('', 204);
    }

    abstract public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada): void;

    abstract public function cachePrefix(): string;
}
