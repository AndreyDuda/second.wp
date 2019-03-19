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
define( 'AUTH_KEY',         '&^<fuZXa-d:-p#[[dx!(Yv@sVb`<7d``^>WYsGk5A9QRDS7]P~bi!,OjgT/UxG#|' );
define( 'SECURE_AUTH_KEY',  'I;ZPit_*r$uv(Aycm/BP2R^QFW2p*Z$G7gJPrH90l8%mmNe&2>,9R$s^YlBXpLp[' );
define( 'LOGGED_IN_KEY',    '[v]22!vO,LOH{tfH%}ogW6`{/k}NY2]#2!71G5^z_`t*ToT[4yV)Cb;ia _QXI8v' );
define( 'NONCE_KEY',        'V8oC8i,o:[W[Lbzc>bxlDh3GGNXr-L4<rq`9%Xg<}Z][aw~kj#+&=ePs[ &E4$w3' );
define( 'AUTH_SALT',        '5L]@V5qIk1WC,(j4Qg}9W}M%FHhnm<xprQILpb_hgPoq 0[[V&?+#.=Xu3%3OiR%' );
define( 'SECURE_AUTH_SALT', '.roKg2hc/Bl3{Z=sh~=fE4Ir3oT5aT&QQ@9IE}i7M<)@s?g01slj6Cf:*,vQ.fGw' );
define( 'LOGGED_IN_SALT',   '@F(QnY@YX|vN)*.^W0(aG8+y!sv@Qn(I6BN-?tw/t9j0BIm`T#*k;..(n#Ix&)WV' );
define( 'NONCE_SALT',       '>(-SC#WW$)tsgt|r;ixc!_I$|;oE@7/4-}Rqrk{R|,nTH?m&Z>p)7xWvR7hB55*{' );

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

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
