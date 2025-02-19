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
use FilesystemIterator;
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
     * Событие после выполнения команды "app-deploy".
     *
     * @return void
     */
    static public function appDeploy(Event $event): void
    {
        /** @var IOInterface $io */
        $io = $event->getIO();
        /** @var \Composer\Composer $composer  */
        $composer = $event->getComposer();

        $basePath = realpath(rtrim($composer->getConfig()->get('vendor-dir'), '/') . '/..');
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $counter = array_fill_keys(
            [
                'readme.md', 'info.md', 'license.md', 'changelog.md', 'security.md', 'roadmap.md', 'contributing.md', 'contributors.md',
                'license.txt', 'changelog.txt', 'security.txt',
                '.editorconfig', '.gitignore', '.gitattributes', 
                'composer.json', 'composer.lock',
                'readme', 'info', 'license', 'changelog', 'install', 'commitment', 'dockerfile'
            ],
        0);

        $installPath = $basePath . '/.install';
        if (file_exists($installPath)) {
            $io->write('*** Deleting files (.install) ***');
            static::deleteDir($installPath);
            $io->write('deleted: ' . $installPath);
            $io->write('');
        }

        $io->write('*** Deleting Git Repository Files ***');
        $io->write('');
        $index = 1;
        $errors = [];
        foreach ($iterator as $item) {
            if ($item->isFile()) { 
                $name = $item->getFilename();
                $check = strtolower($name);
                if (isset($counter[$check])) {
                    $counter[$check] = $counter[$check] + 1;
                    $filename = $item->getPath() . '/' . $name;
                    if (unlink($filename))
                        $io->write('deleted (' . ($index++) . '): ' . str_replace($basePath, '', $item->getPath()) . '/' .  $name);
                    else
                        $errors[] = str_replace($basePath, '', $item->getPath()) . '/' .  $name;
                }
            }
        }

        if ($errors) {
            $io->write('');
            $io->write('*** File deletion errors ***');
            $io->write('');
            foreach ($errors as $name) {
                $io->write('delete error: ' . $name);
            }
        }

        $totals = [];
        foreach ($counter as $name => $value) {
            if ($value > 0) $totals[] = "$name ($value)";
        }
        $io->write('');
        $io->write('*** Total files deleted ***');
        $io->write('');
        $io->write(implode(', ', $totals));
    }

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
     * Удаление каталога с файлами.
     * 
     * @param string $dir
     * 
     * @return void
     */
    static protected function deleteDir(string $dir): void
    {
        $iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
     
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        if (file_exists($dir)) {
            rmdir($dir);
        }
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
                    if (mkdir($dir)) {
                        $io->write('make dir: ' . str_replace(static::$appPath, '', $item));
                    } else
                        $io->write('Error: can\'t make dir "' . $dir . '".');
                }
            } else {
                if (copy($item, $dir)) {
                    $io->write('copy: ' . str_replace(static::$appPath, '', $item));
                    $io->write('  to: ' . str_replace(static::$appPath, '', $dir));
                } else
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
        $io->write("*** Copy install files ***");
        $io->write('');
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
                else {
                    $io->write('copy: ' . str_replace(static::$appPath, '', $from));
                    $io->write('  to: ' . str_replace(static::$appPath, '', $to));
                }
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
