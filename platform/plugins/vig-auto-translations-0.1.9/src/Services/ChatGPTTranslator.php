<?php

namespace VigStudio\VigAutoTranslations\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use VigStudio\VigAutoTranslations\Contracts\Translator;

/**
 * ChatGPT/OpenAI Translator using the latest OpenAI API
 *
 * Uses the Chat Completions API with GPT-3.5/4 models for high-quality translations
 * https://platform.openai.com/docs/api-reference/chat
 */
class ChatGPTTranslator implements Translator
{
    /**
     * Default model to use for translations
     * GPT-4.1 is the latest flagship model as of 2025
     */
    protected string $model = 'gpt-4.1';

    /**
     * Maximum tokens for translation response
     * GPT-4.1 supports up to 1 million token context window
     */
    protected int $maxTokens = 2000;

    /**
     * API timeout in seconds (increased for GPT-4.1's enhanced processing)
     */
    protected int $timeout = 45;

    /**
     * Available models with their capabilities (Updated for GPT-4.1 family)
     */
    protected array $availableModels = [
        'gpt-4.1' => [
            'name' => 'GPT-4.1 (Latest Flagship)',
            'description' => 'Superior coding (+21.4% vs GPT-4o), better instruction following (+10.5%), 1M token context, June 2024 knowledge cutoff',
            'max_tokens' => 1000000,
            'context_window' => '1M tokens',
            'cost' => 'Premium',
            'strengths' => ['coding', 'instruction-following', 'long-context', 'multimodal'],
        ],
        'gpt-4.1-mini' => [
            'name' => 'GPT-4.1 Mini',
            'description' => 'Smaller, faster GPT-4.1 variant with excellent performance-to-cost ratio',
            'max_tokens' => 128000,
            'context_window' => '128K tokens',
            'cost' => 'Medium',
            'strengths' => ['speed', 'cost-efficiency', 'coding'],
        ],
        'gpt-4.1-nano' => [
            'name' => 'GPT-4.1 Nano',
            'description' => 'Smallest, lowest-latency GPT-4.1 variant optimized for speed',
            'max_tokens' => 32000,
            'context_window' => '32K tokens',
            'cost' => 'Low',
            'strengths' => ['ultra-low-latency', 'cost-effective', 'basic-tasks'],
        ],
        // Legacy models for backward compatibility
        'gpt-4o' => [
            'name' => 'GPT-4o (Legacy)',
            'description' => 'Previous flagship model - consider upgrading to GPT-4.1',
            'max_tokens' => 4096,
            'context_window' => '128K tokens',
            'cost' => 'High',
            'strengths' => ['multimodal'],
            'deprecated' => true,
        ],
        'gpt-4-turbo' => [
            'name' => 'GPT-4 Turbo (Legacy)',
            'description' => 'Legacy model - GPT-4.1 offers superior performance',
            'max_tokens' => 4096,
            'context_window' => '128K tokens',
            'cost' => 'High',
            'deprecated' => true,
        ],
        'gpt-3.5-turbo' => [
            'name' => 'GPT-3.5 Turbo (Budget)',
            'description' => 'Most cost-effective option for basic translations',
            'max_tokens' => 4096,
            'context_window' => '16K tokens',
            'cost' => 'Low',
            'strengths' => ['cost-effective', 'speed'],
        ],
    ];

