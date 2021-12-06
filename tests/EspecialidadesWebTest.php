<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesWebTest extends WebTestCase
{
    public function testGaranteQueAutenticacaoFalhaSemRequisicao(): void
    {
        static::createClient()->request('GET', '/especialidades');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGaranteQueEspecialidadesSaoListadas(): void
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('GET', '/especialidades', [], [], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        $this->assertResponseIsSuccessful();
    }

    private function login(KernelBrowser $client): string
    {
        $client->request('POST', '/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'usuario' => 'username',
            'senha' => '123456'
        ]));
        
        return json_decode($client->getResponse()->getContent())->access_token;
    }
}
