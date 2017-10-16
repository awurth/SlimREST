<?php

namespace App\Core\Command;

use Slim\Router;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param array  $restOptions
     */
    public function __construct(Router $router, array $restOptions)
    {
        parent::__construct();

        $this->router = $router;
        $this->options = $restOptions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('routes')
            ->setDescription('Display API routes')
            ->setDefinition([
                new InputOption('markdown', 'm', InputOption::VALUE_NONE, 'Print routes in markdown format')
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdown = $input->getOption('markdown');

        if ($markdown) {
            $this->markdown($input, $output);
        } else {
            $this->text($input, $output);
        }

        return 0;
    }

    public function text(InputInterface $input, OutputInterface $output)
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
    }

    public function markdown(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('# API Routes');
        $output->writeln('');
        $url = $this->options['url'];

        foreach ($this->router->getRoutes() as $route) {
            if ($route->getName()) {
                $methods = implode(', ', $route->getMethods());

                $output->writeln('### `' . $methods . '` [' . $route->getPattern() . '](' . $url . $route->getPattern() . ')');
                $output->writeln('##### ' . $route->getCallable());
                $output->writeln('###### ' . $route->getName());

                $output->writeln('');
            }
        }
    }
}
