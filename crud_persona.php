<?php
include('config.php');
$post = json_decode(file_get_contents("php://input"), true);

/******************************************************************************************FUNCION CONSULTAR CONTACTOS POR CODIGO DE PERSONA***************************************************************************/
if ($post['accion'] == "consultarC") {

    // Verificar si el código del usuario está presente en la solicitud
    if (isset($post['codigo_usuario'])) {
        $codigo_usuario = mysqli_real_escape_string($mysqli, $post['codigo_usuario']);

        // Consulta para obtener los contactos asociados al código del usuario
        $sentencia = sprintf("SELECT * FROM contacto WHERE persona_cod_persona='%s'", $codigo_usuario);
        $result = mysqli_query($mysqli, $sentencia);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $contactos[] = array(
                    'cod_contacto' => $row['cod_contacto'],
                    'nom_contacto' => $row['nom_contacto'],
                    'ape_contacto' => $row['ape_contacto'],
                    'telefono_contacto' => $row['telefono_contacto'],
                    'email_contacto' => $row['email_contacto']
                );
            }
            $respuesta = json_encode(array('estado' => true, "contactos" => $contactos));
        } else {
            $respuesta = json_encode(array('estado' => false, "mensaje" => "No hay contactos asociados a este usuario"));
        }
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Código de usuario no proporcionado"));
    }

    echo $respuesta;
}
/*********************************************************************************************************************************************************************************************************************/







/******************************************************************************************FUNCION INICIAR SESION*************************************************************************************************/
if ($post['accion'] == "iniciarSesion") {
    $correo = mysqli_real_escape_string($mysqli, $post['correo']);
    $clave = mysqli_real_escape_string($mysqli, $post['clave']);

    $sentencia = sprintf("SELECT * FROM persona WHERE correo_persona='%s'", $correo);
    $result = mysqli_query($mysqli, $sentencia);

    if (mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_array($result);

        $intentos_fallidos = $usuario['intentos_fallidos'];
        $ultima_fecha = $usuario['ultima_fecha'];
        $fecha_actual = date("Y-m-d H:i:s"); // Fecha actual en formato MySQL

        if ($intentos_fallidos >= 3 && (strtotime($fecha_actual) - strtotime($ultima_fecha)) < 3600) {
            $respuesta = json_encode(array(
                'estado' => false,
                "mensaje" => "Cuenta bloqueada. Inténtelo de nuevo más tarde."
            ));
        } else {
            if ($usuario['clave_persona'] === $clave) {
                $datos = array(
                    'codigo' => $usuario['cod_persona'],
                    'cedula' => $usuario['ci_persona'],
                    'nombre' => $usuario['nom_persona'],
                    'apellido' => $usuario['ape_persona'],
                    'correo' => $usuario['correo_persona']
                );
                // Reiniciar intentos fallidos y última fecha
                $sentencia_actualizar = sprintf(
                    "UPDATE persona SET intentos_fallidos=0, ultima_fecha=NULL WHERE correo_persona='%s'",
                    $correo
                );
                mysqli_query($mysqli, $sentencia_actualizar);

                $respuesta = json_encode(array(
                    'estado' => true,
                    "mensaje" => "Inicio de sesión exitoso",
                    "usuario" => $datos
                ));
            } else {
                $intentos_fallidos++;
                $sentencia_actualizar = sprintf(
                    "UPDATE persona SET intentos_fallidos=%d, ultima_fecha='%s' WHERE correo_persona='%s'",
                    $intentos_fallidos,
                    $fecha_actual,
                    $correo
                );
                mysqli_query($mysqli, $sentencia_actualizar);

                if ($intentos_fallidos >= 3) {
                    $respuesta = json_encode(array(
                        'estado' => false,
                        "mensaje" => "Cuenta bloqueada. Inténtelo de nuevo más tarde."
                    ));
                } else {
                    $respuesta = json_encode(array(
                        'estado' => false,
                        "mensaje" => "Correo o contraseña incorrectos"
                    ));
                }
            }
        }
    } else {
        $respuesta = json_encode(array(
            'estado' => false,
            "mensaje" => "Correo o contraseña incorrectos"
        ));
    }

    echo $respuesta;
}

