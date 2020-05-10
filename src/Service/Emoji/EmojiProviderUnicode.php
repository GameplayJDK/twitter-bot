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
use Help\File;

/**
 * Class EmojiProviderUnicode
 * @package App\Service\Emoji
 */
class EmojiProviderUnicode extends EmojiProviderAbstract
{
    use HexRangeHandleTrait;

    protected const NEEDLE_COMMENT = '#';
    protected const NEEDLE_SEPARATOR = ';';
    protected const NEEDLE_RANGE = '..';

    /**
     * @var string
     */
    private $path;

    /**
     * EmojiProviderUnicode constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct();

        $this->path = $path;
    }

    /**
     * See {@see https://www.unicode.org/Public/UCD/latest/ucd/emoji/emoji-data.txt emoji-data.txt}.
     */
    protected function parse(): void
    {
        $content = File::read($this->path);
        if (empty($content)) {
            return;
        }

        $contentData = explode(PHP_EOL, $content);

        foreach ($contentData as $contentDataLine) {
            $contentDataLine = trim($contentDataLine);

            // Skip empty line.
            if (0 === strlen($contentDataLine)) {
                continue;
            }

            // Skip comment.
            if (0 === strpos($contentDataLine, static::NEEDLE_COMMENT, 0)) {
                continue;
            }

            // Only take first part.
            $contentDataLinePart = explode(static::NEEDLE_SEPARATOR, $contentDataLine);
            $contentDataLine = reset($contentDataLinePart);
            unset($contentDataLinePart);

            // Handle range.
            $contentRange = $this->handleRange($contentDataLine, static::NEEDLE_RANGE);
            foreach ($contentRange as $contentRangeItem) {
                $this->add($contentRangeItem);
            }
        }
    }
}
