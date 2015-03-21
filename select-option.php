<?php
/**
*@author bauerpy
 */


$lugar = $_GET['select'];
require_once('conexion.php');

if($lugar == 'convocante'){
    $v_query = "SELECT  a.convocante_nombre
                        FROM (
                        SELECT DISTINCT  `convocante_nombre` 
                        FROM  `contratos` 
                        UNION 
                        SELECT DISTINCT  `convocante` 
                        FROM  `contratos2012-2014`
                        )a";
}elseif($lugar == 'categoria'){
    $v_query = "SELECT DISTINCT a.categoria_nombre, a.categoria_id
                        FROM (

                        SELECT DISTINCT categoria_nombre, categoria_id
                        FROM  `contratos` 
                        UNION 
                        SELECT DISTINCT categoria, categoria_id
                        FROM  `contratos2012-2014`
                        )a
                        ORDER BY  `a`.`categoria_nombre` ASC";
}elseif($lugar == 'tipo'){
    $v_query = "SELECT DISTINCT a.tipo_procedimiento_nombre, a.tipo_procedimiento_id
                        FROM (

                        SELECT DISTINCT tipo_procedimiento_nombre, tipo_procedimiento_id
                        FROM  `contratos` 
                        UNION 
                        SELECT DISTINCT tipo_procedimiento, tipo_procedimiento_id
                        FROM  `contratos2012-2014`
                        )a
                        ORDER BY  `a`.`tipo_procedimiento_nombre` ASC";
}

$v_result = mysql_query($v_query);
$data = array();
while ($fila = mysql_fetch_assoc($v_result)) {
    $data[] = array_map("utf8_encode",$fila);
}
$json_data = json_encode($data);

echo $json_data;