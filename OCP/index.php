<?php


require_once __DIR__ . '/ReportExporter.php';
require_once __DIR__ . '/ReportExportService.php';
require_once __DIR__ . '/Exporters/CSVExporter.php';
require_once __DIR__ . '/Exporters/JSONExporter.php';
require_once __DIR__ . '/Exporters/XMLExporter.php';

///echo __DIR__;

$type =  $_GET['exporter'];

function exporterFactory(string $type) : ReportExporter{
    switch ($type) {
    case 'json':
        $exporter = new JSONExporter();
        break;

    case 'csv':
        $exporter = new CSVExporter();
        break;

    case 'xml':
        $exporter = new XMLExporter();
        break;
    
    default:
        $exporter = new JSONExporter();
        break;
    }

    return $exporter;
}
//echo $type;
$e = exporterFactory($type);

//print_r($e);

$exportService = new ReportExportService($e);

$exportService->exportReport();