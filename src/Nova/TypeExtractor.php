<?php

namespace DocsHorizon\Nova;

use Illuminate\Support\Facades\File;

class TypeExtractor
{
    public function extractFromControllers(): array
    {
        $validationRules = [];
        $controllerPath = app_path('Http/Controllers');
        
        if (!File::exists($controllerPath)) {
            return $validationRules;
        }
        
        $files = File::allFiles($controllerPath);
        
        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            
            // Extract validation rules patterns
            preg_match_all('/validate\(\[\s*(.*?)\s*\]\)/s', $content, $matches);
            
            foreach ($matches[1] as $match) {
                $rules = $this->parseValidationRules($match);
                $validationRules = array_merge($validationRules, $rules);
            }
        }
        
        return $validationRules;
    }
    
    public function extractFromMigrations(): array
    {
        $migrationTypes = [];
        $migrationPath = database_path('migrations');
        
        if (!File::exists($migrationPath)) {
            return $migrationTypes;
        }
        
        $files = File::allFiles($migrationPath);
        
        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            
            // Extract table creation patterns
            preg_match('/Schema::create\([\'"](\w+)[\'"]/', $content, $tableMatch);
            
            if (!empty($tableMatch[1])) {
                $tableName = $tableMatch[1];
                $columns = $this->extractColumnTypes($content);
                $migrationTypes[$tableName] = $columns;
            }
        }
        
        return $migrationTypes;
    }
    
    private function parseValidationRules(string $rulesString): array
    {
        $rules = [];
        
        preg_match_all('/[\'"](\w+)[\'"][\s]*=>[\s]*[\'"]([^\'"]*)[\'"]/i', $rulesString, $matches);
        
        for ($i = 0; $i < count($matches[1]); $i++) {
            $field = $matches[1][$i];
            $rule = $matches[2][$i];
            
            $type = $this->mapValidationToType($rule);
            $rules[$field] = $type;
        }
        
        return $rules;
    }
    
    private function extractColumnTypes(string $content): array
    {
        $columns = [];
        
        $patterns = [
            '/\$table->string\([\'"](\w+)[\'"]/' => 'string',
            '/\$table->integer\([\'"](\w+)[\'"]/' => 'number',
            '/\$table->boolean\([\'"](\w+)[\'"]/' => 'boolean',
            '/\$table->text\([\'"](\w+)[\'"]/' => 'string',
            '/\$table->json\([\'"](\w+)[\'"]/' => 'object',
            '/\$table->timestamp\([\'"](\w+)[\'"]/' => 'string',
            '/\$table->date\([\'"](\w+)[\'"]/' => 'string',
        ];
        
        foreach ($patterns as $pattern => $type) {
            preg_match_all($pattern, $content, $matches);
            foreach ($matches[1] as $column) {
                $columns[$column] = $type;
            }
        }
        
        return $columns;
    }
    
    private function mapValidationToType(string $rule): string
    {
        if (str_contains($rule, 'string') || str_contains($rule, 'email')) {
            return 'string';
        }
        
        if (str_contains($rule, 'integer') || str_contains($rule, 'numeric')) {
            return 'number';
        }
        
        if (str_contains($rule, 'boolean')) {
            return 'boolean';
        }
        
        if (str_contains($rule, 'array')) {
            return 'array';
        }
        
        return 'string'; // default
    }
}