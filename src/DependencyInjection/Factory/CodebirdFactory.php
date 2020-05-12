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

namespace App\DependencyInjection\Factory;

use App\Model\Configuration;
use Codebird\Codebird;

/**
 * Class CodebirdFactory
 * @package App\DependencyInjection\Factory
 */
class CodebirdFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * LanguageClientFactory constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Codebird
     */
    public function __invoke(): Codebird
    {
        return $this->create();
    }

    /**
     * @return Codebird
     */
    public function create(): Codebird
    {
        Codebird::setConsumerKey($this->configuration->getTwitterConsumerApiKey(),
            $this->configuration->getTwitterConsumerApiSecret());

        $codebird = Codebird::getInstance();
        $codebird->setToken($this->configuration->getTwitterAccessToken(),
            $this->configuration->getTwitterAccessTokenSecret());
        $codebird->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
        return $codebird;
    }

}
