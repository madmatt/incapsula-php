<?php

namespace Incapsula\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitesListCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('sites:list')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output as JSON')
            ->setDescription('List all sites')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->client->sites();
        $sites = [];
        $page = 0;

        while (true) {
            $resp = $api->list(50, $page);
            if (empty($resp['sites'])) {
                break;
            }
            $sites = array_merge($sites, $resp['sites']);
            ++$page;
        }

        if (true === $input->getOption('json')) {
            $output->write(json_encode($sites));

            return 0;
        }

        $table = new Table($output);
        $table->setHeaders(['Site ID', 'Status', 'Domain']);
        foreach ($sites as $site) {
            $table->addRow([$site['site_id'], $site['status'], $site['domain']]);
        }
        $table->render();

        return 0;
    }
}
