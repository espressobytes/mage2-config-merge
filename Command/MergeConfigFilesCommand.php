<?php

namespace Espressobytes\MergeConfigFiles\Command;

use Magento\Framework\Oauth\Exception;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\IntegrationService;
use Magento\Integration\Model\Oauth\Consumer;
use Magento\Integration\Model\Oauth\ConsumerFactory;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Semaio\ConfigImportExport\Model\Processor\ImportProcessorInterface;
use Semaio\ConfigImportExport\Model\File\FinderInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Magento\Framework\Oauth\Helper\Oauth as OauthHelper;
use Magento\Integration\Model\ResourceModel\Oauth\Token\Collection as TokenCollection;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;

class MergeConfigFilesCommand extends Command
{

    /** Command Name */
    const COMMAND_NAME = 'config-merge:merge-files';

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var ImportProcessorInterface */
    private $importProcessor;

    /** @var array */
    private $readers;

    /** @var FinderInterface */
    private $finder;

    /** @var IntegrationFactory */
    private $integrationFactory;

    /** @var IntegrationService */
    private $integrationService;

    /** @var ConsumerFactory */
    private $consumerFactory;

    /** @var OauthHelper */
    private $oauthHelper;

    /** @var TokenFactory */
    private $tokenFactory;

    /** @var TokenCollectionFactory */
    private $tokenCollectionFactory;

    public function __construct(
        ImportProcessorInterface $importProcessor,
        FinderInterface $finder,
        IntegrationFactory $integrationFactory,
        IntegrationServiceInterface $integrationService,
        ConsumerFactory $consumerFactory,
        OauthHelper $oauthHelper,
        TokenFactory $tokenFactory,
        TokenCollectionFactory $tokenCollectionFactory,
        array $readers = []
    )
    {
        $this->importProcessor = $importProcessor;
        $this->readers = $readers;
        $this->finder = $finder;
        parent::__construct();

        $this->integrationFactory = $integrationFactory;
        $this->integrationService = $integrationService;
        $this->consumerFactory = $consumerFactory;
        $this->oauthHelper = $oauthHelper;
        $this->tokenFactory = $tokenFactory;
        $this->tokenCollectionFactory = $tokenCollectionFactory;
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Merge config files for multiple purposes');
        $this->addArgument('orig_file', InputArgument::REQUIRED, 'filename of original config.php');
        $this->addArgument('additional_file', InputArgument::REQUIRED, 'filename of file that should be added (merged) to original config.php');
        $this->addArgument('output_file', InputArgument::REQUIRED, 'filename of output file');
        $this->addArgument('config_dir', InputArgument::OPTIONAL, 'path  to config files (default: app/etc/)', 'app/etc/');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        // $this->writeSection('Start Merging config files ...');

        // Retrieve the arguments
        $origFile = $input->getArgument('orig_file');
        $addFile = $input->getArgument('additional_file');
        $outputFile = $input->getArgument('output_file');
        $dir = $input->getArgument('config_dir');

        $this->mergeFiles($dir . $origFile, $dir . $addFile, $dir . $outputFile);

        $this->writeSection('Merging of config files done!');
    }

    private function mergeFiles($inputFilepath, $addFilepath, $outputFilepath)
    {
        $this->writeSection("Merging $inputFilepath and $addFilepath into $outputFilepath ...");

        $inputArr = include($inputFilepath);
        $addArr = include($addFilepath);

        $outputArr = array_merge_recursive($inputArr, $addArr);

        $outputContent = '<?php return ' . var_export($outputArr, true) . ';';

        file_put_contents($outputFilepath, $outputContent);

    }

    /**
     * @param string $text
     * @param string $style
     */
    private function writeSection($text, $style = 'bg=blue;fg=white')
    {
        $formatter = new FormatterHelper();
        $this->output->writeln(['', $formatter->formatBlock($text, $style, true), '']);
    }

}
