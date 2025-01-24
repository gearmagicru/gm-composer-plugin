<?php
namespace Gm\ComposerPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;


class Installer extends LibraryInstaller
{
    /**
     * $packageType
     */
    protected string $packageType = '';

    /**
     * $packageTypesMap
     */
    protected array $packageTypesMap = [
        'gm-component' => '/modules/gm/{name}/'
    ];

    /**
     * {@inheritdoc}
     */
    public function supports(string $packageType)
    {
        $this->packageType = $packageType;
        return $packageType === 'gm-component' || $packageType === 'gm-theme' || $packageType === 'gm';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
        /** @var array $extra */
        $extra = $package->getExtra();
        if (empty($extra['gm'])) {
            return parent::getInstallPath($package);
        }

        /** @var array $gmExtra */
        $gmExtra = $extra['gm'];
        // если компонент (модуль, расш. модуля, виджет, плагин)
        if ($this->packageType === 'gm-component') {
            $template = $this->packageTypesMap[$this->packageType];
            if ($template) {
                $path = '';
                if (!empty($gmExtra['path'])) 
                    $path = $gmExtra['path'];
                else {
                    $id     = $gmExtra['id'] ?? '';
                    $vendor = $gmExtra['vendor'] ?? 'gm';
                    if ($id && $vendor) {
                        $path = $vendor. '/' . $id;
                    }
                }
                if ($path) {
                    return str_replace('{name}', $path, $template);
                }
            }
        }

        return parent::getInstallPath($package);
    }
}
