<?php

namespace App\Command;

use App\Model\Configuration;
use App\Service\EmojiService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class EveryEmojiEyesCommand
 * @package App\Command
 */
class EveryEmojiCommand extends CommandAbstract
{
    const MESSAGE_FORMAT = '{EMOJI}w{EMOJI}';

    protected static $defaultName = 'bot:every-emoji';

    /**
     * @var EmojiService
     */
    private $emojiService;

    /**
     * EveryEmojiCommand constructor.
     * @param Configuration $configuration
     * @param EmojiService $emojiService
     */
    public function __construct(Configuration $configuration, EmojiService $emojiService)
    {
        parent::__construct($configuration);

        $this->emojiService = $emojiService;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Tweet a random emoji.')
            ->addArgument('format', InputArgument::OPTIONAL, 'The tweet message format.', static::MESSAGE_FORMAT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getArgument('format');

        $message = $this->getMessage($format);

        $io->note("Format:  $format");
        $io->note("Message: $message");

        return (int)$this->sendTweet($message);
    }

    /**
     * @param string $format
     * @return string
     */
    private function getMessage(string $format): string
    {
        $emoji = $this->emojiService
            ->getEmojiRandom();
        return str_replace('{EMOJI}', $emoji, $format);
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

        $this->codebird
            ->statuses_update($query);

        return false;
    }
}
