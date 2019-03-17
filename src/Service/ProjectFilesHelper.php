<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProjectFilesHelper
{
    /**
     * @var string
     */
    private $latexOutputDir;

    /**
     * @var string
     */
    private $symlinkDir;

    public function __construct(ParameterBagInterface $params)
    {
        $latexParams = $params->get('latex');
        $this->latexOutputDir = $params->get('kernel.root_dir') . '/../' . $latexParams['output_dir'];
        $this->symlinkDir = $params->get('kernel.root_dir') . '/../public/images/project/';
    }

    public function getOutputLatexFilePath(string $projectIdentifier, int $projectVersion): string
    {
        return $this->latexOutputDir . '/project/' . $projectIdentifier . '/' . $projectVersion;
    }

    public function createSymlink(string $projectIdentifier, int $projectVersion): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->symlink(
            $this->getOutputLatexFilePath($projectIdentifier, $projectVersion),
            $this->getSymlinkPath($projectIdentifier, $projectVersion)
        );
    }
    private function getSymlinkPath(string $projectIdentifier, int $projectVersion): string
    {
        return $this->symlinkDir . $projectIdentifier . '/' . $projectVersion;
    }

    public function removeProjectVersion(string $projectIdentifier, int $projectVersion)
    {
        $this->checkAndRemoveFile($this->getOutputLatexFilePath($projectIdentifier, $projectVersion));
        $this->checkAndRemoveFile($this->getSymlinkPath($projectIdentifier, $projectVersion));
        if (is_link($this->getSymlinkPath($projectIdentifier, $projectVersion))) {
            unlink($this->getSymlinkPath($projectIdentifier, $projectVersion));
        }
    }

    private function checkAndRemoveFile(string $file)
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($file)) {
            $fileSystem->remove($file);
        }
    }
}