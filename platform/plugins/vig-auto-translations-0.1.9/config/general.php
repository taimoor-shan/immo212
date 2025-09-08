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
    
    // Production Optimization Settings
    'enable_queue_processing' => env('VIG_TRANSLATE_ENABLE_QUEUES', true),
    'queue_name' => env('VIG_TRANSLATE_QUEUE', 'translations'),
    'chunk_processing_enabled' => env('VIG_TRANSLATE_CHUNK_PROCESSING', true),
    'small_batch_threshold' => env('VIG_TRANSLATE_SMALL_BATCH_THRESHOLD', 20),
    'medium_batch_threshold' => env('VIG_TRANSLATE_MEDIUM_BATCH_THRESHOLD', 100),
    'api_rate_limit_delay' => env('VIG_TRANSLATE_API_DELAY', 100), // milliseconds
    'max_retry_attempts' => env('VIG_TRANSLATE_MAX_RETRIES', 3),
    'job_timeout_seconds' => env('VIG_TRANSLATE_JOB_TIMEOUT', 300), // 5 minutes
    'progress_cache_ttl_hours' => env('VIG_TRANSLATE_PROGRESS_TTL', 2),
    'memory_limit_mb' => env('VIG_TRANSLATE_MEMORY_LIMIT', 256),
    'enable_smart_chunking' => env('VIG_TRANSLATE_SMART_CHUNKING', true),
];
