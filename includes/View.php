<?php

class View {
    public function __construct(private string $viewsPath = __DIR__ . '/../views') {
    }

    public function render(string $template, array $data = []): string {
        extract($data, EXTR_SKIP);
        ob_start();
        require $this->viewsPath . '/' . $template . '.php';
        return ob_get_clean();
    }

    public function renderWithLayout(string $template, array $data = [], string $layout = 'layout'): void {
        $content = $this->render($template, $data);
        echo $this->render($layout, $data + ['content' => $content]);
    }
}
