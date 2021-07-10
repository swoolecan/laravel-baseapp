<?php

namespace Framework\Baseapp\Commands;

use Swoolecan\Foundation\Commands\TraitGenResourceCommand;

class GenResourceCommand extends AbstractCommand
{
    use TraitGenResourceCommand;

    protected $signature = 'gen:resource';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create classes for a resource';

    public function handle()
    {
        $type = $this->argument('type');
        $options = $this->option('options');
        file_put_contents('/tmp/text.txt', date('Y-m-d H:i:s') . '--'. $type. '==' . $options . 'ssssssssss', FILE_APPEND);
        echo 'sssssssssss';exit();
    }
}
