<?php
/**
 * Этот файл является частью пакета GM ComposerPlugin.
 * 
 * @see https://gearmagic.ru/framework/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\ComposerPlugin;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Класс событий composer.
 * 
 * @link https://getcomposer.org/doc/articles/scripts.md#event-names
 *
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\ComposerPlugin
 */
class Events
{
    public static function copyFiles($source, $dest)
    {
        mkdir($dest, 0755);
        foreach (
          $iterator = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
          RecursiveIteratorIterator::SELF_FIRST) as $item) {
          if ($item->isDir()) {
            mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
          } else {
            copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
          }
        }
    }
  
    /**
     * Событие после установки пакета (post-package-install).
     *
     * @return void
     */
    public static function postPackageInstall(PackageEvent $event): void
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $event->getOperation()->getPackage();
        /** @var array $extra */
        $extra = $package->getExtra();

        /** @var array $gmExtra */
        $replaceFrom = $extra['gm']['replaceFrom'] ?? '';
        if ($replaceFrom) return;

        /** @var IOInterface $io */
        $io = $event->getIO();

        //$name = $package->getName();

        /** @var \Composer\Composer $composer  */
        $composer = $event->getComposer();

        /** @var \Composer\Installer\LibraryInstaller $installer  */
        $installer = $composer->getInstallationManager()->getInstaller($package->getType());

        $basePath = realpath(rtrim($composer->getConfig()->get('vendor-dir'), '/') . '/..');

        $fromPath = rtrim($basePath, '/') . $replaceFrom;
        $toPath = $installer->getInstallPath($package);

        $io->write("[[FROM=$fromPath TO=$toPath]]]");

        $io->write('-------------------------------------------------------');
    }
}
