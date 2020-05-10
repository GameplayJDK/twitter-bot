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

/**
 * Class EmojiProviderAbstract
 * @package App\Service\Emoji
 */
abstract class EmojiProviderAbstract implements EmojiProviderInterface
{
    /**
     * @var array|string[]
     */
    protected $data;

    /**
     * EmojiProviderAbstract constructor.
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        if (empty($this->data)) {
            $this->parse();
        }

        return $this->data;
    }

    /**
     * Run the parsing algorithm calling {@link add}.
     */
    protected abstract function parse(): void;

    /**
     * @param string $dataItem
     */
    protected function add(string $dataItem): void
    {
        if (in_array($dataItem, $this->data, true)) {
            return;
        }

        $this->data[] = $dataItem;
    }
}