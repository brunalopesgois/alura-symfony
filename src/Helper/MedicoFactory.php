<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntidadeFactory
{
    private EspecialidadeRepository $especialidadeRepository;
    
    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }
    
    public function criarEntidade(string $json): Medico
    {
        $dadosDoMedico = json_decode($json);

        if (!property_exists($dadosDoMedico, 'crm')
            || !property_exists($dadosDoMedico, 'nome')
            || !property_exists($dadosDoMedico, 'especialidadeId')
        ) {
            throw new EntityFactoryException();
        }

        $especialidadeId = $dadosDoMedico->especialidade_id;
        $especialidade = $this->especialidadeRepository->find($especialidadeId);
        
        $medico = new Medico();
        $medico
            ->setCrm($dadosDoMedico->crm)
            ->setNome($dadosDoMedico->nome)
            ->setEspecialidade($especialidade);

        return $medico;
    }
}
