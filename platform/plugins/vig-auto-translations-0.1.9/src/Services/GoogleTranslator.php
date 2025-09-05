<?php

namespace VigStudio\VigAutoTranslations\Services;

use Illuminate\Support\Facades\Log;
use VigStudio\VigAutoTranslations\Contracts\Translator;
use VigStudio\VigAutoTranslations\Services\GoogleTranslate;
use VigStudio\VigAutoTranslations\Exceptions\LargeTextException;
use VigStudio\VigAutoTranslations\Exceptions\RateLimitException;
use VigStudio\VigAutoTranslations\Exceptions\TranslationRequestException;
use VigStudio\VigAutoTranslations\Exceptions\TranslationDecodingException;

/**
 * Google Translate implementation using free API
 *
 * This uses the free Google Translate API endpoint.
 * For production environments with high volume, consider using Google Cloud Translation API instead.
 */
class GoogleTranslator implements Translator
{
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
            $translator = new GoogleTranslate($target, $source);
            $translated = $translator->translate($value);
            
            // Log successful translation for monitoring
            if (config('plugins.vig-auto-translations.general.log_success', false)) {
                Log::info('Google translation successful', [
                    'source' => $source,
                    'target' => $target,
                    'original_length' => strlen($value),
                    'translated_length' => strlen($translated ?? ''),
                ]);
            }
            
            return $translated;
            
        } catch (RateLimitException $e) {
            Log::warning('Google Translate rate limit exceeded', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
            
        } catch (LargeTextException $e) {
            Log::warning('Google Translate text too large', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'text_length' => strlen($value),
            ]);
            
            return null;
            
        } catch (TranslationRequestException $e) {
            Log::error('Google Translate request error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
            
        } catch (TranslationDecodingException $e) {
            Log::error('Google Translate decoding error', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Google Translate unexpected error', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
                'value' => substr($value, 0, 100),
            ]);
            
            return null;
        }
    }
    
    /**
     * Check if Google Translate supports the given language pair
     */
    public function supportsLanguagePair(string $source, string $target): bool
    {
        // Google Translate supports a wide range of languages
        // This is a basic check - Google Translate actually supports auto-detection
        // and most language pairs, so we return true for most cases
        
        // Only reject if codes are clearly invalid
        return !empty(trim($source)) && !empty(trim($target)) && 
               strlen($source) >= 2 && strlen($target) >= 2;
    }
}
