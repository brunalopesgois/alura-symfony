<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    private bool $sucesso;
    private $conteudoResposta;
    private ?int $statusResposta;
    private ?int $paginaAtual;
    private ?int $itensPorPagina;
    
    public function __construct(
        bool $sucesso,
        $conteudoResposta,
        ?int $statusResposta = 200,
        ?int $paginaAtual = null,
        ?int $itensPorPagina = null
    ) {
        $this->sucesso = $sucesso;
        $this->conteudoResposta = $conteudoResposta;
        $this->statusResposta = $statusResposta;
        $this->paginaAtual = $paginaAtual;
        $this->itensPorPagina = $itensPorPagina;
    }

    public function getResponse(): JsonResponse
    {
        $conteudoResposta = [
            'sucesso' => $this->sucesso,
            'paginaAtual' => $this->paginaAtual,
            'itensPorPagina' => $this->itensPorPagina,
            'conteudoResposta' => $this->conteudoResposta
        ];

        if (is_null($this->paginaAtual)) {
            unset($conteudoResposta['paginaAtual']);
            unset($conteudoResposta['itensPorPagina']);
        }

        return new JsonResponse($conteudoResposta, $this->statusResposta);
    }
}
