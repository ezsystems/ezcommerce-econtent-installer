<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Commerce\EContentInstaller\Installer;

use Doctrine\DBAL\Connection;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\PlatformInstallerBundle\Installer\DbBasedInstaller;
use EzSystems\PlatformInstallerBundle\Installer\Installer;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use function sprintf;

/**
 * @internal
 */
class EContentDemoInstaller extends DbBasedInstaller implements Installer
{
    private const INDEX_ECONTENT_COMMAND = 'php -d max_execution_time=-1 bin/console silversolutions:indexecontent --live-core';
    private const ECONTENT_DATA_PROVIDER = 'econtent';

    protected $migrationPath;

    protected $projectRootDir;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var string */
    private $demoDataDumpFilePath;

    /** @var string */
    private $binaryFilesDstPath;

    /** @var string */
    private $binaryFilesSrcPath;

    public function __construct(
        Connection $db,
        ConfigResolverInterface $configResolver,
        string $demoDataDumpFilePath,
        string $binaryFilesSrcPath,
        string $binaryFilesDstPath
    ) {
        parent::__construct($db);

        $this->configResolver = $configResolver;
        $this->demoDataDumpFilePath = $demoDataDumpFilePath;
        $this->binaryFilesDstPath = $binaryFilesDstPath;
        $this->binaryFilesSrcPath = $binaryFilesSrcPath;
    }

    public function importData(): void
    {
        $this->validateDataProviderConfiguration();
        // import demo data
        $this->runQueriesFromFile($this->demoDataDumpFilePath);

        $this->output->writeln('Indexing eContent...');
        $process = Process::fromShellCommandline(self::INDEX_ECONTENT_COMMAND);
        $process->setTimeout(3600);
        $process->setIdleTimeout(600);

        $process->run(
            function (string $type, string $output): void {
                $this->output->write($output, false, OutputInterface::OUTPUT_RAW);
            }
        );
    }

    private function validateDataProviderConfiguration(): void
    {
        $dataProvider = $this->configResolver->getParameter(
            'catalog_data_provider',
            'silver_eshop'
        );
        if (self::ECONTENT_DATA_PROVIDER !== $dataProvider) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" data provider, but "%s" is set. eContent demo data won\'t be ' .
                    'imported. Switch the data provider first.',
                    self::ECONTENT_DATA_PROVIDER,
                    $dataProvider
                )
            );
        }
    }

    public function importSchema(): void
    {
        // No schema to be imported
    }

    public function createConfiguration(): void
    {
        // Unused
    }

    public function importBinaries(): void
    {
        $this->output->writeln(
            sprintf(
                "Copying assets from\n  <comment>%s</comment>\nto\n  <comment>%s</comment>",
                $this->binaryFilesSrcPath,
                $this->binaryFilesDstPath
            )
        );
        $fs = new Filesystem();
        $fs->mirror($this->binaryFilesSrcPath, $this->binaryFilesDstPath);
        $this->output->writeln('<info>Assets copied</info>');
    }
}
