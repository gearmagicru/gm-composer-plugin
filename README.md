# Плагин Composer для установки ресурсов (компонентов, тем и библиотек) веб-приложения GearMagic

[![Latest Stable Version](https://img.shields.io/packagist/v/gearmagicru/gm-composer-plugin.svg)](https://packagist.org/packages/gearmagicru/gm-composer-plugin)
[![Total Downloads](https://img.shields.io/packagist/dt/gearmagicru/gm-composer-plugin.svg)](https://packagist.org/packages/gearmagicru/gm-composer-plugin)
[![Source Code](https://img.shields.io/badge/source-gearmagicru/gm--composer--plugin-blue.svg)](https://github.com/gearmagicru/gm-composer-plugin)
[![Software License](https://img.shields.io/badge/license-BSD%203--Clause%20License-brightgreen.svg)](https://github.com/gearmagicru/gm-composer-plugin/blob/master/LICENSE)
![php 8.2+](https://img.shields.io/badge/php-min%208.2-red.svg)

Для установки ресурса, необходимо указать значение атрибуту <b>"type"</b> в вашем файле composer.json:
- `"type" : "component"`, модули, расширения модулей, виджеты, плагины, 
где в разделе <b>"extra"</b> можно указать путь установки или идентификатор компонента, например:
```
{"extra" : {"gm": {"path": "/your-name/component-dir"} } }
```
или
```
{"extra" : {"gm": {"id": "component.id", "vendor": "your-name"} } }
```
- `"type" : "theme"`, темы, 
где в разделе <b>"extra"</b> можно указать тип и название темы, например:
```
{"extra" : {"gm": {"type": "frontend", "name": "theme-name"} } }
```
или
```
{"extra" : {"gm": {"type": "backend", "name": "theme-name"} } }
```
- `"type" : "lang"`, локализации;
- `"type": "skeleton"`, файлы и папки редакции веб-приложения;
- `"type" : "gm"`, один из компонентов или библиотек GM,
где в разделе <b>"extra"</b> можно указать путь установки, например:
```
{"extra" : {"gm": {"path": "/public/vendors/gm/panel"} } }
```
- `"type" : "library"`, библиотека,
где в разделе <b>"extra"</b> можно указать название библиотеки или локальный путь относительно проекта, например:
```
{"extra" : {"gm": {"path": "/vendor/library-dir"} } }
```
или
```
{"extra" : {"gm": {"name": "library-name"} } }
```

В раздел <b>"require"</b> необходимо добавить `"gearmagicru/gm-composer-plugin": "*"`.


В файле создания проекта composer.json указать:
```
{
    ...,
    "config": {
        "allow-plugins": {
            "gearmagicru/gm-composer-plugin": true
        }
    }
}
```
