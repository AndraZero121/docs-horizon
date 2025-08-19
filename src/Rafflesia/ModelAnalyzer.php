<?php

namespace DocsHorizon\Rafflesia;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class ModelAnalyzer
{
    public function extractModels(): array
    {
        $models = [];
        $modelPath = app_path('Models');
        
        if (!File::exists($modelPath)) {
            return $models;
        }
        
        $files = File::allFiles($modelPath);
        
        foreach ($files as $file) {
            $className = 'App\\Models\\' . $file->getFilenameWithoutExtension();
            
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                $instance = $reflection->newInstance();
                
                if (method_exists($instance, 'getFillable')) {
                    $tableName = $instance->getTable() ?? strtolower($file->getFilenameWithoutExtension());
                    $models[$tableName] = $instance->getFillable();
                }
            }
        }
        
        return $models;
    }
}