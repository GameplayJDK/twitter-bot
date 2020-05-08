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

namespace App\Model;

/**
 * Class Configuration
 * @package App\Model
 */
class Configuration
{
    /**
     * @var string
     */
    private $consumerApiKey;

    /**
     * @var string
     */
    private $consumerApiSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $accessTokenSecret;

    /**
     * Configuration constructor.
     * @param string $consumerApiKey
     * @param string $consumerApiSecret
     * @param string $accessToken
     * @param string $accessTokenSecret
     */
    public function __construct(string $consumerApiKey, string $consumerApiSecret, string $accessToken, string $accessTokenSecret)
    {
        $this->consumerApiKey = $consumerApiKey;
        $this->consumerApiSecret = $consumerApiSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;
    }

    /**
     * @return string
     */
    public function getConsumerApiKey(): string
    {
        return $this->consumerApiKey;
    }

    /**
     * @return string
     */
    public function getConsumerApiSecret(): string
    {
        return $this->consumerApiSecret;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getAccessTokenSecret(): string
    {
        return $this->accessTokenSecret;
    }
}
