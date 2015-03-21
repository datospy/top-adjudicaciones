<?php
/**
*@author bauerpy
 */
require_once  'constantes.php';

$conexion = mysql_connect(HOST, USUARIO, PASS);
$db = mysql_select_db(BD, $conexion);