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
    $v_query = "SELECT  a.anho,count(a.proveedor_razon_social) cant, a.proveedor_razon_social as label, round(sum(case  when mid(moneda_nombre,1,1) = 'D' then  monto else 0 end)) as monto_usd, round(sum(case  when mid(moneda_nombre,1,1) =  'G' then  monto else 0 end)) as monto_pyg, round(sum(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*b.cambio end)/1000000) monto_total   FROM 

    (SELECT  a.anho, a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre,   a.monto_adjudicado  monto
    FROM contratos a where estado_codigo = 'ADJ' and (anho BETWEEN $anio_inicio and $anio_fin)  $filtro1
    union all
    SELECT   a.anho,a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda,    a.monto_adjudicado  monto
    FROM `contratos2012-2014` a where _estado = 'ADJ' AND (anho BETWEEN $anio_inicio and $anio_fin) $filtro2
    ) a , anios b where a.anho = b.anio
    GROUP BY a.proveedor_razon_social
    ORDER BY sum(case  when mid(moneda_nombre,1,1) = 'G' then monto else monto*4500 end) desc
    limit $top";
}else{
    $v_query = "SELECT  a.codigo_contratacion as label, a.anho, a.moneda_codigo,  a.id_llamado, a.nombre_licitacion, a.convocante_nombre, a.proveedor_razon_social,  round(case  when mid(moneda_nombre,1,1) = 'G'  then  monto else monto*b.cambio end/1000000) monto_total,  monto monto_total_2    FROM 

    (SELECT   a.codigo_contratacion, a.anho, a.moneda_codigo, a.id, a.id_llamado, a.nombre_licitacion, a.convocante_nombre , a.proveedor_razon_social, a.proveedor_ruc, a.tipo_procedimiento_nombre, a.categoria_nombre, a.moneda_nombre,  a.monto_adjudicado  monto
    FROM contratos a where estado_codigo = 'ADJ' AND (anho BETWEEN $anio_inicio and $anio_fin) $filtro1
    union all
    SELECT  a.codigo_contratacion, a.anho, a._moneda,  a .id, a.id_llamado, a.nombre_licitacion, a.convocante, a.razon_social, a.ruc, a.tipo_procedimiento, a.categoria, a.moneda,   a.monto_adjudicado  monto
    FROM `contratos2012-2014` a where _estado = 'ADJ' AND (anho BETWEEN $anio_inicio and $anio_fin) $filtro2
    ) a  , anios b where a.anho = b.anio
    ORDER BY case  when mid(moneda_nombre,1,1) = 'G' then monto else monto*4500 end desc
    limit $top";
}
//echo $v_query;

$v_result = mysql_query($v_query);
$data = array();
while ($fila = mysql_fetch_assoc($v_result)) {
    $fila = array_map("utf8_encode",$fila);
    $fila['llamados'] = array();
    if($lugar == 'empresas'){
        $v_query = "select a.* from (select a.codigo_contratacion, a.id_llamado, a.moneda_codigo, a.monto_adjudicado from contratos a where 
                            estado_codigo = 'ADJ' AND (anho BETWEEN $anio_inicio and $anio_fin) $filtro1 and a.proveedor_razon_social = \"".$fila['label']."\"
                        union all
                        select a.codigo_contratacion, a.id_llamado, a._moneda, a.monto_adjudicado from `contratos2012-2014` a where 
                        _estado = 'ADJ' AND (anho BETWEEN $anio_inicio and $anio_fin) $filtro2 and a.razon_social = \"".$fila['label']."\"
                        ) a order by a.monto_adjudicado";
        $v_result2 = mysql_query($v_query);
        //echo $v_query;
        while ($fila2 = mysql_fetch_assoc($v_result2)) {
            $fila['llamados'][] = $fila2;
        }
        //print_r($fila);
    }
    $data[] = $fila;
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