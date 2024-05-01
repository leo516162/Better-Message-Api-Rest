<?php
// Incluir los archivos que contienen las funciones necesarias
include_once( plugin_dir_path( __FILE__ ) . '../includes/funciones.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/send_message.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/send_message_thread.php' );
include_once( plugin_dir_path( __FILE__ ) . '../public/chatslist.php' );
include_once( plugin_dir_path( __FILE__ ) . '../public/single_chat.php' );

// Registro de la ruta REST para obtener la lista de chats
function register_chat_list_api_route() {
    register_rest_route('chat/v1', '/chatslist', array(
        'methods' => 'GET',
        'callback' => 'lista_de_chats_callback',
        'permission_callback' => 'verificar_permisos', // Verificar permisos antes de ejecutar la función
    ));
}


// Registro de la ruta REST para obtener un chat único
function register_single_chat_api_route() {
   register_rest_route('chat/v1', '/single-chat', array(
    'methods' => 'GET',
    'callback' => 'get_single_chat_callback',
    'permission_callback' => 'verificar_permisos', // Verificar permisos antes de ejecutar la función
));
}
add_action('rest_api_init', 'register_single_chat_api_route');

// Registro de la ruta REST para enviar un mensaje
function register_send_message_api_route() {
    register_rest_route('chat/v1', '/send-message', array(
        'methods' => 'POST',
        'callback' => 'send_message_callback',
        'permission_callback' => 'verificar_permisos', // Verificar permisos antes de ejecutar la función
    ));
}
add_action('rest_api_init', 'register_send_message_api_route');

// Registro de la ruta REST para enviar un mensaje a un thread
function register_send_message_thread_api_route() {
    register_rest_route('chat/v1', '/send-message-thread', array(
        'methods' => 'POST',
        'callback' => 'send_message_thread_callback',
        'permission_callback' => 'verificar_permisos', // Verificar permisos antes de ejecutar la función
    ));
}
add_action('rest_api_init', 'register_send_message_thread_api_route');
?>