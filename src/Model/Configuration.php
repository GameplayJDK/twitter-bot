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
    private $twitterConsumerApiKey;

    /**
     * @var string
     */
    private $twitterConsumerApiSecret;

    /**
     * @var string
     */
    private $twitterAccessToken;

    /**
     * @var string
     */
    private $twitterAccessTokenSecret;

    /**
     * @var string
     */
    private $googleCloudKeyFilePath;

    /**
     * Configuration constructor.
     * @param string $twitterConsumerApiKey
     * @param string $twitterConsumerApiSecret
     * @param string $twitterAccessToken
     * @param string $twitterAccessTokenSecret
     * @param string $googleCloudKeyFilePath
     */
    public function __construct(string $twitterConsumerApiKey, string $twitterConsumerApiSecret, string $twitterAccessToken, string $twitterAccessTokenSecret, string $googleCloudKeyFilePath)
    {
        $this->twitterConsumerApiKey = $twitterConsumerApiKey;
        $this->twitterConsumerApiSecret = $twitterConsumerApiSecret;
        $this->twitterAccessToken = $twitterAccessToken;
        $this->twitterAccessTokenSecret = $twitterAccessTokenSecret;
        $this->googleCloudKeyFilePath = $googleCloudKeyFilePath;
    }

    /**
     * @return string
     */
    public function getTwitterConsumerApiKey(): string
    {
        return $this->twitterConsumerApiKey;
    }

    /**
     * @return string
     */
    public function getTwitterConsumerApiSecret(): string
    {
        return $this->twitterConsumerApiSecret;
    }

    /**
     * @return string
     */
    public function getTwitterAccessToken(): string
    {
        return $this->twitterAccessToken;
    }

    /**
     * @return string
     */
    public function getTwitterAccessTokenSecret(): string
    {
        return $this->twitterAccessTokenSecret;
    }

    /**
     * @return string
     */
    public function getGoogleCloudKeyFilePath(): string
    {
        return $this->googleCloudKeyFilePath;
    }
}
