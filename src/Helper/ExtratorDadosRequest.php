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

    public function buscaDadosPaginacao(Request $request)
    {
        [ , , $paginaAtual, $itensPorPagina] = $this->buscaDadosRequest($request);

        return [$paginaAtual, $itensPorPagina];
    }

    private function buscaDadosRequest(Request $request): array
    {
        $dadosOrdenacao = $request->query->get('sort');
        $queryString = $request->query->all();
        unset($queryString['sort']);
        $paginaAtual = array_key_exists('page', $queryString)
            ? $queryString['page']
            : 1;
        unset($queryString['page']);
        $itensPorPagina = array_key_exists('itensPorPagina', $queryString)
            ? $queryString['itensPorPagina']
            : 5;
        unset($queryString['itensPorPagina']);

        return [
            $dadosOrdenacao,
            $queryString,
            $paginaAtual,
            $itensPorPagina
        ];
    }
}
