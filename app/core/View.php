<?php

/**
 * View Class - Xử lý rendering views
 */
class View
{
    private $viewsPath;
    private $layoutsPath;
    private $data = [];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../views/';
        $this->layoutsPath = __DIR__ . '/../views/layouts/';
    }

    /**
     * Render view với layout
     */
    public function render($view, $data = [], $layout = 'main')
    {
        // Merge data
        $this->data = array_merge($this->data, $data);
        
        // Render view content
        $content = $this->renderPartial($view, $this->data);
        
        // Render với layout
        if ($layout && $layout !== false) {
            return $this->renderWithLayout($content, $layout, $this->data);
        }
        
        return $content;
    }

    /**
     * Render view không có layout
     */
    public function renderPartial($view, $data = [])
    {
        // Merge data
        $this->data = array_merge($this->data, $data);
        
        // Tìm file view
        $viewFile = $this->getViewFile($view);
        
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$view} (File: {$viewFile})");
        }
        
        // Extract data to variables
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        try {
            // Include view file
            include $viewFile;
            
            // Get content
            $content = ob_get_clean();
            
            return $content;
            
        } catch (Exception $e) {
            // Clean buffer on error
            ob_end_clean();
            throw new Exception("Error rendering view {$view}: " . $e->getMessage());
        }
    }

    /**
     * Render với layout
     */
    private function renderWithLayout($content, $layout, $data = [])
    {
        $layoutFile = $this->getLayoutFile($layout);
        
        if (!file_exists($layoutFile)) {
            // Nếu không có layout, trả về content
            return $content;
        }
        
        // Extract data to variables
        extract($data);
        
        // Set content variable for layout
        $this->data['content'] = $content;
        
        // Start output buffering
        ob_start();
        
        try {
            // Include layout file
            include $layoutFile;
            
            // Get content
            $layoutContent = ob_get_clean();
            
            return $layoutContent;
            
        } catch (Exception $e) {
            // Clean buffer on error
            ob_end_clean();
            throw new Exception("Error rendering layout {$layout}: " . $e->getMessage());
        }
    }

    /**
     * Get view file path
     */
    private function getViewFile($view)
    {
        // Remove leading slash if exists
        $view = ltrim($view, '/');
        
        // Convert to file path
        $viewFile = $this->viewsPath . $view . '.php';
        
        return $viewFile;
    }

    /**
     * Get layout file path
     */
    private function getLayoutFile($layout)
    {
        $layoutFile = $this->layoutsPath . $layout . '.php';
        
        return $layoutFile;
    }

    /**
     * Check if view exists
     */
    public function exists($view)
    {
        $viewFile = $this->getViewFile($view);
        return file_exists($viewFile);
    }

    /**
     * Check if layout exists
     */
    public function layoutExists($layout)
    {
        $layoutFile = $this->getLayoutFile($layout);
        return file_exists($layoutFile);
    }

    /**
     * Get all available views
     */
    public function getAvailableViews($directory = '')
    {
        $views = [];
        $searchPath = $this->viewsPath . $directory;
        
        if (is_dir($searchPath)) {
            $files = glob($searchPath . '*.php');
            foreach ($files as $file) {
                $viewName = str_replace([$this->viewsPath, '.php'], '', $file);
                $views[] = $viewName;
            }
        }
        
        return $views;
    }

    /**
     * Get all available layouts
     */
    public function getAvailableLayouts()
    {
        $layouts = [];
        
        if (is_dir($this->layoutsPath)) {
            $files = glob($this->layoutsPath . '*.php');
            foreach ($files as $file) {
                $layoutName = basename($file, '.php');
                $layouts[] = $layoutName;
            }
        }
        
        return $layouts;
    }

    /**
     * Set data
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        
        return $this;
    }

    /**
     * Get data
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
        
        return $this->data[$key] ?? null;
    }

    /**
     * Clear data
     */
    public function clearData()
    {
        $this->data = [];
        return $this;
    }

    /**
     * Render error view
     */
    public function renderError($errorCode = 404, $message = null)
    {
        $errorViews = [
            404 => 'errors/404',
            500 => 'errors/500',
            403 => 'errors/403'
        ];
        
        $view = $errorViews[$errorCode] ?? 'errors/404';
        
        $data = [
            'error_code' => $errorCode,
            'error_message' => $message ?: $this->getDefaultErrorMessage($errorCode),
            'title' => "Error {$errorCode}"
        ];
        
        return $this->render($view, $data, false);
    }

    /**
     * Get default error message
     */
    private function getDefaultErrorMessage($errorCode)
    {
        $messages = [
            404 => 'Trang không tồn tại',
            500 => 'Lỗi máy chủ',
            403 => 'Không có quyền truy cập'
        ];
        
        return $messages[$errorCode] ?? 'Có lỗi xảy ra';
    }

    /**
     * Escape HTML
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Include partial view
     */
    public function include($view, $data = [])
    {
        return $this->renderPartial($view, $data);
    }

    /**
     * Render JSON
     */
    public function renderJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Debug view paths
     */
    public function debug()
    {
        return [
            'views_path' => $this->viewsPath,
            'layouts_path' => $this->layoutsPath,
            'available_views' => $this->getAvailableViews(),
            'available_layouts' => $this->getAvailableLayouts(),
            'current_data' => $this->data
        ];
    }
}
