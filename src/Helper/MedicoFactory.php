<?php

namespace App\Helper;

use App\Entity\Medico;

class MedicoFactory
{
    public static function criarMedico(string $json): Medico
    {
        $dadosDoMedico = json_decode($json);
        
        $medico = new Medico();
        $medico->crm = $dadosDoMedico->crm;
        $medico->nome = $dadosDoMedico->nome;

        return $medico;
    }
}
