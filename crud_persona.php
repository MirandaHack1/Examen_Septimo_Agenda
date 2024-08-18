<?php
include('config.php');
$post = json_decode(file_get_contents("php://input"), true);

// //FUNCION INSERTAR
// if ($post['accion'] == "insertar") {
//     $sentencia = sprintf("INSERT INTO persona (ci_persona, nom_persona, ape_persona, clave_persona, correo_persona)values('%s', '%s', '%s', '%s', '%s')",$post = ['cedula'], $post = ['nombre'], $post = ['apellido'], $post = ['clave'], $post = ['email']);
//     $result = mysqli_query($conexion, $sentencia);
//     if ($result) {
//         $respuesta = json_encode(array('estado' => true, "mensaje" => "DATOS:GUARDADOS CORRECTAMENTE"));
//     } else {
//         $respuesta = json_encode(array('estado' => false, "mensaje" => "ERROR: DATOS NO GUARDADOS"));
//     }
//     echo $respuesta;
// }

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




//FUNCION CONSULTAR
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
