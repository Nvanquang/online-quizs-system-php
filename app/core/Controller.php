<?php

class Controller
{
    protected $db;
    protected $view;
    protected $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
    }

    /**
     * Render view không có layout
     */
    protected function renderPartial($view, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        return $this->view->renderPartial($view, $this->data);
    }

    /**
     * Redirect đến URL khác
     */
    protected function redirect($url, $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
