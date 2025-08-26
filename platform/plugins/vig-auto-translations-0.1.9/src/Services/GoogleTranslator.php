<?php

namespace VigStudio\VigAutoTranslations\Services;

use VigStudio\VigAutoTranslations\Contracts\Translator;
use VigStudio\VigAutoTranslations\Services\GoogleTranslate;

class GoogleTranslator implements Translator
{
    public function translate(string $source, string $target, string $value): string|null
    {
        return (new GoogleTranslate())
            ->setSource($source)
            ->setTarget($target)
            ->translate($value);
    }
}
