<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <?php wp_head(); ?>
</head>
<body <?php body_class() ?>>

<?php if(is_front_page() && get_theme_mod('header_image') != 'remove-header'): ?>
    <div class="header-image" style="background: url(<?php echo get_custom_header()->url; ?>) center no-repeat; background-size: cover; height: 50vh;"></div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <?php if (has_custom_logo()) :?>
        <?php the_custom_logo()?>
    <?php else :?>
        <a class="navbar-brand" href="<?= home_url() ?>"><?php bloginfo('name') ?></a>
    <?php endif; ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?php wp_nav_menu(array(
            'theme_location'  => 'header_menu',
            'container'       => 'div',
            'container_class' => 'collapse navbar-collapse',
            'container_id'    => 'navbarSupportedContent',
            'menu_class'      => 'navbar-nav mr-auto',
            'walker'          => new Test_Menu(),
    )) ?>

</nav>

<div class="wrapper">