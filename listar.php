<?php
/**
*@author bauerpy
 */

$anio = $_GET['anio'];
require_once('conexion.php');
$v_query = "SELECT a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, a.anio,  sum( monto) monto FROM (SELECT  a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,  a.monto_adjudicado  monto
FROM contratos a where SUBSTRING( a.fecha_firma_contrato, 1, 4 ) = $anio
union all
SELECT  a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,   a.monto_adjudicado  monto
FROM `contratos2012-2014` a where SUBSTRING( a.fecha_firma_contrato, 1, 4 ) = $anio
) a 
GROUP BY a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, anio 
ORDER BY a.monto desc
limit 10";
$v_result = mysql_query($v_query);
$data = array();
while ($fila = mysql_fetch_assoc($v_result)) {
    $data[] = array_map("utf8_encode",$fila);
}
$json_data = json_encode($data);

echo '[
  {
    "key": "Series 2",
    "color": "#4f99b4",
    "values": '.$json_data.'}]';