<?php
function bizcaribe_admin_page_content() {
    ?>
    <div class="wrap">
        <!-- Banner -->
    <div class="banner">
        <img src="https://bizcaribe.com/wp-content/uploads/2024/03/bizcaribe-l2-1024x398.png" alt="Banner">
        <h1>Bienvenido a Bizcaribe</h1>
        <p>Aquí puedes administrar tu plugin Bizcaribe.</p>
    </div>
    <!-- Pestañas -->
    <div class="tabs">
        <button id="tab-configuracion" class="tablinks" onclick="openTab(event, 'configuracion')">Configuración</button>
        <button class="tablinks" onclick="openTab(event, 'opciones')">Opciones</button>
        <!-- Agrega más pestañas según sea necesario -->
    </div>

    <!-- Contenido de las pestañas -->
    <div id="configuracion" class="tabcontent">
        <!-- Campos de configuración -->
        <h2>Configuración</h2>
        <form method="post" onsubmit="return confirmGenerarCredenciales()"> <!-- Aquí agregamos el evento onsubmit -->
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
        
     <?php } ?>
    
    <div id="opciones" class="tabcontent">
        <!-- Campos de opciones -->
        <h2>Opciones</h2>
        <form method="post">
            <!-- Agrega aquí los campos de opciones -->
        </form>
    </div>
    
    }
    ?>
    </div>
    <style>
        /* Estilos para el banner */
    .banner {
        background-color: #f0f0f0;
        padding: 20px;
        text-align: center;
    }
    .banner img {
        max-width: 100%;
        height: auto;
        max-height: 100px; /* Establece una altura máxima para la imagen */
    }

    /* Estilos para las pestañas */
    .tabs {
        overflow: hidden;
        border-bottom: 1px solid #ddd;
        margin-top: 20px;
    }
    .tablinks {
        background-color: #f0f0f0;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: background-color 0.3s;
    }
    .tablinks:hover {
        background-color: #ddd;
    }

    /* Estilos para el contenido de las pestañas */
    .tabcontent {
        display: none;
        padding: 20px;
    }

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

    <script>
    // Función para abrir una pestaña específica
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Función para confirmar la generación de nuevas credenciales
    function confirmGenerarCredenciales() {
        // Mostrar un mensaje de confirmación
        var confirmacion = confirm("¿Estás seguro de que deseas generar nuevas credenciales? Al hacerlo las credenciales antiguas serán invalidadas. Asegúrate de actualizar cualquier sistema que esté utilizando las credenciales antiguas");

        // Si el usuario confirma, devolver true para enviar el formulario; de lo contrario, devolver false
        return confirmacion;
    }
    
    // Activar la pestaña de configuración por defecto al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tab-configuracion').click();
    });
    
</script>

    <?php
}
?>