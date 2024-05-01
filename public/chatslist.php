<?php

// Incluir los archivos que contienen las funciones necesarias
include_once( plugin_dir_path( __FILE__ ) . '../includes/funciones.php' );
include_once( plugin_dir_path( __FILE__ ) . '../admin/admin.php' );

// Registro de la ruta REST para obtener la lista de chats
function lista_de_chats_callback($data) {
    global $wpdb;

    // Obtener el user_id de la solicitud
    $user_id_solicitud = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    // Verificar si se proporcionó el parámetro user_id
    if ($user_id_solicitud === 0) {
        return new WP_Error('missing_parameter', 'User_id es necesario.', array('status' => 400));
    }

    // Llamar a la función para establecer los encabezados de control de caché
    no_cache_headers();

    // Consulta SQL para seleccionar los user_id, thread_id, last_delivered, unread_count y último mensaje
    $query = $wpdb->prepare("
        SELECT r.user_id, r.thread_id, MAX(r.last_delivered) AS last_delivered, MAX(r.unread_count) AS unread_count,  m.message, m.date_sent
        FROM qyl_bm_message_recipients r
        LEFT JOIN qyl_bm_message_messages m ON r.thread_id = m.thread_id
        WHERE r.user_id = %d -- Filtrar por el user_id proporcionado
        GROUP BY r.thread_id
        ORDER BY m.date_sent DESC
        
    ", $user_id_solicitud);


    // Ejecutar la consulta SQL
    $results = $wpdb->get_results($query, ARRAY_A);

    // Verificar si se obtuvieron resultados
    if ($results) {
        // Array para almacenar los resultados
        $chats = array();

        // Recorrer los resultados y agregarlos al array de chats
        foreach ($results as $row) {
            // Obtener los user_ids y display_names del thread
            $query_user_ids = $wpdb->prepare("
                SELECT r.user_id, u.display_name
                FROM qyl_bm_message_recipients r
                LEFT JOIN qyl_bm_user_index u ON r.user_id = u.ID
                WHERE r.thread_id = %d AND r.user_id != %d
            ", $row['thread_id'], $user_id_solicitud);

            // Ejecutar la consulta para obtener los user_ids y display_names
            $user_results = $wpdb->get_results($query_user_ids, ARRAY_A);

            // Crear un array asociativo para los user_ids con su display_name correspondiente
            $user_ids = array();
            foreach ($user_results as $user_row) {
                $user_ids[] = array(
                    'id' => $user_row['user_id'],
                    'display_name' => $user_row['display_name']
                );
            }

            // Obtener el tiempo transcurrido desde el último mensaje
            $date_sent_human_readable = 'hace ' . human_time_diff(strtotime($row['date_sent']), current_time('timestamp'));

            // Agregar el thread al array de chats
            $unread_count = $row['unread_count'] != 0 ? $row['unread_count'] : null;
            $chats[] = array(
                'thread_id' => $row['thread_id'],
                'user_ids' => $user_ids,
                'last_delivered' => 'hace ' . human_time_diff(strtotime($row['last_delivered']), current_time('timestamp')),
                'last_message' => array(
                    'message' => $row['message'],
                    'date_sent' => $date_sent_human_readable
                ),
                'unread_count' => $unread_count // Agregar unread_count al array principal
            );
        }

        // Devolver los resultados como JSON
        return rest_ensure_response($chats);
    } else {
        // Devolver un mensaje de error si no se encontraron chats
        return new WP_Error('no_chats', 'No se encontraron chats.', array('status' => 404));
    }
}

?>