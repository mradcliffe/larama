<?php

namespace Radcliffe\Larama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Display application status or general PHP status.
 */
class AppStatusCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Provides a birds-eye view of the current Laravel installation, if any.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $info = [];

        $app = $this->getApplication();
        if (is_a($app, '\Illuminate\Console\Application')) {
            // Get Laravel application status.
            $container = $this->getApplication()->getLaravel();
            $config = $container->make(\Illuminate\Config\Repository::class);
            $driver = $config->get('database.default');
            $filesystem = $config->get('filesystems.default');

            $info['laravel-version'] = $this->getApplication()->getVersion();
            $info['app-name'] = $config->get('app.name');
            $info['app-env'] = $config->get('app.env');
            $info['app-url'] = $config->get('app.url');
            $info['db-driver'] = $driver;
            $info['db-host'] = $config->get('database.connections.' . $driver . '.host');
            $info['db-port'] = $config->get('database.connections.' . $driver . '.port');
            $info['db-user'] = $config->get('database.connections.' . $driver . '.username');
            $info['db-name'] = $config->get('database.connections.' . $driver . '.database');
            $info['bootstrap'] = 'Successful';
            // @todo Figure out how to get this via the container and not helper
            // methods.
            $info['base-path'] = base_path();
            $info['app-root'] = app_path();
            $info['fs-driver'] = $filesystem;
            $info['fs-root'] = $config->get('filesystems.disks.' . $filesystem . '.root');
         }

         // Get the default fields.
         $info['console-name'] = $this->getApplication()->getName();
         $info['console-version'] = $this->getApplication()->getVersion();
         $info['php-exec'] = PHP_BINARY;
         $ini_file = php_ini_loaded_file();
         $info['php-conf'] = $ini_file ? : 'none';
         $info['php-os'] = PHP_OS;

         $field_map = $this->getFieldMap();
         $max_width = $this->getMaxPadWidth(array_keys($info), $field_map) + 1;
         foreach ($info as $name => $value) {
             $label = str_pad($field_map[$name], $max_width);
             $output->writeln($label . ': ' . $value, OutputInterface::OUTPUT_PLAIN);
         }

         return;
    }

    /**
     * Get the string padding to use from the labels to print.
     *
     * @param array $names
     *   An array of names to print.
     * @param array $fieldMap
     *   The field label map.
     * @return int
     *   The maximum length of any of the labels to print.
     */
    protected function getMaxPadWidth($names, $fieldMap)
    {
        return array_reduce($names, function (&$result, $name) use ($fieldMap) {
            $label = $fieldMap[$name];
            if (strlen($label) > $result) {
                $result = strlen($label);
            }
            return $result;
        }, 0);
    }

    /**
     * Get field-label map.
     *
     * @return array
     *   A map of field names to labels.
     */
    protected function getFieldMap()
    {
        return [
            'laravel-version' => 'Laravel version',
            'app-name' => 'Site name',
            'app-env' => 'Site environment',
            'app-url' => 'Site URI',
            'app-root' => 'Laravel app root',
            'base-path' => 'Laravel root',
            'db-driver' => 'Database driver',
            'db-host' => 'Database hostname',
            'db-port' => 'Database port',
            'db-user' => 'Database username',
            'db-name' => 'Database name',
            'bootstrap' => 'Laravel bootstrap',
            'fs-driver' => 'Filesystem driver',
            'fs-root' => 'Filesystem path',
            'php-exec' => 'PHP executable',
            'php-conf' => 'PHP configuration',
            'php-os' => 'PHP OS',
            'console-name' => 'Console application',
            'console-version' => 'Console version',
        ];
    }
}
