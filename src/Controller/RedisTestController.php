<?php 
// Test redis controller delete after
namespace App\Controller;
use Predis\Client;
use Symfony\Component\HttpFoundation\Response;

class RedisTestController
{
    public function test(): Response
    {
        $redis = new Client($_ENV['REDIS_URL']);
        $redis->set('test_key', 'test_value');

        return new Response('Stored value: ' . $redis->get('test_key'));
    }
}
