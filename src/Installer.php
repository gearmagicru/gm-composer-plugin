<?php

namespace Gm\ComposerPlugin;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class Installer extends LibraryInstaller
{
    public function supports($packageType)
    {
        return $packageType === 'gm-component';
    }

    public function getInstallPath(PackageInterface $package)
    {
        $name = explode("/", $package->getName());
        return "modules/{$name[1]}TTTTTTT/";
    }

    /**
     * Implementing the abstract method test()
     */
    public function deactivate(){
        //do something
    }
}
