<?php

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
