<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesWebTest extends WebTestCase
{
    private KernelBrowser $client;

    private string $token;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->login($this->client);
    }
    
    public function testGaranteQueAutenticacaoFalhaSemRequisicao(): void
    {
        $this->client->request('GET', '/especialidades');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGaranteQueEspecialidadesSaoListadas(): void
    {
        $this->client->request('GET', '/especialidades', [], [], [
            'HTTP_AUTHORIZATION' => "Bearer $this->token"
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testInsereEspecialidade(): void
    {
        $this->client->request('POST', '/especialidades', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => "Bearer $this->token"
        ], json_encode([
            'descricao' => 'Teste'
        ]));

        $this->assertResponseIsSuccessful();
    }

    private function login(KernelBrowser $client): string
    {
        $client->request(
            'POST',
            '/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'usuario' => 'usuario',
                'senha' => '123456'
            ])
        );
        
        return json_decode($client->getResponse()->getContent())->access_token;
    }
}
