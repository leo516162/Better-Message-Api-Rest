<?php

// Incluir los archivos que contienen las funciones necesarias
include_once( plugin_dir_path( __FILE__ ) . '../admin/send_message.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/send_message_thread.php' );
include_once( plugin_dir_path( __FILE__ ) . '../public/chatslist.php' );
include_once( plugin_dir_path( __FILE__ ) . '../public/single_chat.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/admin.php' );

// Función para establecer los encabezados de control de caché
function no_cache_headers() {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// Función para verificar la autenticación básica
function verificar_autenticacion_basica($data) {
    $authorization_header = $data->get_header('Authorization');
    
    // Obtener las credenciales almacenadas
    $stored_public_key = get_option('bizcaribe_public_key');
    $stored_secret_key = get_option('bizcaribe_secret_key');
    
    // Verificar si las credenciales almacenadas están definidas
    if (!$stored_public_key || !$stored_secret_key) {
        return false; // No hay credenciales almacenadas
    }

    // Construir la cadena de credenciales esperada
    $expected_credentials = base64_encode($stored_public_key . ':' . $stored_secret_key);

    // Verificar si las credenciales coinciden con las esperadas
    if ($authorization_header !== 'Basic ' . $expected_credentials) {
        return false; // No autorizado
    } else {
        return true; // Autorizado
    }
}

// Función para verificar los permisos antes de ejecutar la función principal
function verificar_permisos($data) {
    if (verificar_autenticacion_basica($data)) {
        return true; // Usuario autorizado
    } else {
        return new WP_Error('not_authorized', 'No tienes permiso para usar esta API.', array('status' => 403));
    }
}
add_action('rest_api_init', 'register_chat_list_api_route');

// Función para verificar si ya existe un hilo con el usuario especificado
function check_thread_exists($sender_id, $receiver_id) {
    global $wpdb;

    // Realizar la consulta para buscar un hilo que incluya a ambos usuarios
    $query = $wpdb->prepare("
        SELECT m1.thread_id
        FROM qyl_bm_message_recipients m1
        INNER JOIN qyl_bm_message_recipients m2 ON m1.thread_id = m2.thread_id
        WHERE m1.user_id = %d AND m2.user_id = %d
    ", $sender_id, $receiver_id);

    // Ejecutar la consulta SQL
    $result = $wpdb->get_var($query);

    // Verificar si se encontró algún hilo
    if ($result !== null) {
        return $result; // Retornar el ID del hilo existente
    } else {
        return false; // Retornar falso si no se encontró ningún hilo
    }
}

// Función para crear un nuevo hilo
function create_new_thread($sender_id, $receiver_id) {
    global $wpdb;

    // Obtener la fecha y hora actual en formato MySQL
    $current_time = current_time('mysql');

    // Insertar el nuevo hilo en la tabla qyl_bm_threads
    $insert_thread_result = $wpdb->insert('qyl_bm_threads', array(
        'subject' => 'New Thread', // Puedes establecer un asunto predeterminado si lo deseas
        'type' => 'thread', // Especificar el tipo como "thread"
        'created_at' => $current_time,
    ));

    // Verificar si la inserción del hilo fue exitosa
    if ($insert_thread_result === false) {
        return false; // Retornar falso si la inserción falló
    }

    // Obtener el ID del hilo recién insertado
    $thread_id = $wpdb->insert_id;

    // Insertar los participantes del hilo en la tabla qyl_bm_message_recipients
    $insert_recipient_result = $wpdb->insert('qyl_bm_message_recipients', array(
        'user_id' => $sender_id,
        'thread_id' => $thread_id,
        'last_update' => $current_time,
    ));

    // Verificar si la inserción de participantes fue exitosa
    if ($insert_recipient_result === false) {
        return false; // Retornar falso si la inserción falló
    }

    // Insertar el otro participante del hilo en la tabla qyl_bm_message_recipients
    $insert_recipient_result = $wpdb->insert('qyl_bm_message_recipients', array(
        'user_id' => $receiver_id,
        'thread_id' => $thread_id,
        'last_update' => $current_time,
    ));

    // Verificar si la inserción de participantes fue exitosa
    if ($insert_recipient_result === false) {
        return false; // Retornar falso si la inserción falló
    }

    return $thread_id; // Retornar el ID del nuevo hilo creado
}
?>