# Star Rating for MODX Evolution / Звездный рейтинг для MODX Evolution

## Установка

Создать новый сниппет star_rating (название сниппета не имеет значения), со следующим содержимым:

```php
<?php
return require MODX_BASE_PATH . 'assets/snippets/star_rating/snippet.php';
?>
```

Создать новый модуль Star Rating, со следующим содержимым:

```php
include MODX_BASE_PATH . 'assets/snippets/star_rating/starrating.module.php';
```

После создания модуля необходимо обновить страницу чтобы ссылка на модуль появилась на вкладке `"Модули"`.
 Далее если вы ранее не устанавливали этот компонент необходимо нажать кнопку `"Установить"`
