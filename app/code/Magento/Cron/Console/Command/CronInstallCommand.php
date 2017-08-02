<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cron\Console\Command;

use Magento\Framework\Crontab\CrontabManagerInterface;
use Magento\Framework\Crontab\TasksProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Input\InputOption;

/**
 * CronInstallCommand installs Magento cron tasks
 * @since 2.2.0
 */
class CronInstallCommand extends Command
{
    /**
     * @var CrontabManagerInterface
     * @since 2.2.0
     */
    private $crontabManager;

    /**
     * @var TasksProviderInterface
     * @since 2.2.0
     */
    private $tasksProvider;

    /**
     * @param CrontabManagerInterface $crontabManager
     * @param TasksProviderInterface $tasksProvider
     * @since 2.2.0
     */
    public function __construct(
        CrontabManagerInterface $crontabManager,
        TasksProviderInterface $tasksProvider
    ) {
        $this->crontabManager = $crontabManager;
        $this->tasksProvider = $tasksProvider;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * @since 2.2.0
     */
    protected function configure()
    {
        $this->setName('cron:install')
            ->setDescription('Generates and installs crontab for current user')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force install tasks');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @since 2.2.0
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->crontabManager->getTasks() && !$input->getOption('force')) {
            $output->writeln('<error>Crontab has already been generated and saved</error>');
            return Cli::RETURN_FAILURE;
        }

        try {
            $this->crontabManager->saveTasks($this->tasksProvider->getTasks());
        } catch (LocalizedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Crontab has been generated and saved</info>');

        return Cli::RETURN_SUCCESS;
    }
}