/*********************************************************************************************************************************************************************************************************************/





/******************************************************************************************FUNCION ELIMINAR LOS DATOS************************************************************************************************/
if ($post['accion'] == "eliminar") {
    $sentencia = sprintf(
        "DELETE FROM persona WHERE cod_persona='%s'",
        mysqli_real_escape_string($mysqli, $post['codigo'])
    );
    $result = mysqli_query($mysqli, $sentencia);
    if ($result) {
        $respuesta = json_encode(array('estado' => true, "mensaje" => "Datos Eliminados Correctamente"));
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Error al Eliminar"));
    }
    echo $respuesta;
}
/*********************************************************************************************************************************************************************************************************************/


/******************************************************************************************FUNCION ACTUALIAZR LOS DATOS************************************************************************************************/
if ($post['accion'] == "actualizar") {
    $sentencia = sprintf(
        "UPDATE  persona SET ci_persona='%s', nom_persona='%s', ape_persona='%s', clave_persona='%s', correo_persona='%s'WHERE cod_persona='%s'",
        $post['cedula'],
        $post['nombre'],
        $post['apellido'],
        $post['clave'],
        $post['correo'],
        $post['codigo']
    );
    $result = mysqli_query($mysqli, $sentencia);
    if ($result) {
        $respuesta = json_encode(array('estado' => true, "mensaje" => "Datos Actualizados Correctamente"));
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Error al Actualizar"));
    }
    echo $respuesta;
}
/*********************************************************************************************************************************************************************************************************************/

/******************************************************************************************FUNCION CONSULTAR DATO DE TABLA POR CADA PERSOAN***************************************************************************/
if ($post['accion'] == "consultarDato") {

    $sentencia = sprintf("SELECT * FROM persona WHERE cod_persona='%s'", $post['codigo']);
    $result = mysqli_query($mysqli, $sentencia);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $datos[] = array(
                'codigo' => $row['cod_persona'],
                'cedula' => $row['ci_persona'],
                'nombre' => $row['nom_persona'],
                'apellido' => $row['ape_persona'],
                'clave' => $row['clave_persona'],
                'correo' => $row['correo_persona']
            );
        }
        $respupesta = json_encode(array('estado' => true, "persona" => $datos));
    } else {
        $respupesta = json_encode(array('estado' => false, "mensaje" => "No hay datos"));
    }
    echo $respupesta;
}
/*********************************************************************************************************************************************************************************************************************/

/******************************************************************************************FUNCION INSERTAR O GUARDAR*************************************************************************************************/
if ($post['accion'] == "insertar") {
    $sentencia = sprintf(
        "INSERT INTO persona (ci_persona, nom_persona, ape_persona, clave_persona, correo_persona) VALUES ('%s', '%s', '%s', '%s', '%s')",
        $post['cedula'],
        $post['nombre'],
        $post['apellido'],
        $post['clave'],
        $post['correo']
    );
    $result = mysqli_query($mysqli, $sentencia);
    if ($result) {
        $respuesta = json_encode(array('estado' => true, "mensaje" => "Datos Guardados Correctamente"));
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Error al guardar"));
    }
    echo $respuesta;
}

/*********************************************************************************************************************************************************************************************************************/

/******************************************************************************************FUNCION CONSULTAR O PARA PRESENTAR EN LA TABLA*****************************************************************************/
if ($post['accion'] == "consultar") {
    $sentencia = sprintf("SELECT* FROM persona");
    $result = mysqli_query($mysqli, $sentencia);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $datos[] = array(
                'codigo' => $row['cod_persona'],
                'cedula' => $row['ci_persona'],
                'nombre' => $row['nom_persona'],
                'apellido' => $row['ape_persona']
            );
        }
        $respuesta = json_encode(array('estado' => true, "personas" => $datos));
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Error:No hay datos"));
    }
    echo $respuesta;
}
/*********************************************************************************************************************************************************************************************************************/
