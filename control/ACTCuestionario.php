<?php
/**
 *@package pXP
 *@file gen-ACTCuestionario.php
 *@author  (mguerra)
 *@date 21-04-2020 08:31:41
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
#0				21-04-2020 08:31:41								CREACION
#16            19/05/2020				manuel guerra		correcciones en correo

 */
require_once(dirname(__FILE__).'/../reportes/RReporteCuestionario.php');
require_once(dirname(__FILE__).'/../reportes/RReporteGeneral.php');
class ACTCuestionario extends ACTbase{

    function listarCuestionario(){
        $this->objParam->defecto('ordenacion','id_cuestionario');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('pes_estado')=='borrador'){
            $this->objParam->addFiltro("cue.estado in (''borrador'')");
        }
        if($this->objParam->getParametro('pes_estado')=='enviado'){
            $this->objParam->addFiltro("cue.estado in (''enviado'')");
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODCuestionario','listarCuestionario');
        } else{
            $this->objFunc=$this->create('MODCuestionario');

            $this->res=$this->objFunc->listarCuestionario($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarCuestionario(){
        $this->objFunc=$this->create('MODCuestionario');
        if($this->objParam->insertar('id_cuestionario')){
            $this->res=$this->objFunc->insertarCuestionario($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarCuestionario($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarCuestionario(){
        $this->objFunc=$this->create('MODCuestionario');
        $this->res=$this->objFunc->eliminarCuestionario($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    //
    function enviarCorreo(){
        //#16
        $var = '<a href="http://' . $_SESSION['_HOST_SERVIDOR'] . $_SESSION['_FOLDER'] . '/sis_seguridad/vista/_adm/index.php#alerta:ID_ALARMA">TEXTO CUSTUMIZADO</a>';
        $this->objParam->addParametro('url_alarma',$var);

        $this->objFunc=$this->create('MODCuestionario');
        $this->res=$this->objFunc->enviarCorreo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    //
    function finCuestionario(){
        $this->objFunc=$this->create('MODCuestionario');
        $this->res=$this->objFunc->finCuestionario($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    //
    function listarRepCuestionario(){
        $this->objParam->defecto('ordenacion','id_encuesta');
        $this->objParam->defecto('dir_ordenacion','asc');

        $this->objFunc=$this->create('MODCuestionario');
        $this->res=$this->objFunc->listarRepCuestionario($this->objParam);
        $respuesta = $this->res->getDatos();
        array_unshift ( $respuesta, array(  'id_encuesta'=>'0',
            'nombre'=>'RESUMEN GENERAL EVALUACION DESEMPEÑO VALORES 360 GRADOS'));
        $this->res->setDatos($respuesta);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function reporteCuestionario(){

        $this->objFunc = $this->create('MODCuestionario');

        if($this->objParam->getParametro('id_encuesta') != 0) {
            $this->res = $this->objFunc->reporteCuestionario($this->objParam);
            $titulo = 'Reporte Encuesta';
            $nombreArchivo = uniqid(md5(session_id()) . $titulo);
            $nombreArchivo .= '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->datos);
            $this->objReporteFormato = new RReporteCuestionario($this->objParam);
            $this->objReporteFormato->generarDatos();
            $this->objReporteFormato->generarReporte();
        }else{
            $this->res = $this->objFunc->reporteCuestionarioGeneral($this->objParam);
            $titulo = 'Reporte General';
            $nombreArchivo = uniqid(md5(session_id()) . $titulo);
            $nombreArchivo .= '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->datos);
            $this->objReporteFormato = new RReporteGeneral($this->objParam);
            $this->objReporteFormato->generarDatos();
            $this->objReporteFormato->generarReporte();
        }


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
}

?>