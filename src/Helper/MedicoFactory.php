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
        $dadosEmJson = json_decode($json);

        $this->checarPropriedades($dadosEmJson);

        $especialidadeId = $dadosEmJson->especialidade_id;
        $especialidade = $this->especialidadeRepository->find($especialidadeId);
        
        $medico = new Medico();
        $medico
            ->setCrm($dadosEmJson->crm)
            ->setNome($dadosEmJson->nome)
            ->setEspecialidade($especialidade);

        return $medico;
    }

    private function checarPropriedades(object $dadosEmJson)
    {
        if (!property_exists($dadosEmJson, 'crm')
            || !property_exists($dadosEmJson, 'nome')
            || !property_exists($dadosEmJson, 'especialidadeId')
        ) {
            throw new EntityFactoryException();
        }
    }
}
