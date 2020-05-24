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

use App\Model\Search\Tweet;
use App\Repository\Search\TweetRepositoryInterface as SearchTweetRepositoryInterface;
use Codebird\Codebird;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ImageCommand
 * @package App\Command
 */
class ImageCommand extends CommandAbstract
{
    const ARGUMENT_DEFAULT_TEXT = '';
    const OPTION_DEFAULT_LIMIT = 15;

    protected static $defaultName = 'bot:image';

    /**
     * @var SearchTweetRepositoryInterface
     */
    private $tweetRepository;

    /**
     * ImageCommand constructor.
     * @param Codebird $codebird
     * @param SearchTweetRepositoryInterface $tweetRepository
     */
    public function __construct(Codebird $codebird, SearchTweetRepositoryInterface $tweetRepository)
    {
        parent::__construct($codebird);

        $this->tweetRepository = $tweetRepository;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Respond with a picture to a given keyword string.')
            ->addArgument('search', InputArgument::REQUIRED, 'The tweet search query.')
            ->addArgument('image', InputArgument::REQUIRED, 'The image to send as response.')
            ->addOption('text', 't', InputOption::VALUE_OPTIONAL, 'The tweet message text.', static::ARGUMENT_DEFAULT_TEXT)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'The response tweet limit.', static::OPTION_DEFAULT_LIMIT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $search = $input->getArgument('search');
        $image = $input->getArgument('image');
        $text = $input->getOption('text');
        $text = (string)$text;
        $limit = $input->getOption('limit');
        $limit = (int)$limit;

        $io->note("Search:  {$search}");
        $io->note("Image:   {$image}");
        $io->note("Text:    {$text}");
        $io->note("Limit:   {$limit}");

        $result = 0;

        $tweetList = $this->tweetRepository
            ->getAll($limit, $search);

        foreach ($tweetList as $tweet) {
            $result += (int)!$this->answerTweet($image, $text, $tweet);
        }

        return $result;
    }

    /**
     * @param string $image
     * @param string $text
     * @param Tweet $tweet
     * @return bool
     */
    private function answerTweet(string $image, string $text, Tweet $tweet): bool
    {
        $message = $this->getMessage($text, $tweet);

        return (null !== $message) && $this->sendTweet($message, $image, $tweet->getId());
    }

    /**
     * @param string $message
     * @param string $image
     * @param int $replyStatusId
     * @return bool
     */
    private function sendTweet(string $message, string $image, int $replyStatusId): bool
    {
        $mediaId = $this->uploadImage($image);

        if (null === $mediaId) {
            return false;
        }

        $mediaIdList = [
            $mediaId,
        ];

        $query = http_build_query([
            'status' /************/ => $message,
            'media_ids' /*********/ => implode(',', $mediaIdList),
            'in_reply_to_status_id' => $replyStatusId,
        ]);

        $status = $this->codebird
            ->statuses_update($query);

        return isset($status['id']);
    }

    /**
     * @param string $image
     * @return string|null
     */
    private function uploadImage(string $image): ?string
    {
        $query = http_build_query([
            'media' => $image,
        ]);

        $media = $this->codebird
            ->media_upload($query);

        return $media['media_id'] ?? null;
    }

    /**
     * @param string $text
     * @param Tweet $tweet
     * @return string|null
     */
    private function getMessage(string $text, Tweet $tweet): ?string
    {
        $message = $text;

        if (null !== ($author = $tweet->getAuthor())) {
            $message = "@{$author} {$message}";
        }

        return $message;
    }
}
