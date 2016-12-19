Контейнер
=========

**Class suffi\di\Container**

Класс для хранения объектов и их зависимостей.

### Основные методы:

* _set(string $key, $instance)_ - Добавление объекта в контейнер.

* _setSingleton(string $key, $instance)_ - Добавление объекта в контейнер как синглтона (одиночки). Объект нельзя будет удалить или перезаписать повторно.

* _get(string $key)_ - Получение объекта по ключу.

* _has(string $key)_ - Показывает, есть ли в контейнере готовый объект по указанному ключу.

* _hasSingleton(string $key)_ - Показывает, есть ли в контейнере готовый объект-одиночка по указанному ключу.

* _remove(string $key)_ - Удаляет из контейнера объект по ключу. Не удаляет синглтоны.

* _setDefinition(string $name, string $className): Definition_ - Задаем правило $name для класса $className.

* _getDefinition(string $name)_ - Получаем правило по имени.

* _removeDefinition(string $name)_ - Удаление правила.

* _hasDefinition(string $name)_ - Показывает, есть ли в контейнере правило с указанным именем.

* _setAlias(string $name, string $alias)_ - Установка псевдонима.

* _getAlias(string $name)_ - Получение псевдонима.

* _hasAlias(string $name)_ - Проверка на существование псевдонима.

* _removeAlias(string $name)_ - Удаление псевдонима.