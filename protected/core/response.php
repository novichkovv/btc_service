<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 14.09.17
 * Time: 12:48
 */
class response
{
    private $html = '';
    private $json = [];
    private $headers = [];

    public function withHtml($html)
    {
        $this->html .= $html;
    }

    public function withJson(array $response)
    {
        $this->json = array_merge($this->json, $response);
    }

    public function withStatus($status)
    {
        http_response_code($status);
    }

    public function withHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function withContentType($type)
    {
        header('Content-Type: ' . $type . '; charset=utf-8');
    }

    public function respond()
    {
        foreach ($this->headers as $header => $value) {
            header($header . ': ' . $value);
        }
        if(!$this->json) {
            echo $this->html;
            exit;
        } else {
            if(is_array($this->json)) {
                echo json_encode($this->json);
                exit;
            } else {
                $this->withStatus(500);
                echo json_encode([
                    'status' => 'false',
                    'error' => 'Incorrect Response'
                ]);
            }
        }
    }
}