    public function translate(string $source, string $target, string $value): string|null
    {
        $apiKey = $this->getApiKey();

        if (empty($apiKey)) {
            Log::warning('ChatGPT API key not configured');
            return null;
        }

        // Skip translation if source and target are the same
        if ($source === $target) {
            return $value;
        }

        // Skip empty values
        if (empty(trim($value))) {
            return $value;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->getModel(),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt($source, $target)
                        ],
                        [
                            'role' => 'user', 
                            'content' => $value
                        ]
                    ],
                    'max_tokens' => $this->maxTokens,
                    'temperature' => 0.3, // Lower temperature for more consistent translations
                    'top_p' => 1,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                ]);

            if ($response->failed()) {
                Log::warning('ChatGPT API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'source' => $source,
                    'target' => $target,
                    'value' => substr($value, 0, 100)
                ]);
                return null;
            }

            $responseData = $response->json();
            
            // Check for API errors
            if (isset($responseData['error'])) {
                Log::error('ChatGPT API error', [
                    'error' => $responseData['error'],
                    'source' => $source,
                    'target' => $target
                ]);
                return null;
            }

            // Extract translated text
            $translated = $responseData['choices'][0]['message']['content'] ?? null;
            
            if (empty($translated)) {
                Log::warning('Empty translation response from ChatGPT');
                return null;
            }

            // Clean up the translation
            $translated = $this->cleanTranslation($translated);
            
            return $translated ?: null;
            
        } catch (\Exception $e) {
            Log::error('ChatGPT translation error', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100)
            ]);
            
            return null;
        }
    }

    /**
     * Get the OpenAI API key from configuration
     */
    protected function getApiKey(): ?string
    {
        return setting('vig_translate_chatgpt_key') 
            ?: config('plugins.vig-auto-translations.general.chatgpt_key')
            ?: env('OPENAI_API_KEY');
    }

    /**
     * Get the model to use for translation
     */
    protected function getModel(): string
    {
        $configuredModel = setting('vig_translate_chatgpt_model') 
            ?: config('plugins.vig-auto-translations.general.chatgpt_model')
            ?: env('OPENAI_MODEL')
            ?: $this->model;
            
        // Validate the model is available
        if (!isset($this->availableModels[$configuredModel])) {
            Log::warning('Invalid ChatGPT model configured, falling back to default', [
                'configured' => $configuredModel,
                'default' => $this->model,
                'available' => array_keys($this->availableModels)
            ]);
            return $this->model;
        }
        
        return $configuredModel;
    }

    /**
     * Generate system prompt for translation
     */
    protected function getSystemPrompt(string $source, string $target): string
    {
        $sourceLanguage = $this->getLanguageName($source);
        $targetLanguage = $this->getLanguageName($target);
        
        // Get custom system message from admin configuration
        $customSystemMessage = setting('vig_translate_chatgpt_system_message');
        
        if (!empty($customSystemMessage)) {
            // Use custom system message with language placeholders
            return str_replace(
                ['{source_language}', '{target_language}', '{source}', '{target}'],
                [$sourceLanguage, $targetLanguage, $source, $target],
                $customSystemMessage
            );
        }
        
        // Enhanced system prompt leveraging GPT-4.1's superior instruction following
        return sprintf(
            'You are an expert professional translator with specialized expertise in %s to %s translations. ' .
            'Your task is to provide accurate, contextually appropriate translations that maintain the exact intent and nuance of the original text.' .
            '\n\nCRITICAL TRANSLATION RULES (follow exactly):' .
            '\n1. OUTPUT FORMAT: Return ONLY the translated text with no explanations, introductions, or additional commentary.' .
            '\n2. PRESERVE FORMATTING: Maintain ALL formatting exactly - HTML tags, markdown syntax, special characters, line breaks, spacing, and indentation must remain identical.' .
            '\n3. PRESERVE VARIABLES: Keep ALL variables completely unchanged including :name, {{variable}}, {variable}, [variable], :variable, %variable%, and any other placeholder patterns.' .
            '\n4. MAINTAIN TONE: Preserve the exact tone, style, formality level, and register of the original text.' .
            '\n5. TECHNICAL TERMS: Use standard industry terminology for %s. For programming terms, use widely accepted translations or keep in English if commonly used untranslated.' .
            '\n6. UI ELEMENTS: For user interface text, use standard terminology familiar to %s users of software applications.' .
            '\n7. CONTEXT AWARENESS: Consider the likely context (web interface, documentation, marketing, etc.) and translate appropriately.' .
            '\n8. CONSISTENCY: Maintain consistency in terminology throughout the translation.' .
            '\n\nRemember: Your response must contain ONLY the translated text - no explanations or meta-commentary.',
            $sourceLanguage,
            $targetLanguage,
            $targetLanguage,
            $targetLanguage
        );
    }

    /**
     * Clean up the translated text
     */
    protected function cleanTranslation(string $translated): string
    {
        // Remove common artifacts from ChatGPT responses
        $translated = trim($translated);
        
        // Remove quotes if the entire translation is wrapped in them
        if (preg_match('/^["\'](.+)["\']$/', $translated, $matches)) {
            $translated = $matches[1];
        }
        
        // Remove any leading/trailing whitespace and normalize spaces
        return Str::squish($translated);
    }

    /**
     * Get human-readable language name from language code
     */
    protected function getLanguageName(string $code): string
    {
        $languages = [
            'en' => 'English',
            'es' => 'Spanish', 
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'tr' => 'Turkish',
            'pl' => 'Polish',
            'nl' => 'Dutch',
            'sv' => 'Swedish',
            'da' => 'Danish',
            'no' => 'Norwegian',
            'fi' => 'Finnish',
            'cs' => 'Czech',
            'hu' => 'Hungarian',
            'ro' => 'Romanian',
            'bg' => 'Bulgarian',
            'hr' => 'Croatian',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'et' => 'Estonian',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'uk' => 'Ukrainian',
            'vi' => 'Vietnamese',
            'th' => 'Thai',
            'id' => 'Indonesian',
            'ms' => 'Malay',
            'tl' => 'Filipino',
        ];

        return $languages[$code] ?? $code;
    }

    /**
     * Set the model to use for translations
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set the maximum tokens for responses
     */
    public function setMaxTokens(int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;
        return $this;
    }

    /**
     * Set the API timeout
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Get available models for admin interface
     */
    public function getAvailableModels(): array
    {
        return $this->availableModels;
    }

    /**
     * Get the currently configured model information
     */
    public function getCurrentModelInfo(): array
    {
        $model = $this->getModel();
        return $this->availableModels[$model] ?? [];
    }

    /**
     * Get default system message template with placeholders
     */
    public function getDefaultSystemMessageTemplate(): string
    {
        return 'You are an expert professional translator with specialized expertise in {source_language} to {target_language} translations. ' .
               'Your task is to provide accurate, contextually appropriate translations that maintain the exact intent and nuance of the original text.\n\n' .
               'CRITICAL TRANSLATION RULES (follow exactly):\n' .
               '1. OUTPUT FORMAT: Return ONLY the translated text with no explanations, introductions, or additional commentary.\n' .
               '2. PRESERVE FORMATTING: Maintain ALL formatting exactly - HTML tags, markdown syntax, special characters, line breaks, spacing, and indentation must remain identical.\n' .
               '3. PRESERVE VARIABLES: Keep ALL variables completely unchanged including :name, {{variable}}, {variable}, [variable], :variable, %variable%, and any other placeholder patterns.\n' .
               '4. MAINTAIN TONE: Preserve the exact tone, style, formality level, and register of the original text.\n' .
               '5. TECHNICAL TERMS: Use standard industry terminology for {target_language}. For programming terms, use widely accepted translations or keep in English if commonly used untranslated.\n' .
               '6. UI ELEMENTS: For user interface text, use standard terminology familiar to {target_language} users of software applications.\n' .
               '7. CONTEXT AWARENESS: Consider the likely context (web interface, documentation, marketing, etc.) and translate appropriately.\n' .
               '8. CONSISTENCY: Maintain consistency in terminology throughout the translation.\n\n' .
               'Remember: Your response must contain ONLY the translated text - no explanations or meta-commentary.';
    }
}
