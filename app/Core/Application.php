<?php

namespace App\Core;

class Application
{
    private static Application $instance;
    private Router $router;
    private Request $request;
    private Response $response;

    public function __construct()
    {
        self::$instance = $this;
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
    }

    public static function getInstance(): Application
    {
        return self::$instance;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function run(): void
    {
        try {
            $this->router->dispatch($this->request, $this->response);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e): void
    {
        $debug = Env::get('APP_DEBUG', 'false') === 'true';

        if ($this->request->isAjax() || str_starts_with($this->request->getUri(), '/api/')) {
            $payload = [
                'error' => 'Internal Server Error',
                'message' => $debug ? $e->getMessage() : 'An unexpected error occurred.',
            ];
            if ($debug) {
                $payload['trace'] = $e->getTraceAsString();
                $payload['file'] = $e->getFile() . ':' . $e->getLine();
            }
            $this->response->json($payload, 500);
        }

        $this->response->setStatusCode(500);
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile() . ':' . $e->getLine());
        $trace = nl2br(htmlspecialchars($e->getTraceAsString()));

        echo "<!DOCTYPE html><html><head><title>Application Error</title><link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'></head><body class='bg-gray-100 p-8 font-sans'><div class='max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-red-200'><h1 class='text-2xl font-bold text-red-600 mb-2'>Application Exception</h1><p class='text-gray-700 font-semibold mb-4'>{$message}</p>";
        if ($debug) {
            echo "<p class='text-sm text-gray-500 mb-4 font-mono'>{$file}</p><div class='bg-gray-900 text-green-400 p-4 rounded text-xs overflow-x-auto font-mono'>{$trace}</div>";
        }
        echo "</div></body></html>";
    }
}

