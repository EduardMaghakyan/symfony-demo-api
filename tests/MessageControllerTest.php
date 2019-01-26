<?php

namespace App\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MessageControllerTest extends TestCase
{
    const API_BASE = 'http://localhost/api/v1';
    
    /**
     * @var Client
     */
    private $client;
    
    public function setUp()
    {
        $this->client = new Client(['http_errors' => false]);
    }
    
    /**
     * Test successfully adding new message.
     */
    public function testAddingMessage()
    {
        $email   = "test@test.com";
        $message = "Some cool message";
        $data    = [
            "email"   => $email,
            "message" => $message,
        ];
        
        $response = $this->client->request(
            'POST',
            self::API_BASE . '/messages/new',
            [
                'json' => $data,
            ]
        );
        
        $this->assertEquals(201, $response->getStatusCode(), 'Expected response code should be 201');
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('uuid', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals($data['email'], $email);
        $this->assertEquals($data['message'], $message);
    }
    
    /**
     * Return appropriate response if empty fields submitted.
     *
     * @dataProvider postFiledProvider
     */
    public function testEmptyPostData($postData)
    {
        $response = $this->client->request(
            'POST',
            'http://localhost/api/v1/messages/new',
            [
                'json' => $postData,
            ]
        );
        $this->assertEquals(400, $response->getStatusCode(), 'Expect 400 since wrong or no data was submitted');
    }
    
    public function postFiledProvider()
    {
        yield [['email' => 'something@somewhere.com']];
        yield [['message' => 'Some cool message without email']];
        yield [['something_else' => 'not important']];
        yield [
            [
                'email'   => 'wrong_email',
                'message' => 'Nice message',
            ],
        ];
        yield [
            [
                'email'   => 'email@email.com',
                'message' => $this->getRandomString(1001),
            ],
        ];
    }
    
    /**
     * @link https://stackoverflow.com/a/13212994
     * @param int $length
     *
     * @return string
     */
    private function getRandomString(int $length = 10): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );
    }
}
