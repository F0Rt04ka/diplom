<?php

namespace App\Command;

use App\Repository\PageRepository;
use App\Repository\ProjectRepository;
use App\Service\ProjectFilesHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanProjectVersionsCommand extends Command
{
    protected static $defaultName = 'app:clean-project-versions';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var ProjectFilesHelper
     */
    private $projectFilesHelper;

    /**
     * CleanProjectVersionsCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @param PageRepository $pageRepository
     * @param ProjectFilesHelper $projectFilesHelper
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProjectRepository $projectRepository,
        ProjectFilesHelper $projectFilesHelper
    ) {
        $this->entityManager = $entityManager;
        $this->projectRepository = $projectRepository;
        $this->projectFilesHelper = $projectFilesHelper;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Cleaning project version info')
            ->addOption('max-versions', null, InputOption::VALUE_OPTIONAL, 'Maximum versions count for project(default 5)', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $maxVersionsCount = $input->getOption('max-versions');

        foreach ($this->projectRepository->getCountVersionsForAll() as $projectId => $projectVersionsCount) {
            if ($projectVersionsCount < $maxVersionsCount) {
                continue;
            }

            $project = $this->projectRepository->findOneById($projectId);
            $maxVersion = $project->getCurrentVersion() - $maxVersionsCount;

            foreach ($project->getPages() as &$page) {
                if ($page->getVersion() <= $maxVersion) {
                    $this->projectFilesHelper->removeProjectVersion(
                        $project->getIdentifier(), $page->getVersion()
                    );
                    $project->removePage($page);
                }
            }
            $this->entityManager->persist($project);
        }

        $this->entityManager->flush();
        $io->success('Clean completed!');
    }
}
