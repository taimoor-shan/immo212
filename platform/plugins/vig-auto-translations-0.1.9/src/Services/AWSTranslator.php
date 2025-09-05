<?php

namespace VigStudio\VigAutoTranslations\Services;

use Aws\Exception\AwsException;
use Aws\Translate\TranslateClient;
use Illuminate\Support\Facades\Log;
use VigStudio\VigAutoTranslations\Contracts\Translator;

/**
 * AWS Translate service implementation
 *
 * Uses AWS Translate for professional-grade translation services
 * https://aws.amazon.com/translate/
 */
class AWSTranslator implements Translator
{
    protected ?TranslateClient $client = null;
    
    public function translate(string $source, string $target, string $value): string|null
    {
        // Skip translation if source and target are the same
        if ($source === $target) {
            return $value;
        }

        // Skip empty values
        if (empty(trim($value))) {
            return $value;
        }

        try {
            $client = $this->getClient();
            
            if (!$client) {
                Log::warning('AWS Translate client not properly configured');
                return null;
            }

            $result = $client->translateText([
                'SourceLanguageCode' => $this->normalizeLanguageCode($source),
                'TargetLanguageCode' => $this->normalizeLanguageCode($target),
                'Text' => $value,
                'Settings' => [
                    'Formality' => 'FORMAL', // Use formal tone for professional translations
                ],
            ]);

            if ($result->hasKey('TranslatedText')) {
                $translated = $result->get('TranslatedText');
                
                // Log successful translation for monitoring
                if (config('plugins.vig-auto-translations.general.log_success', false)) {
                    Log::info('AWS translation successful', [
                        'source' => $source,
                        'target' => $target,
                        'original_length' => strlen($value),
                        'translated_length' => strlen($translated),
                    ]);
                }
                
                return $translated;
            }

            Log::warning('AWS Translate returned empty result', [
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
            
        } catch (AwsException $e) {
            Log::error('AWS Translate error', [
                'error_code' => $e->getAwsErrorCode(),
                'error_type' => $e->getAwsErrorType(),
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AWS Translate unexpected error', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
        }
    }

    /**
     * Get or create AWS Translate client instance
     */
    protected function getClient(): ?TranslateClient
    {
        if ($this->client === null) {
            $config = $this->loadAWSConfiguration();
            
            // Validate configuration
            if (empty($config['credentials']['key']) || empty($config['credentials']['secret'])) {
                Log::warning('AWS credentials not configured properly');
                return null;
            }
            
            try {
                $this->client = new TranslateClient($config);
            } catch (\Exception $e) {
                Log::error('Failed to create AWS Translate client', [
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }
        
        return $this->client;
    }

    /**
     * Load the configuration to pass to AWS
     */
    protected function loadAWSConfiguration(): array
    {
        return [
            'version' => config('plugins.vig-auto-translations.general.aws_version', 'latest'),
            'region' => $this->getRegion(),
            'credentials' => [
                'key' => $this->getAccessKey(),
                'secret' => $this->getSecretKey(),
            ],
            'http' => [
                'timeout' => 30, // 30 second timeout
                'connect_timeout' => 10, // 10 second connection timeout
            ],
            // Only disable SSL verification in development
            'http' => array_merge(
                ['timeout' => 30, 'connect_timeout' => 10],
                app()->environment('production') ? [] : ['verify' => false]
            ),
        ];
    }
    
    /**
     * Get AWS access key from configuration
     */
    protected function getAccessKey(): ?string
    {
        return setting('vig_translate_aws_key') 
            ?: config('plugins.vig-auto-translations.general.aws_key')
            ?: env('AWS_ACCESS_KEY_ID');
    }
    
    /**
     * Get AWS secret key from configuration
     */
    protected function getSecretKey(): ?string
    {
        return setting('vig_translate_aws_secret')
            ?: config('plugins.vig-auto-translations.general.aws_secret')
            ?: env('AWS_SECRET_ACCESS_KEY');
    }
    
    /**
     * Get AWS region from configuration
     */
    protected function getRegion(): string
    {
        return setting('vig_translate_aws_region')
            ?: config('plugins.vig-auto-translations.general.aws_region')
            ?: env('AWS_DEFAULT_REGION')
            ?: 'us-east-1';
    }
    
    /**
     * Normalize language codes for AWS Translate
     * AWS Translate uses specific language codes that may differ from standard ISO codes
     */
    protected function normalizeLanguageCode(string $code): string
    {
        // Map common language codes to AWS Translate supported codes
        $languageMap = [
            'zh' => 'zh', // Chinese (Simplified)
            'zh-cn' => 'zh',
            'zh-tw' => 'zh-TW', // Chinese (Traditional)
            'pt' => 'pt', // Portuguese
            'pt-br' => 'pt', // Portuguese (Brazil)
            'pt-pt' => 'pt', // Portuguese (Portugal)
            'no' => 'no', // Norwegian
            'nb' => 'no', // Norwegian Bokmål
            'nn' => 'no', // Norwegian Nynorsk
        ];
        
        return $languageMap[strtolower($code)] ?? strtolower($code);
    }
    
    /**
     * Check if AWS Translate supports the given language pair
     */
    public function supportsLanguagePair(string $source, string $target): bool
    {
        // AWS Translate supported languages (as of 2024)
        $supportedLanguages = [
            'af', 'sq', 'am', 'ar', 'hy', 'az', 'bn', 'bs', 'bg', 'ca', 'zh', 'zh-TW',
            'hr', 'cs', 'da', 'fa-AF', 'nl', 'en', 'et', 'fa', 'tl', 'fi', 'fr', 'fr-CA',
            'ka', 'de', 'el', 'gu', 'ht', 'ha', 'he', 'hi', 'hu', 'is', 'id', 'ga', 'it',
            'ja', 'kn', 'kk', 'ko', 'lv', 'lt', 'mk', 'ms', 'ml', 'mt', 'mr', 'mn', 'no',
            'ps', 'pl', 'pt', 'pt-PT', 'pa', 'ro', 'ru', 'sr', 'si', 'sk', 'sl', 'so',
            'es', 'es-MX', 'sw', 'sv', 'ta', 'te', 'th', 'tr', 'uk', 'ur', 'uz', 'vi', 'cy'
        ];
        
        $normalizedSource = $this->normalizeLanguageCode($source);
        $normalizedTarget = $this->normalizeLanguageCode($target);
        
        return in_array($normalizedSource, $supportedLanguages) && 
               in_array($normalizedTarget, $supportedLanguages);
    }
}
