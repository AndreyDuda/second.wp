<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'wp_second' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'second' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', 'secret' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'mysql' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '(>l1tnUDJj|lTt,V=?m8[ 86~%oUcZsKcA^~o1{w]]H`1-reuqmq7~}=q8]7PKAS' );
define( 'SECURE_AUTH_KEY',  'yqKTCxhG@Xyh]y(7Gt:C?Sqcs@|D*bJL-fy#|CIUUTjk`?G_VxHvbTQVi$rsA{YW' );
define( 'LOGGED_IN_KEY',    'Y!OHys)Kw)5MKl0Q:C~j,jXbgT*.2E1)F%l6$Y0I&M4~2,*d:[Bd9>W);SS3P-B>' );
define( 'NONCE_KEY',        '65$:%cflQFJ+pdz<{IPad`IuYhA+{ThvHCIae[]ee{MDkz8/wFyUfOShO}}5P/#[' );
define( 'AUTH_SALT',        ':D^RsM5,C#<dG1Th[1<Cz*ONb2?2h^D0xt,jO7Y$(0E}?gY4PHe&4s6.7AtvP!yi' );
define( 'SECURE_AUTH_SALT', '!S8LMm,;+9aT,?qs,1P+u[)3rreZ5R=Wd9PLZR`.($15R:ZG~{,^(1,xJCn?Q#d)' );
define( 'LOGGED_IN_SALT',   '8z ^#`P}E]iq3PvmE`5k}#S|D_/siL@e+g$^eKNqE&YvihV,,j[W$RSj-I6SP?:!' );
define( 'NONCE_SALT',       '9d)Hl&KnC]2$$2!OfegTF%tV<ld/7gU_?*OHBnm-(]`($})M#y4xwq$~YWX3kM3}' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
define('FS_METHOD','direct'); // не запрашивать данные о сервере при работе с плагинами
define('ALLOW_UNFILTERED_UPLOADS', true);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
