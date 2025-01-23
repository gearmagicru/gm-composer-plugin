<?php
namespace Gm\ComposerPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;


class Installer extends LibraryInstaller
{
    protected string $packageType = '';

    protected array $packageMap = [
        'gm-component' => '/modules/gm/{name}'
    ];

    /**
     * {@inheritdoc}
     */
    public function supports(string $packageType)
    {
        $this->packageType = $packageType;
        return $packageType === 'gm-component';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
        print_r($package->getExtra());
        exit;
        return "modules/";

        $this->initializeVendorDir();

        $basePath = ($this->vendorDir ? $this->vendorDir.'/' : '') . $package->getPrettyName();
        $targetDir = $package->getTargetDir();

        return $basePath . ($targetDir ? '/'.$targetDir : '');

        $name = explode("/", $package->getName());
        return "modules/{$name[1]}TTTTTTT/";
    }
}
