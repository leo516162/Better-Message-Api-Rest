<?php

// Incluir los archivos que contienen las funciones necesarias
include_once( plugin_dir_path( __FILE__ ) . '../includes/funciones.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/admin.php' );

// Callback para enviar un mensaje a un usuario
function send_message_callback( WP_REST_Request $request ) {
    global $wpdb;

     // Obtener los parámetros de la solicitud
    $sender_id = intval($request->get_param('sender_id'));
    $receiver_id = intval($request->get_param('receiver_id'));
    $message = $request->get_param('message');

    // Verificar si ya existe un hilo entre el remitente y el receptor
    $existing_thread_id = check_thread_exists($sender_id, $receiver_id);

    // Si no existe un hilo, crear uno nuevo
    if (!$existing_thread_id) {
        $thread_id = create_new_thread($sender_id, $receiver_id);
    } else {
        $thread_id = $existing_thread_id;
    }

    // Verificar si los parámetros requeridos están presentes
    if (empty($sender_id) || empty($receiver_id) || empty($message)) {
        return new WP_Error('missing_parameters', 'Faltan parámetros requeridos para enviar el mensaje.', array('status' => 400));
    }

    // Llamar a la función para establecer los encabezados de control de caché
    no_cache_headers();

    // Obtener la fecha y hora actual en formato MySQL
    $current_time = current_time('mysql');

    // Generar un valor de tiempo único para created_at y updated_at
    $microtime = microtime(true);
    $created_at = $microtime * 10000; // Convertir a microsegundos
    $updated_at = $created_at;

    // Generar un temp_id único
    $temp_id = 'tmp_' . $sender_id . '_' . uniqid();

    // Insertar el nuevo mensaje en la tabla qyl_bm_message_messages
    $insert_result = $wpdb->insert('qyl_bm_message_messages', array(
        'thread_id' => $thread_id,
        'sender_id' => $sender_id,
        'message' => $message,
        'date_sent' => $current_time,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
        'temp_id' => $temp_id,
    ));

    // Verificar si la inserción fue exitosa
    if ($insert_result === false) {
        return new WP_Error('message_not_sent', 'El mensaje no se pudo enviar.', array('status' => 500));
    }

    // Obtener el ID del mensaje recién insertado
    $message_id = $wpdb->insert_id;

    // Devolver una respuesta con el ID del mensaje
    return array(
        array(
            'message_id' => $message_id,
            'thread_id' => $thread_id,
            'status' => 'Mensaje enviado exitosamente.',
        )
    );
}
?>
