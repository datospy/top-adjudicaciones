<?php
/**
*@author bauerpy
 */



$anio = $_GET['anio'];
require_once('conexion.php');
$v_query = "SELECT count(a.proveedor_razon_social) cant, a.proveedor_razon_social, round(sum(case  when mid(moneda_nombre,1,1) = 'D' then  monto else 0 end)) as monto_usd, round(sum(case  when mid(moneda_nombre,1,1) =  'G' then  monto else 0 end)) as monto_pyg, round(sum(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*4500 end)) monto_total   FROM 
    
(SELECT  a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,  a.monto_adjudicado  monto
FROM contratos a where SUBSTRING( a.fecha_firma_contrato, 1, 4 ) = $anio
union all
SELECT  a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,   a.monto_adjudicado  monto
FROM `contratos2012-2014` a where SUBSTRING( a.fecha_firma_contrato, 1, 4 ) = $anio
) a 
GROUP BY a.proveedor_razon_social
ORDER BY sum(case  when mid(moneda_nombre,1,1) = 'G' then monto else monto*4500 end) desc
limit 10";

$v_result = mysql_query($v_query);
$data = array();
while ($fila = mysql_fetch_assoc($v_result)) {
    $data[] = array_map("utf8_encode",$fila);
}
$json_data = json_encode($data);

echo '[
  {
    "key": "Proveedor",
    "values": '.$json_data.'}]';