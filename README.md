# twig-declension

Фильтр для twig

* Позволяет управлять списком склоняемых слов и множественных форм
* Подключается как расширение к шаблонизатору twig
* Применяется к строке как фильтр в шаблоне
* Находит склоняемое слово в предварительно наполненной таблице и возвращает требуемую форму
* В случае отсутствия соответствующей записи в БД или при пустом склонении возвращает исходную строку или именительный падеж
* Использует standalone библиотеку phpMophy (http://phpmorphy.sourceforge.net/dokuwiki/)

1) Установка
----------------------------------

    Выполнить:
    ```sh
    composer require bubnov/twig-declension-bundle: ~3.0
    ```

    Добавить бандл в конфигурацию AppKernel
    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bubnov\TwigDeclensionBundle\BubnovTwigDeclensionBundle(),
            // ...
        );
    }
    ```

    Обновить базу данных
    ```sh
    bin/console doctrine:schema:update
    ```

    или

    ```sh
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

    Настроить routing.yml
    ```yml
    # app/routing.yml
    bubnov-twig-declension-bundle:
        resource: "@BubnovTwigDeclensionBundle/Resources/config/routing/routing.yml"
    ```

    Тонкая настройка
    ```yml
    # app/config.yml
    bubnov_twig_declension:
        pre_cache: false #(true по умолчанию) - загрузка сразу всех слов в словаре одним запросом к БД при первом обращении
        auto_create: false #(true по умолчанию) - автоматическое создание форм слов, запрашиваемых в twig


2) Использование
-------------------------------------
    Добавить ссылку в административной панели или меню
    ```twig
    {# Ваш шаблон меню/панели #}
    <a href="{{ path('admin_twig_declension') }}">Слонения</a>
    ```

    ** Внимание! В контроллере используется контроль доступа. Пользователь должен обладать ролью ROLE_ADMIN непосредственно, либо по иерархии ролей (см. security.role_hierarchy)

    Создать необходимые записи в административном интерфейсе
    В данном примере мы создали запись "яблоко" и заполнили все падежи и множественные формы

    Склонение:
    ```twig
    {# Ваш шаблон #}
    Ньютон получил по голове {{ 'яблоко' | declension('abl') }}
    {# Получится 'Ньютон получил по голове  яблоком' #}
    ```

    Множественное число:
    ```twig
    {# Ваш шаблон #}
    В ящике лежат {{ 'яблоко' | declension('inf_multi') }}
    {# Получится 'В ящике лежат яблоки' #}
    ```

    Множественные формы:
    ```twig
    {# Ваш шаблон #}
    У меня в кармане 12 {{ 'яблоко' | declension('plural', 12) }}
    {# Получится 'У меня в кармане 12 яблок' #}
    ```

3) Список ключей и падежей
-------------------------------------
    * inf         - именительный падеж
    * inf_multi   - именительный падеж множественного числа
    * gen         - родительный падеж
    * gen_multi   - родительный падеж множественного числа
    * dat         - дательный падеж
    * acc         - винительный падеж
    * abl         - творительный падеж
    * pre         - предложный падеж
    * plural      - множественные формы


4) Автоматическое заполнение форм слова
-------------------------------------
    На странице добавления/обновления записи можно настроить автоматическое заполнение форм слова.

    Для генерации url к контроллеру используется FOS\JsRoutingBundle
    Подключите его в app/AppKernel.php (скорее всего он там уже есть)
    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            // ...
        );
    }
    ```

    Подключите в html js-ассет 'bundles/fosjsrouting/js/router.js' и 'bundles/bubnovtwigdeclension/js/auto-declension.js'
    ```html
    <script type="text/javascript" src="{{ asset('bundles/fosjsrouting/js/router.js') }}></script>
    <script type="text/javascript" src="{{ asset('bundles/bubnovtwigdeclension/js/auto-declension.js') }}></script>
    ```

    или

    ```html
    {% javascripts
        ...
        'bundles/fosjsrouting/js/router.js'
        'bundles/bubnovtwigdeclension/js/auto-declension.js'
        ...
        output='compiled/compiled.js'
    %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}
    ```

    Установите ассеты из бандла
    ```sh
    app/console assets:install
    ```
