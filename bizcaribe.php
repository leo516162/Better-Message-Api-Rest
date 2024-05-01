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
}

function bizcaribe_admin_page_content() {
    ?>
    <div class="wrap">
        <h1>Bienvenido a Bizcaribe</h1>
        <p>Aquí puedes administrar tu plugin Bizcaribe.</p>
        <form method="post">
            <input type="hidden" name="bizcaribe_generate_credentials" value="true">
            <button type="submit" class="button button-primary">Generar Credenciales</button>
        </form>
        <?php
    
    // Mostrar mensaje de advertencia si se intenta regenerar credenciales
    if (isset($_POST['bizcaribe_generate_credentials']) && $_POST['bizcaribe_generate_credentials'] === 'true') {
        // Verificar si ya existen credenciales generadas
        $public_key_existente = get_option('bizcaribe_public_key');
        $secret_key_existente = get_option('bizcaribe_secret_key');

        if ($public_key_existente && $secret_key_existente) {
            // Mostrar un mensaje de advertencia
            echo '<div class="notice notice-warning"><p>Al generar nuevas credenciales, las credenciales antiguas serán invalidadas. Asegúrate de actualizar cualquier sistema que esté utilizando las credenciales antiguas.</p></div>';
        }

        // Generar nuevas credenciales
        $nueva_clave_publica = base64_encode(get_bloginfo('name')); // Codificar el nombre del sitio como clave pública
        $nueva_clave_secreta = wp_generate_password(20, false); // Generar una clave secreta aleatoria

        // Guardar las nuevas credenciales en la base de datos
        update_option('bizcaribe_public_key', $nueva_clave_publica);
        update_option('bizcaribe_secret_key', $nueva_clave_secreta);
        ?>
        <div class="credentials">
            <p>Credenciales generadas correctamente:</p>
            <p><strong>Clave Pública:</strong> <?php echo $nueva_clave_publica; ?></p>
            <p><strong>Clave Secreta:</strong> <?php echo $nueva_clave_secreta; ?></p>
        </div>
        <?php
    }
    ?>
    </div>
    <style>
        .wrap {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .wrap h1 {
            margin-top: 0;
        }

        .wrap p {
            margin-bottom: 20px;
        }

        .button-primary {
            background-color: #0073aa;
            border-color: #0073aa;
            text-shadow: none;
            box-shadow: none;
        }

        .credentials {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }

        .credentials p {
            margin: 0 0 10px;
        }

        .credentials strong {
            color: #0073aa;
        }
    </style>
    <?php
}
?>