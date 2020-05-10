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

namespace App\Service\Emoji;

use App\Help\HexRangeHandleTrait;
use Help\Directory;
use Help\Preg;

/**
 * Class EmojiProviderTwemoji
 * @package App\Service\Emoji
 */
class EmojiProviderTwemoji extends EmojiProviderAbstract
{
    use HexRangeHandleTrait;

    protected const PATTERN_FILE = '/^(.+)\.svg/';

    private const MATCH_FULL = 'full';
    private const MATCH_NAME = 'name';

    protected const NEEDLE_RANGE = '-';

    /**
     * @var string
     */
    private $path;

    /**
     * EmojiProviderTwemoji constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct();

        $this->path = $path;
    }

    /**
     * See {@see https://github.com/twitter/twemoji/tree/gh-pages/svg twitter/twemoji#gh-pages}.
     */
    protected function parse(): void
    {
        $contentData = Directory::listPattern($this->path, static::PATTERN_FILE);
        if (empty($contentData)) {
            return;
        }

        foreach ($contentData as $contentDataLine) {
            // Only take name part.
            $contentDataLinePart = Preg::match(static::PATTERN_FILE, $contentDataLine, [
                static::MATCH_FULL,
                static::MATCH_NAME,
            ]);
            $contentDataLine = $contentDataLinePart[static::MATCH_NAME] ?? null;
            $contentDataLine = $contentDataLine ?: trim($contentDataLine);
            unset($contentDataLinePart);

            // Skip empty name.
            if (empty($contentDataLine)) {
                continue;
            }

            // Handle range.
            $contentRange = $this->handleRange($contentDataLine, static::NEEDLE_RANGE);
            foreach ($contentRange as $contentRangeItem) {
                $this->add($contentRangeItem);
            }
        }
    }
}
