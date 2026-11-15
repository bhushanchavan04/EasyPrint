<?php

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/app'): void
    {
        // Extract variables to local scope
        extract($data);

        // Define helper functions available inside templates if not already defined
        $csrfToken = Session::csrfToken();
        $csrfField = '<input type="hidden" name="_csrf" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">';

        $viewFile = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View template not found: {$template} ({$viewFile})");
        }

        // Buffer the view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $layout) . '.php';
        if (!file_exists($layoutFile)) {
            echo $content;
            return;
        }

        // Render layout wrapping the content
        include $layoutFile;
    }
}

