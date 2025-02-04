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
use Composer\IO\IOInterface;
use Composer\Installer\PackageEvent;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Класс скриптов composer.
 * 
 * @link https://getcomposer.org/doc/articles/scripts.md#event-names
 *
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\ComposerPlugin
 */
class Scripts
{
    /**
     * Полный путь к редакции веб-приложения.
     *
     * @param string
     */
    static string $appPath = '';

    /**
     * Полный путь к установочным файлам.
     *
     * @param string
     */
    static string $installPath = '';

    /**
     * Событие после выполнения команды "app-install".
     *
     * @return void
     */
    static public function appInstall(Event $event): void
    {
        /** @var IOInterface $io */
        $io = $event->getIO();
        /** @var \Composer\Composer $composer  */
        $composer = $event->getComposer();

        static::$appPath = realpath(rtrim($composer->getConfig()->get('vendor-dir'), '/') . '/..');
        static::$installPath = static::$appPath . '/.install';
        if (file_exists(static::$installPath)) {
            $filename = static::$installPath . '/package.json';
            if (file_exists($filename)) {
                $config = static::readInstallPackage($filename);
                if ($config) {
                    // если необходимо копировать
                    if (isset($config['copy'])) {
                        static::copyInstallFiles($config['copy'], $io);
                    }
                } else
                    $io->write('Error: can\'t read install.json.');
            } else
                $io->write('Error: file "' . $filename . '" not found.');
        } else
            $io->write('Warning: path "' . static::$installPath . '" not found.');
    }

    /**
     * Рекурсивное копирование каталогов.
     * 
     * @param string $from
     * @param string $top
     * @param IOInterface $io
     * 
     * @return void
     */
    static protected function copyInstallDir(string $from, string $to, IOInterface $io): void
    {
        if (!file_exists($to)) {
            if (!mkdir($to, 0755)) {
                $io->write('Error: can\'t make dir "' . $to . '".');
                return;
            }
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($from, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $dir = $to . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (!file_exists($dir)) {
                    if (mkdir($dir))
                        $io->write("--> make dir \"$item\".");
                    else
                        $io->write('Error: can\'t make dir "' . $dir . '".');
                }
            } else {
                if (copy($item, $dir))
                    $io->write("--> from \"$item\" to \"$dir\".");
                else
                    $io->write('Error: can\'t copy file "' . $item . '".');
            }
        }
    }

    /**
     * Копирование установочных файлов.
     * 
     * @param array $items
     * @param IOInterface $io
     * 
     * @return void
     */
    static protected function copyInstallFiles(array $items, $io): void
    {
        $io->write("Copy install files: ");
        foreach ($items as $item) {
            $from = static::$installPath . $item[0];
            if (!file_exists($from)) {
                $io->write('Error: can\'t copy file "' . $from . '".');
            }
            $to = static::$appPath . $item[1];
            // если каталог
            if (is_dir($from)) {
                static::copyInstallDir($from, $to, $io);
            // если файл
            } else {
                if (!copy($from, $to))
                    $io->write('Error: can\'t copy file "' . $from . '".');
                else
                    $io->write("--> from \"$from\" to \"$to\".");
            }
        }
    }
  
    /**
     * Чтение установочного пакета.
     * 
     * @param string $filename
     * 
     * @return false|array
     */
    static protected function readInstallPackage(string $filename): false|array
    {
        $json = file_get_contents($filename);
        if ($json === false || empty($json)) {
            return false;
        }

        $config = json_decode($json, true);
        return json_last_error() === JSON_ERROR_NONE ? $config : false;
    }
}
