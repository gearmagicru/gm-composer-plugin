<?php
namespace Gm\ComposerPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class Installer extends LibraryInstaller
{
    /**
     * {@inheritdoc}
     */
    public function supports($packageType)
    {
        return $packageType === 'gm-component';
    }

    /**
     * {@inheritdoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $name = explode("/", $package->getName());
        return "modules/{$name[1]}TTTTTTT/";
    }
}
