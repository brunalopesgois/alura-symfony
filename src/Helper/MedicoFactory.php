<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory
{
    private EspecialidadeRepository $especialidadeRepository;
    
    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }
    
    public function criarMedico(string $json): Medico
    {
        $dadosDoMedico = json_decode($json);
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
