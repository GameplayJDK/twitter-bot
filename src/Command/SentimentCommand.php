<?php

namespace App\Command;

use App\Model\Sentiment\Tweet as TweetSentiment;
use App\Repository\Sentiment\TweetRepository as TweetRepositorySentiment;
use App\Service\Emoji\EmojiService;
use App\Service\Sentiment\SentimentService;
use Codebird\Codebird;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SentimentCommand
 * @package App\Command
 */
class SentimentCommand extends CommandAbstract
{
    const ARGUMENT_DEFAULT_FORMAT = 'mood: {EMOJI}';

    const OPTION_DEFAULT_LIMIT = 20;

    protected static $defaultName = 'bot:sentiment';

    protected static $moodMatrix = [
        '1f641', // Slightly Frowning Face
        '1f610', // Neutral Face
        '1f642', // Slightly Smiling Face
    ];

    /**
     * @var TweetRepositorySentiment
     */
    private $tweetRepository;

    /**
     * @var EmojiService
     */
    private $emojiService;

    /**
     * SentimentCommand constructor.
     * @param Codebird $codebird
     * @param TweetRepositorySentiment $tweetRepository
     * @param EmojiService $emojiService
     */
    public function __construct(Codebird $codebird, TweetRepositorySentiment $tweetRepository, EmojiService $emojiService)
    {
        parent::__construct($codebird);

        $this->tweetRepository = $tweetRepository;
        $this->emojiService = $emojiService;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Analyse a tweet mention and respond with the corresponding emoji.')
            ->addArgument('format', InputArgument::OPTIONAL, 'The tweet message format.', static::ARGUMENT_DEFAULT_FORMAT)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'The response tweet limit.', static::OPTION_DEFAULT_LIMIT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getArgument('format');
        $limit = $input->getOption('limit');
        $limit = (int)$limit;

        $io->note("Format:  $format");
        $io->note("Limit:   $limit");

        $result = 0;

        $tweetList = $this->tweetRepository
            ->getAll($limit);

        foreach ($tweetList as $tweet) {
            $result += (int)!$this->answerTweet($format, $tweet);
        }

        return $result;
    }

    /**
     * See {@see https://cloud.google.com/natural-language/docs/basics#sentiment_analysis_response_fields this explanation}
     * for more information on the sentiment value analysis.
     *
     * @param string $format
     * @param TweetSentiment $tweet
     * @return bool
     */
    private function answerTweet(string $format, TweetSentiment $tweet): bool
    {
        $message = $this->getMessage($format, $tweet);

        return (null !== $message) && $this->sendTweet($message, $tweet->getId());
    }

    /**
     * @param string $message
     * @param int $replyStatusId
     * @return bool
     */
    private function sendTweet(string $message, int $replyStatusId): bool
    {
        $query = http_build_query([
            'status' /************/ => $message,
            'in_reply_to_status_id' => $replyStatusId,
        ]);

        $status = $this->codebird
            ->statuses_update($query);

        return isset($status['id']);
    }

    /**
     * @param string $format
     * @param TweetSentiment $tweet
     * @return string|null
     */
    private function getMessage(string $format, TweetSentiment $tweet): ?string
    {
        [
            // Score is x.
            SentimentService::KEY_SCORE /*********/ => $score,
            // Magnitude is y.
            SentimentService::KEY_MAGNITUDE /*****/ => $magnitude,
        ] = $tweet->getSentiment();

        $count = count(static::$moodMatrix);

        $value = (($score + 1) / 2);
        $value = ($value * $count);
        $value = floor($value);

        $emojiCode = static::$moodMatrix[$value];
        $emoji = $this->emojiService
            ->createFromCode($emojiCode);

        if (null === $emoji) {
            return null;
        }

        $message = str_replace('{EMOJI}', $emoji, $format);
        if (null !== ($author = $tweet->getAuthor())) {
            $message = "@{$author} {$message}";
        }

        return $message;
    }
}
