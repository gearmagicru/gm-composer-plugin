# Плагин Composer для установки ресурсов (компонентов, тем и библиотек) веб-приложения GearMagic

Для установки ресурса, необходимо указать значение атрибуту <b>"type"</b> в вашем файле composer.json:
- модули, расширения модулей, виджеты, плагины
```
{"type" : "component", ... }
```
- темы
```
{"type" : "theme", ... }
```
- локализации
```
{"type" : "lang", ... }
```
- файлы и папки редакции веб-приложения
```
{"type" : "skeleton", ... }
```
а в раздел <b>"require"</b> добавить `"gearmagicru/gm-composer-plugin": "*"`.


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
