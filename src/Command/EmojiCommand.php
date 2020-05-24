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

namespace App\Command;

use App\Service\Emoji\EmojiService;
use Codebird\Codebird;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class EmojiCommand
 * @package App\Command
 */
class EmojiCommand extends CommandAbstract
{
    // TODO: Use string instead of int id.

    const ARGUMENT_DEFAULT_FORMAT = '{EMOJI}w{EMOJI}';

    protected static $defaultName = 'bot:emoji';

    /**
     * @var EmojiService
     */
    private $emojiService;

    /**
     * EmojiCommand constructor.
     * @param Codebird $codebird
     * @param EmojiService $emojiService
     */
    public function __construct(Codebird $codebird, EmojiService $emojiService)
    {
        parent::__construct($codebird);

        $this->emojiService = $emojiService;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Tweet a message with a random emoji.')
            ->addArgument('format', InputArgument::OPTIONAL, 'The tweet message format.', static::ARGUMENT_DEFAULT_FORMAT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getArgument('format');

        $message = $this->getMessage($format);

        $io->note("Format:  {$format}");
        $io->note("Message: {$message}");

        return (int)!$this->sendTweet($message);
    }

    /**
     * @param string $message
     * @return bool
     */
    private function sendTweet(string $message): bool
    {
        $query = http_build_query([
            'status' => $message,
        ]);

        $status = $this->codebird
            ->statuses_update($query);

        return isset($status['id']);
    }

    /**
     * @param string $format
     * @return string
     */
    private function getMessage(string $format): string
    {
        $emoji = $this->emojiService
            ->getRandomEmoji();
        return str_replace('{EMOJI}', $emoji, $format);
    }
}
