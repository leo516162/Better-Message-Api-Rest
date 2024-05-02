<?php
/*
Plugin Name: Bizcaribe
Description: Plugin para el B2B Bizcaribe.
Version: 1.0
Author: Leonardo Perez
*/

// Include necessary files
include_once( plugin_dir_path( __FILE__ ) . 'includes/funciones.php' );
include_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
include_once( plugin_dir_path( __FILE__ ) . 'admin/send_message.php' );
include_once( plugin_dir_path( __FILE__ ) . 'admin/send_message_thread.php' );
include_once( plugin_dir_path( __FILE__ ) . 'public/chatslist.php' );
include_once( plugin_dir_path( __FILE__ ) . 'public/single_chat.php' );
include_once( plugin_dir_path( __FILE__ ) . 'includes/admin-page-content.php' );

// Hook para agregar la página de administración
add_action('admin_menu', 'bizcaribe_admin_menu');

function bizcaribe_admin_menu() {
    add_menu_page(
        'Bizcaribe',
        'Bizcaribe',
        'manage_options',
        'bizcaribe-admin-page',
        'bizcaribe_admin_page_content',
        'dashicons-businessman', // Icono de la página (Puedes cambiarlo)
        30 // Posición en el menú
    );
// Agregar submenús
    add_submenu_page(
        'bizcaribe-admin-page', // Slug del menú principal
        'Opciónes', // Título de la página
        'Opciónes', // Título del menú
        'manage_options', // Capacidad requerida
        'bizcaribe-opcion-1', // Slug de la página
        'bizcaribe_opcion_1_content' // Callback de contenido de la página
    );

    add_submenu_page(
        'bizcaribe-admin-page', // Slug del menú principal
        'Quienes somos', // Título de la página
        'Quienes somos', // Título del menú
        'manage_options', // Capacidad requerida
        'bizcaribe-opcion-2', // Slug de la página
        'bizcaribe_opcion_2_content' // Callback de contenido de la página
    );

    // Agrega más submenús según sea necesario
}

// Callback para el contenido de la Opción 1
function bizcaribe_opcion_1_content() {
    echo '<h1>Contenido de la Opción 1</h1>';
    // Agrega el contenido que desees mostrar en la Opción 1
}

// Callback para el contenido de la Opción 2
function bizcaribe_opcion_2_content() {
    echo '<h1>Contenido de la Opción 2</h1>';
    // Agrega el contenido que desees mostrar en la Opción 2
}
add_action('admin_head', 'remove_other_plugin_warnings');

function remove_other_plugin_warnings() {
    ?>
    <style>
        /* Ocultar elementos no deseados */
        .notice {
            display: none !important;
        }
    </style>
    <?php
}

?>
