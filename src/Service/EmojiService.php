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

namespace App\Service;

use Help\File;

/**
 * Class EmojiService
 *
 * @package App\Service
 */
class EmojiService
{
    /**
     * @var string
     */
    private $dataPath;

    /**
     * @var array|string[]
     */
    private $data;

    /**
     * EmojiService constructor.
     * @param string $dataPath
     */
    public function __construct(string $dataPath)
    {
        $this->dataPath = $dataPath;
        $this->data = [];

        $this->parse();
    }

    /**
     * See {@see https://www.unicode.org/Public/UCD/latest/ucd/emoji/emoji-data.txt emoji-data.txt}.
     */
    private function parse(): void
    {
        if (!empty($this->data)) {
            return;
        }

        $content = File::read($this->dataPath);
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
            if (0 === strpos($contentDataLine, '#', 0)) {
                continue;
            }

            // Only take first part.
            $contentDataLinePart = explode(';', $contentDataLine);
            $contentDataLine = reset($contentDataLinePart);
            $contentDataLine = trim($contentDataLine);
            unset($contentDataLinePart);

            // Handle range.
            if (false !== strpos($contentDataLine, '..', 0)) {
                $contentDataLinePart = explode('..', $contentDataLine);
                if (2 === count($contentDataLinePart)) {
                    [
                        $rangeBegin,
                        $rangeEnd,
                    ] = $contentDataLinePart;
                    $rangeBegin = hexdec($rangeBegin);
                    $rangeEnd = hexdec($rangeEnd);
                    $range = range($rangeBegin, $rangeEnd);

                    foreach ($range as $rangeItem) {
                        $rangeItem = dechex($rangeItem);

                        $this->add($rangeItem);
                    }
                }
                unset($contentDataLinePart);

                continue;
            }

            $this->add($contentDataLine);
        }
    }

    /**
     * @param string $dataItem
     */
    private function add(string $dataItem): void
    {
        if (in_array($dataItem, $this->data, true)) {
            return;
        }

        $this->data[] = $dataItem;
    }

    /**
     * @param int $itemIndex
     * @return string|null
     */
    private function get(int $itemIndex): ?string
    {
        return $this->data[$itemIndex] ?? null;
    }

    /**
     * @param string $code
     * @return string
     */
    private static function convertCodeToEntity(string $code): string
    {
        return "&#x{$code};";
    }

    /**
     * @param string $itemEntity
     * @return string
     */
    private static function convertEntityToCharacter(string $itemEntity): string
    {
        $item = html_entity_decode($itemEntity, ENT_NOQUOTES, 'UTF-8');
//        $item = utf8_encode($item);
        return $item;
    }

    /**
     * @return string
     */
    public function getEmojiRandom(): ?string
    {
        /** @var int $itemIndex */
        $itemIndex = array_rand($this->data);
        $itemCode = $this->get($itemIndex);

        if (null === $itemCode) {
            return null;
        }

        $itemEntity = $this->convertCodeToEntity($itemCode);
        return $this->convertEntityToCharacter($itemEntity);
    }
}
