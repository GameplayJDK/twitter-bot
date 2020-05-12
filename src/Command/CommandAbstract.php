<?php

namespace App\Command;

use Codebird\Codebird;
use Symfony\Component\Console\Command\Command;

/**
 * Class TestCommand
 * @package App\Command
 */
abstract class CommandAbstract extends Command
{
    /**
     * @var Codebird|null
     */
    protected $codebird;

    /**
     * CommandAbstract constructor.
     * @param Codebird $codebird
     */
    public function __construct(Codebird $codebird)
    {
        $this->codebird = $codebird;

        parent::__construct(null);
    }
}
