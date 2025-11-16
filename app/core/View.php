<?php

/**
 * View Class - Xử lý rendering views
 */
class View
{
    private $viewsPath;
    private $data = [];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../views/';
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
}
