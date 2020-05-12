<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 GameplayJDK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Service\Sentiment;

use Google\Cloud\Language\LanguageClient;

/**
 * Class SentimentService
 * @package App\Service\Sentiment
 */
class SentimentService
{
    const KEY_SCORE = 'score';
    const KEY_MAGNITUDE = 'magnitude';

    /**
     * @var LanguageClient
     */
    private $languageClient;

    /**
     * SentimentService constructor.
     * @param LanguageClient $languageClient
     */
    public function __construct(LanguageClient $languageClient)
    {
        $this->languageClient = $languageClient;
    }

    /**
     * Analyse the given text using the google natural language api for it's sentiment.
     *
     * @param string $text
     * @return array
     */
    public function analyze(string $text): array
    {
        $annotation = $this->languageClient
            ->analyzeSentiment($text);

        return $annotation->sentiment() ?: [
            static::KEY_SCORE /*******/ => null,
            static::KEY_MAGNITUDE /***/ => null,
        ];
    }
}
