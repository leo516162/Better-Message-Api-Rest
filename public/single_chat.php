<?php
// Incluir los archivos que contienen las funciones necesarias
include_once( plugin_dir_path( __FILE__ ) . '../includes/funciones.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/admin.php' );

// Callback para obtener un chat único
function get_single_chat_callback($data) {
    $thread_id = $data->get_param('thread_id');
    global $wpdb;

    // Llamar a la función para establecer los encabezados de control de caché
    no_cache_headers();

    // Obtener los mensajes del hilo específico
    $query = $wpdb->prepare("
        SELECT m.id AS message_id, m.sender_id, m.message, m.date_sent, meta.meta_value AS meta_info
        FROM qyl_bm_message_messages AS m
        LEFT JOIN qyl_bm_message_meta AS meta ON m.id = meta.bm_message_id
        WHERE m.thread_id = %d
        ORDER BY m.date_sent DESC
    ", $thread_id);

    // Ejecutar la consulta SQL
    $results = $wpdb->get_results($query, ARRAY_A);

    // Verificar si se obtuvieron resultados
    if ($results) {
        // Array para almacenar los mensajes del chat
        $messages = array();

        // Recorrer los resultados y procesar los mensajes
        foreach ($results as $row) {
            // Verificar si el mensaje es borrado o tiene solo archivos adjuntos
            $content = '';
            if ($row['message'] === '<!-- BM-DELETED-MESSAGE -->') {
                $content = 'Mensaje eliminado';
            } elseif ($row['message'] === '<!-- BM-ONLY-FILES -->') {
                // Obtener el enlace del archivo del meta_value
                $file_link = '';
                if (!empty($row['meta_info'])) {
                    $meta_info = unserialize($row['meta_info']);
                    if (is_array($meta_info) && isset($meta_info[5416])) {
                        // Extraer solo el enlace del archivo del meta_value
                        $attachment_info = unserialize($meta_info[5416]);
                        if (is_array($attachment_info) && !empty($attachment_info)) {
                            // Obtener el primer valor del array (el enlace del archivo)
                            $file_link = reset($attachment_info);
                        }
                    }
                }
                $content = !empty($file_link) ? $file_link : 'Archivo';
            } else {
                $content = $row['message'];
            }

            // Construir el mensaje con la información adecuada
            $message = array(
                'message_id' => $row['message_id'],
                'sender_id' => $row['sender_id'],
                'message' => $content,
                'date_sent' => $row['date_sent'],
                'meta_info' => $row['meta_info'] // Incluir la meta_info si está disponible
            );

            // Agregar el mensaje al array de mensajes
            $messages[] = $message;
        }

        // Devolver los resultados como JSON
        return rest_ensure_response($messages);
    } else {
        // Devolver un mensaje de error si no se encontraron mensajes
        return new WP_Error('no_messages_found', 'No se encontraron mensajes para el hilo especificado.', array('status' => 404));
    }
}
?>