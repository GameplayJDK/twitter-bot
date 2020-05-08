<?php

namespace App\Command;

use App\Model\Configuration;
use Codebird\Codebird;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Command
 */
abstract class CommandAbstract extends Command
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Codebird|null
     */
    protected $codebird;

    /**
     * CommandAbstract constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        parent::__construct(null);
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        Codebird::setConsumerKey($this->configuration->getConsumerApiKey(),
            $this->configuration->getConsumerApiSecret());

        $this->codebird = Codebird::getInstance();
        $this->codebird
            ->setToken($this->configuration->getAccessToken(), $this->configuration->getAccessTokenSecret());
    }
}
