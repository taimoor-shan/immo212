<?php

return [
    // AWS Translate Configuration
    'aws_key' => env('AWS_ACCESS_KEY_ID', ''),
    'aws_secret' => env('AWS_SECRET_ACCESS_KEY', ''),
    'aws_region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'aws_version' => 'latest',
    
    // OpenAI/ChatGPT Configuration
    'chatgpt_key' => env('OPENAI_API_KEY', ''),
    'chatgpt_model' => env('OPENAI_MODEL', 'gpt-4.1'), // GPT-4.1 flagship model (2025) with superior performance
    'chatgpt_system_message' => env('OPENAI_SYSTEM_MESSAGE', ''), // Custom system message for translations
    
    // Logging Configuration
    'log_errors' => env('VIG_TRANSLATE_LOG_ERRORS', true),
    'log_success' => env('VIG_TRANSLATE_LOG_SUCCESS', false), // Set to true for debugging
    
    // Cache Configuration
    'cache_enabled' => env('VIG_TRANSLATE_CACHE_ENABLED', true),
    'cache_ttl_days' => env('VIG_TRANSLATE_CACHE_TTL_DAYS', 30),
    
    // Translation Settings
    'default_driver' => env('VIG_TRANSLATE_DRIVER', 'google'),
    'timeout_seconds' => env('VIG_TRANSLATE_TIMEOUT', 30),
    'max_text_length' => env('VIG_TRANSLATE_MAX_LENGTH', 5000), // Characters
    
    // Batch Processing
    'default_batch_size' => env('VIG_TRANSLATE_BATCH_SIZE', 50),
    'max_batch_size' => env('VIG_TRANSLATE_MAX_BATCH_SIZE', 100),
];
