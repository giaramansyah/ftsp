<?php
namespace App\Library;

class Response 
{
    private $alert = array('error', 'success', 'warning', 'info');

    private $status;

    private $message;

    private $code;

    private $data;

    private $redirect;

    public function __construct($status = false, $message = 'Bad Parameter', $code = 0)
    {
        $this->status = $status;
        $this->message = $message;
        $this->code = $code;
    }

    public function setRedirect($redirect) {
        $this->redirect = $redirect;
        return $this;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function responseJson() {
        $json = array(
            'status' => $this->status, 
            'alert' => $this->alert[$this->code], 
            'message' => $this->message,
        );

        if(isset($this->data)) {
            $json['data'] = $this->data;
        }

        if(isset($this->redirect)) {
            $json['redirect'] = $this->redirect;
        }

        return $json;
    }
    
}