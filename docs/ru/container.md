Контейнер
=========

**Class suffi\di\Container**

Класс для хранения объектов и их зависимостей.

### Основные методы:

* _set(string $key, $instance)_ - Добавление объекта в контейнер.

* _setSingleton(string $key, $instance)_ - Добавление объекта в контейнер как синглтона (одиночки). Объект нельзя будет удалить или перезаписать повторно.

* _get(string $key)_ - Получение объекта по ключу.

* _make(string $name, array $makeParameters = [])_ - Создание объекта по определению, с параметрами для конструктора. Не добавляет созданный объект в контейнер.

* _invokeMethod($instance, string $methodName, array $makeParameters = [])_ - Вызывает метод объекта с подставлением в него параметров из контейнера.

* _has(string $key)_ - Показывает, есть ли в контейнере готовый объект по указанному ключу.

* _hasSingleton(string $key)_ - Показывает, есть ли в контейнере готовый объект-одиночка по указанному ключу.

* _remove(string $key)_ - Удаляет из контейнера объект по ключу. Не удаляет синглтоны.

* _addDefinition(string $name, string $className): Definition_ - Задаем определение $name для класса $className.

* _setDefinition(string $name, Definition $definition)_ - Устанавливаем определение $definition.

* _getDefinition(string $name)_ - Получаем определение по имени.

* _removeDefinition(string $name)_ - Удаление определение.

* _hasDefinition(string $name)_ - Показывает, есть ли в контейнере определение с указанным именем.

* _setAlias(string $name, string $alias)_ - Добавление псевдонима.

* _getAlias(string $name)_ - Получение псевдонима по имени.

* _removeAlias(string $name)_ - Удаление псевдонима.

* _hasAlias(string $name)_ - Проверка, существует ли указанный псевдоним.

* _setParameter(string $name, string $parameter)_ - Добавление параметра.

* _getParameter(string $name)_ - Получение параметра по имени.

* _removeParameter(string $name)_ - Удаление параметра.

* _hasParameter(string $name)_ - Проверка, существует ли указанный параметр.