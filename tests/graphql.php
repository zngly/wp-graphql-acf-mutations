<?php

namespace Zngly\ACFM\Tests;

use Dotenv\Dotenv;
use GuzzleHttp\Client;

class Graphql
{

    private static $instance = null;
    private string $endpoint;
    private string $token = "";

    private function __construct()
    {
        // read from then .env file if it exists
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->endpoint = $_ENV['GRAPHQL_ENDPOINT'];

        $this->login();
    }

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self();

        return self::$instance;
    }

    private function login()
    {
        $login_query = '
        mutation Login {
            login(input: {username: "api", password: "vLmDecjmp&L][qgwipjqb34)ZTmMQhUOuT2@8ESUkql:&£aojAF0a87JhawJmCiFKxkP0Kf3aTI$5vJ8xVkBpaQuZKu"}) {
              authToken
            }
          }
        ';

        $response = $this->query($login_query);

        // $this->token = $response->data->login->authToken;

        echo "your new token is: " . $this->token . "\n";
    }

    public function query(string $query): mixed
    {
        $url = $this->endpoint;

        $headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        );

        if ($this->token)
            $headers['Authorization'] = 'Bearer ' . $this->token;


        $body = array(
            'query' => $query,
            // 'variables' => null,
        );

        $client = new Client();

        try {
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => json_encode($body),
            ]);

            $response = json_decode($response->getBody()->getContents());

            echo "response: " . json_encode($response) . "\n";
            // return $body;
        } catch (\Throwable $th) {
            echo "\n\n";
            throw $th;
            echo "\n\n";
            return null;
        }
    }
}
