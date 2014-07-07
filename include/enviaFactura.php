<?php
/* 
 * Autor:   Juan Carrillo
 * Fecha:   Julio 6, 2014
 * Proyecto: Comprobantes Electronicos
 */
//var_dump($GLOBALS);
    session_start();
    include 'conectaBaseDatos.php';
if (isset($_POST['Archivo'])) {
    $archivo = str_replace("},", "}|", $_POST['Archivo']);
    $archi = explode("|", $archivo);
    for($i=0; $i < count($archi); $i++) {
    $registro = json_decode($archi[$i]);
//    var_dump($factura[$i]);
    if(strlen($archi[$i]) != 0){
        foreach ($registro as $key => $value) {
//            echo "Estos Datos {$key} is {$value}\n";
            if($key === "Nombre_Archivo"){
                $wk_archivo = $value;
            } elseif ($key === "Generado") {
                $wk_generado = $value;
            } elseif ($key === "Descargado") {
                $wk_descargado = $value;
            } elseif ($key === "Procesado") {
                $wk_procesado = $value;
                chkArchivo($wk_archivo, $wk_generado, $wk_descargado, $wk_procesado);
            }
        }
    }
    }
} 

function chkArchivo($wk_archivo, $wk_generado, $wk_descargado, $wk_procesado) {
    $db = db_connect();
    if ($db->connect_errno) {
        die('Error de Conexion: ' . $db->connect_errno);
    }
//    echo "Numero: " . $wk_factura . " \n";
    $stmt = "";
    $sql = "select ArchivoNombre, ArchivoGeerado, ArchivoDescargado, ArchivoProcesado from Archivo rwhere ArchivoNombre = ?";
    $stmt = $db->prepare($sql) or die(mysqli_error($db));
   
    $stmt->bind_param("s", $wk_archivo);
    $flag = FALSE;
    $existe = $stmt->execute();
    $stmt->bind_result($db_archivo, $db_generado, $db_descargado, $db_procesado);        /* fetch values */
            while ($stmt->fetch()) {
                $flag = TRUE;
            }
    $stmt->close();
    $db->close();
            if($flag){
                $control = chkArchivo($wk_archivo, $wk_generado, $wk_descargado, $wk_procesado);
                if($control){
                    require 'paraContinuar.html';
                    echo '<script type="text/javascript">'.
                            "$(document).ready(function(){".
                            "$('#mensaje').html('<span style='color:red'>Se ha seleccionado las facturas</span>');".
                            "})".
                            "</script>";
                    exit();
                    }
                    
                }
 }

function updateFactura($wk_factura, $wk_cliente, $wk_valor) {
    $db = db_connect();
    if ($db->connect_errno) {
        die('Error de Conexion: ' . $db->connect_errno);
    }
    $stmt = "";
    $sql = "UPDATE invoice SET CustomField10 = 'SELECCIONADA' where TxnNumber=?";
    $stmt = $db->prepare($sql) or die(mysqli_error($db));
   
    $stmt->bind_param("s", $wk_factura);
    $flag = FALSE;
    $existe = $stmt->execute();
    $nroRegistrosAfectados = $stmt->affected_rows;
    if ($nroRegistrosAfectados > 0) {
        $flag = TRUE;
    }
        /* close statement */
    $stmt->close();
    $db->close();
    return $flag;
}
