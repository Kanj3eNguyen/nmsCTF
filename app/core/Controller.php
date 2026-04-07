<?php

namespace App\Core;

class Controller
{
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . '/../views/' . $path . '.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}