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
 * Class EmojiService
 *
 * @package App\Service
 */
class EmojiService
{
    /**
     * @var EmojiProviderInterface
     */
    private $emojiProvider;

    /**
     * @var array|string[]
     */
    private $data;

    /**
     * EmojiService constructor.
     * @param EmojiProviderInterface $emojiProvider
     */
    public function __construct(EmojiProviderInterface $emojiProvider)
    {
        $this->emojiProvider = $emojiProvider;
        $this->data = [];
    }

    /**
     * Get a random emoji character from the list of available emojis.
     *
     * @return string
     */
    public function getRandomEmoji(): ?string
    {
        $data = $this->getData();

        /** @var int $index */
        $index = array_rand($data);
        $code = $data[$index] ?? null;

        if (null === $code) {
            return null;
        }

        $entity = static::convertCodeToEntity($code);
        return static::convertEntityToCharacter($entity);
    }

    /**
     * Create an emoji character from given code, if it is available from the list of available emojis.
     *
     * @param string $code
     * @return string|null
     */
    public function createFromCode(string $code): ?string
    {
        $code = strtolower($code);
        $data = $this->getData();

        if (!in_array($code, $data)) {
            return null;
        }

        $entity = static::convertCodeToEntity($code);
        return static::convertEntityToCharacter($entity);
    }

    /**
     * Get and cache the data in memory.
     *
     * @return array|string[]
     */
    private function getData(): array
    {
        if (empty($this->data)) {
            $this->data = $this->emojiProvider
                ->getData();
        }

        return $this->data;
    }

    /**
     * Convert a hex string to an html entity code.
     *
     * @param string $code
     * @return string
     */
    public static function convertCodeToEntity(string $code): string
    {
        return "&#x{$code};";
    }

    /**
     * Convert an html entity to a character.
     *
     * @param string $itemEntity
     * @return string
     */
    public static function convertEntityToCharacter(string $itemEntity): string
    {
        return html_entity_decode($itemEntity, ENT_NOQUOTES, 'UTF-8');
    }
}
