<?php

namespace App\Command;

use Slim\Interfaces\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('routes')
            ->setDescription('Display API routes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->router->getRoutes() as $route) {
            if ($route->getName()) {
                $output->writeln('<fg=cyan;options=bold>' . $route->getName() . '</>');
                $output->writeln('    ' . implode(', ', $route->getMethods()));
                $output->writeln('    ' . $route->getPattern());
                $output->writeln('    ' . $route->getCallable());
                $output->writeln('');
            }
        }

        return 0;
    }
}
