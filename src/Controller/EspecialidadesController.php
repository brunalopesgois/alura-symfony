<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class EspecialidadesController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $especialidadeRepository,
        EspecialidadeFactory $especialidadeFactory,
        ExtratorDadosRequest $extratorDadosRequest,
        CacheItemPoolInterface $cache
    ) {
        parent::__construct(
            $especialidadeRepository,
            $entityManager,
            $especialidadeFactory,
            $extratorDadosRequest,
            $cache
        );
    }

    /**
     * @param Especialidade $entidadeExistente
     * @param Especialidade $entidadeEnviada
     */
    public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada): void
    {
        $entidadeExistente
            ->setDescricao($entidadeEnviada->getDescricao());
    }

    public function cachePrefix(): string
    {
        return 'especialidade_';
    }
}
