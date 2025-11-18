<?php

// core/traits/ValidatesRequest.php
trait ValidatesRequest
{
    protected function validate($data, $rules, $redirectBack = true)
    {
        $validator = new Validator($data);

        if (!$validator->validate($rules)) {
            // Nối errors bằng <br> thay vì \n
            $_SESSION['errors'] = $this->convertErrorsToString($validator->errors());
            $_SESSION['old'] = $data;

            if ($redirectBack) {
                $this->redirectBack();
            }

            return false;
        }

        unset($_SESSION['errors']);
        unset($_SESSION['old']);

        return $validator->validated();
    }

    // Nối errors bằng <br> để xuống dòng trong HTML
    private function convertErrorsToString($errors)
    {
        $messages = [];

        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }

        // Nối bằng <br> thay vì \n
        return implode('<br>', $messages);
    }

    protected function redirectBack()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function redirectWithSuccess($url, $message)
    {
        $_SESSION['success'] = $message;
        $this->redirect($url);
    }

    protected function redirectWithError($url, $message)
    {
        $_SESSION['errors'] = $message;
        $this->redirect($url);
    }
}