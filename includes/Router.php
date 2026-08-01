<?php

class Router {
    private array $getRoutes = [];
    private array $postRoutes = [];
    private string $defaultAction = 'home';

    public function addGetRoute(string $path, callable $handler): void {
        $this->getRoutes[$path] = $handler;
    }

    public function addPostRoute(string $path, callable $handler): void {
        $this->postRoutes[$path] = $handler;
    }

    public function dispatch(): array {
        $action = $_GET['action'] ?? $this->defaultAction;
        $data = [];

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $handler = $this->postRoutes[$action] ?? null;
            if ($handler) {
                $result = $handler($_POST);
                if (is_array($result)) {
                    $data = array_merge($data, $result);
                }
                if (isset($result['redirect'])) {
                    return ['redirect' => $result['redirect']];
                }
                if (isset($result['action'])) {
                    $action = $result['action'];
                }
            }
            return ['action' => $action, 'data' => $data];
        }

        // Handle GET requests
        $handler = $this->getRoutes[$action] ?? $this->getRoutes[$this->defaultAction] ?? null;
        if ($handler) {
            $result = $handler($_GET);
            if (is_array($result)) {
                if (isset($result['redirect'])) {
                    return ['redirect' => $result['redirect']];
                }
                if (isset($result['action'])) {
                    $action = $result['action'];
                }
                $data = array_merge($data, $result);
            }
        }

        return [
            'action' => $action,
            'data' => $data
        ];
    }
}
