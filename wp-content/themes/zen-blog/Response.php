<?php

class Response
{
    public $status;
    public $message;
    public $data;

    public function __construct($status, $message, $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    public function toResponse()
    {
        return new WP_REST_Response(
            array(
                'status' => $this->status,
                'message' => $this->message,
                'data' => $this->data
            ),
            $this->status === 'success' ? 200 : 400
        );
    }
}
