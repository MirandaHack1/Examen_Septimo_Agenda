<?php
include('config.php');
$post = json_decode(file_get_contents("php://input"), true);

if ($post['accion'] == "consultar") {
    $sentencia = sprintf("SELECT* FROM persona");
    $result = mysqli_query($mysqli, $sentencia);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $datos[] = array(
                'codigo' => $row['cod_persona'],
                'cedula' => $row['ci_persona'],
                'nombre' => $row['nom_persona'],
                'apellido' => $row['ape_persona'],
            );
        }
        $respuesta = json_encode(array('estado' => true, "personas" => $datos));
    } else {
        $respuesta = json_encode(array('estado' => false, "mensaje" => "Error:No hay datos"));
    }
    echo $respuesta;
}
