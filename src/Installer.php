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
     * Карта установки пакетов в зависимости от его типа.
     * 
     * @var array
     */
    protected array $packageTypesMap = [
        'component' => '/modules/{name}/',
        'lang'      => '/lang/',
        'theme'     => '/public/themes/{name}/'
    ];

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    public function supports(string $packageType)
    {
        return isset($this->packageTypesMap[$packageType]);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
       /** @var string $packageType Тип пакета: gm-component, gm-lang, gm-theme */
        $packageType = $package->getType();

        $this->io->write("Install \"{$packageType}\" for \"{$package->getName()}\"");
        $basePath = realpath($this->vendorDir . '/..');
        $this->io->write("Base path: \"$basePath\".");

        /** @var array $extra */
        $extra = $package->getExtra();
        /** @var array $gmExtra */
        $gmExtra = $extra['gm'] ?? [];

        /** @var string $pathTemplate Шаблон пути */
        $pathTemplate = $this->packageTypesMap[$packageType];
        if ($pathTemplate) {
            // если компонент (модуль, расш. модуля, виджет, плагин)
            if ($packageType === 'component') {
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
                    $installPath = $basePath . str_replace('{name}', $path, $pathTemplate);
                    $this->io->write("Install to: \"$installPath\".");
                    return $installPath;
                } else
                    $this->io->write("Error: can't get the path from extra.");
            } else
            // если локализация
            if ($packageType === 'lang') {
                $installPath = $basePath . $pathTemplate;
                $this->io->write("Install to: \"$installPath\".");
                return $installPath;
            } else
            // если тема
            if ($packageType === 'theme') {
                $name = $gmExtra['name'] ?? ''; // название темы
                $type = $gmExtra['type'] ?? ''; // тип темы: 'backend', 'frontend'
                if ($type === 'frontend') {
                    $type = '';
                }
                if ($name) {
                    $path = $name . ($type ? '/' . $type : '');
                    $installPath = $basePath . str_replace('{name}', $path, $pathTemplate);
                    $this->io->write("Install to: \"$installPath\".");
                    return $installPath;
                } else
                    $this->io->write("Error: not found property \"name\" in extra.");
            } else
                $this->io->write("Warning: not apply plugin to package type.");
        } else
            $this->io->write("Error: not found property \"gm\" in extra.");

        $installPath = parent::getInstallPath($package);
        $this->io->write("Install to: \"$installPath\".");
        return $installPath;
    }
}
