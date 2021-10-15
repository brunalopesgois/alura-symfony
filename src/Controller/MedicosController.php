<?php

namespace App\Controller;

use App\Entity\Medico;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController
{
    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function createAction(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $dadosEmJson = json_decode($corpoRequisicao);

        $medico = new Medico();
        $medico->crm = $dadosEmJson->crm;
        $medico->nome = $dadosEmJson->nome;

        return new JsonResponse($medico);
    }
}
