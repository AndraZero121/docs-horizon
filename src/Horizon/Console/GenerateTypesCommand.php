<?php

namespace AndraZero121\DocsHorizon\Horizon\Console;

use Illuminate\Console\Command;
use AndraZero121\DocsHorizon\Lane\Convert;
use AndraZero121\DocsHorizon\Nova\TypeExtractor;
use AndraZero121\DocsHorizon\Helvetica\FileGenerator;
use AndraZero121\DocsHorizon\Rafflesia\ModelAnalyzer;

class GenerateTypesCommand extends Command
{
    protected $signature = 'docs:horizon';
    protected $description = 'Generate Frontend type definitions from Backend structure';

    public function handle()
    {
        $this->info('🚀 Starting Docs Horizon generation...');
        
        // Analyze models
        $modelAnalyzer = new ModelAnalyzer();
        $models = $modelAnalyzer->extractModels();
        
        // Extract types from controllers and migrations
        $typeExtractor = new TypeExtractor();
        $validationRules = $typeExtractor->extractFromControllers();
        $migrationTypes = $typeExtractor->extractFromMigrations();
        
        // Convert to frontend format
        $converter = new Convert();
        $frontendTypes = $converter->merge($models, $validationRules, $migrationTypes);
        
        // Generate files
        $fileGenerator = new FileGenerator();
        $fileGenerator->createDocsApi($frontendTypes);
        
        $this->info('✅ Docs Horizon generated successfully!');
        $this->info('📁 Files created in: resources/js/docs-api/');
    }
}