<?php
include('config.php');
$post = json_decode(file_get_contents("php://input"), true);


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
