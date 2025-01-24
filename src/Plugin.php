<?php
/**
 * Этот файл является частью пакета GM ComposerPlugin.
 * 
 * @see https://gearmagic.ru/framework/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\ComposerPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Класс плагина Composer.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\ComposerPlugin
 */
class Plugin implements PluginInterface
{
    /**
     * Применяет модификации плагина к Composer.
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $composer->getInstallationManager()->addInstaller(new Installer($io, $composer));
    }

    /**
     * Удаляет все хуки из Composer.
     *
     * Вызывается, когда плагин деактивируется перед удалением, но также и перед 
     * обновлением до новой версии, чтобы можно было деактивировать старый и 
     * активировать новый.
     *
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Подготавливает плагин к удалению.
     *
     * Это будет вызвано после деактивации.
     *
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
}
