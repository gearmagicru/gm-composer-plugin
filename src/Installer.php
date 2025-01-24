<?php
/**
 * Этот файл является частью пакета GM ComposerPlugin.
 * 
 * @see https://gearmagic.ru/framework/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\ComposerPlugin;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Класс установщика.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\ComposerPlugin
 */
class Installer extends LibraryInstaller
{
    /**
     * Тип пакета.
     * 
     * - gm-component, модуль, расш. модуля, виджет, плагин;
     * - gm-lang, локализации;
     * - gm-theme, темы.
     * 
     * @var string 
     */
    protected string $packageType = '';

    /**
     * Карта установки пакетов в зависимости от его типа.
     * 
     * @var array
     */
    protected array $packageTypesMap = [
        'gm-component' => '/modules/{name}/',
        'gm-lang'      => '/lang/',
        'gm-theme'     => '/public/themes/{name}/'
    ];

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    public function supports(string $packageType)
    {
        $this->packageType = $packageType;
        return $packageType === 'gm-component' || $packageType === 'gm-lang' || $packageType === 'gm-theme';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
        echo "\r\nInstall package type \"{$this->packageType}\" for \"{$package->getName()}\".\r\n";
        $basePath = realpath($this->vendorDir . '/..');
        echo "Base path for gm plugin: \"$basePath\".\r\n";

        /** @var array $extra */
        $extra = $package->getExtra();
        /** @var array $gmExtra */
        $gmExtra = $extra['gm'] ?? [];

        /** @var string $pathTemplate Шаблон пути */
        $pathTemplate = $this->packageTypesMap[$this->packageType];
        if ($pathTemplate) {
            // если компонент (модуль, расш. модуля, виджет, плагин)
            if ($this->packageType === 'gm-component') {
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
                if ($path)
                    return $basePath . str_replace('{name}', $path, $pathTemplate);
                else
                    echo "Error: can't get the path from extra.\r\n";
            } else
            // если локализация
            if ($this->packageType === 'gm-lang') {
                return $basePath . $pathTemplate;
            } else
            // если тема
            if ($this->packageType === 'gm-theme') {
                $name = $gmExtra['name'] ?? ''; // название темы
                $type = $gmExtra['type'] ?? ''; // тип темы: 'backend', 'frontend'
                if ($type === 'frontend') {
                    $type = '';
                }
                if ($name) {
                    $path = $name . ($type ? '/' . $type : '');
                    return $basePath . str_replace('{name}', $path, $pathTemplate);
                } else
                    echo "Error: not found property \"name\" in extra.\r\n";
            } else
                echo "Warning: not apply plugin to package type.\r\n";
        } else
            echo "Error: not found property \"gm\" in extra. \r\n";

        return parent::getInstallPath($package);
    }
}
