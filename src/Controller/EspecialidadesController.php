<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param Especialidade $entidadeExistente
     * @param Especialidade $entidadeEnviada
     */
    public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada): void
    {
        $entidadeExistente
            ->setDescricao($entidadeEnviada->getDescricao());
    }
}
