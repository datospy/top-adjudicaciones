<?php
/**
*@author bauerpy
 */
require_once('conexion.php');


$lugar = $_GET['lugar'];
$anio_inicio = $_GET['anio_inicio'];
$anio_fin  = $_GET['anio_fin'];
$convocante  = utf8_decode($_GET['convocante']);
$categoria  = utf8_decode($_GET['categoria']);
$tipo  = utf8_decode($_GET['tipo']);
$top  = $_GET['top'];

$filtro1 = '';
$filtro2 = '';
//filtros
if($convocante != 'todos'){
    $filtro1 .= " and convocante_nombre = '$convocante'";
    $filtro2 .= " and convocante = '$convocante'";
}
if($categoria != 'todos'){
    $filtro1 .= " and categoria_id = '$categoria'";
    $filtro2 .= " and categoria_id = '$categoria'";
}
if($tipo != 'todos'){
    $filtro1 .= " and tipo_procedimiento_id = '$tipo'";
    $filtro2 .= " and tipo_procedimiento_id = '$tipo'";
}



if($lugar == 'empresas'){
    $v_query = "SELECT count(a.proveedor_razon_social) cant, a.proveedor_razon_social as label, round(sum(case  when mid(moneda_nombre,1,1) = 'D' then  monto else 0 end)) as monto_usd, round(sum(case  when mid(moneda_nombre,1,1) =  'G' then  monto else 0 end)) as monto_pyg, round(sum(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*4500 end)/1000000) monto_total   FROM 

    (SELECT  a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,  a.monto_adjudicado  monto
    FROM contratos a where (anho BETWEEN $anio_inicio and $anio_fin)  $filtro1
    union all
    SELECT  a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,   a.monto_adjudicado  monto
    FROM `contratos2012-2014` a where (anho BETWEEN $anio_inicio and $anio_fin) $filtro2
    ) a 
    GROUP BY a.proveedor_razon_social
    ORDER BY sum(case  when mid(moneda_nombre,1,1) = 'G' then monto else monto*4500 end) desc
    limit $top";
}else{
    $v_query = "SELECT  a.moneda_codigo,  a.id_llamado, a.nombre_licitacion, a.convocante_nombre, concat(a.id, a.convocante_nombre) as label, a.proveedor_razon_social,  round(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*4500 end/1000000) monto_total, round(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*4500 end) monto_total_2    FROM 

    (SELECT   a.moneda_codigo, a.id, a.id_llamado, a.nombre_licitacion, a.convocante_nombre , a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,  a.monto_adjudicado  monto
    FROM contratos a where (anho BETWEEN $anio_inicio and $anio_fin) $filtro1
    union all
    SELECT  a._moneda, a.id, a.id_llamado, a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda, SUBSTRING( a.fecha_firma_contrato, 1, 4 ) anio,   a.monto_adjudicado  monto
    FROM `contratos2012-2014` a where (anho BETWEEN $anio_inicio and $anio_fin) $filtro2
    ) a 
    ORDER BY case  when mid(moneda_nombre,1,1) = 'G' then monto else monto*4500 end desc
    limit $top";
}
//echo $v_query;

$v_result = mysql_query($v_query);
$data = array();
while ($fila = mysql_fetch_assoc($v_result)) {
    $data[] = array_map("utf8_encode",$fila);
}
if(count($data)>0){
    $json_data = json_encode($data);
}else{
    $json_data = '[]';
}
echo '[
  {
    "key": "Proveedor",
    "values": '.$json_data.'}]';