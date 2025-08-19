<?php

namespace AndraZero121\DocsHorizon\Lane;

class Convert
{
    public function merge(array $models, array $validationRules, array $migrationTypes): array
    {
        $merged = [];
        
        // Gabungkan semua sumber data
        $allTables = array_unique(array_merge(
            array_keys($models),
            array_keys($migrationTypes)
        ));
        
        foreach ($allTables as $tableName) {
            $tableTypes = [];
            
            // Prioritas: Migration -> Model -> Validation
            if (isset($migrationTypes[$tableName])) {
                $tableTypes = array_merge($tableTypes, $migrationTypes[$tableName]);
            }
            
            if (isset($models[$tableName])) {
                foreach ($models[$tableName] as $field) {
                    if (!isset($tableTypes[$field])) {
                        $tableTypes[$field] = 'string'; // default
                    }
                }
            }
            
            // Override dengan validation rules jika ada
            foreach ($validationRules as $field => $type) {
                if (str_contains($field, $tableName) || in_array($field, $models[$tableName] ?? [])) {
                    $cleanField = str_replace($tableName . '.', '', $field);
                    $tableTypes[$cleanField] = $type;
                }
            }
            
            if (!empty($tableTypes)) {
                $merged[$tableName] = $tableTypes;
            }
        }
        
        return $merged;
    }
}