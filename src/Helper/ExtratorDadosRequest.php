<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtratorDadosRequest
{
    public function buscaDadosOrdenacao(Request $request)
    {
        [$informacoesDeordenacao, ] = $this->buscaDadosRequest($request);

        return $informacoesDeordenacao;
    }

    public function buscaDadosFiltro(Request $request)
    {
        [ , $informacoesDefiltro] = $this->buscaDadosRequest($request);

        return $informacoesDefiltro;
    }

    private function buscaDadosRequest(Request $request): array
    {
        $informacoesDeordenacao = $request->query->get('sort');
        $informacoesDeFiltro = $request->query->all();
        unset($informacoesDeFiltro['sort']);

        return [$informacoesDeordenacao, $informacoesDeFiltro];
    }
}
