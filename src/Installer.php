<?php
/**
 * GM Installer.
 * 
 * @link https://gearmagic.ru/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\ComposerPlugin;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class Installer implements LibraryInstaller
{
    public function supports($packageType)
    {
        return $packageType === 'gm-component';
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::uninstall($repo, $package);
    }

    public function getInstallPath(PackageInterface $package)
    {
        $name = explode("/", $package->getName());
        return "modules/{$name[1]}/";
    }
}
