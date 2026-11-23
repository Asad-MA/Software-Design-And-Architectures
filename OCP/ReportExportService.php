<?php
// require_once __DIR__ . '/ReportExporter.php';
// namespace OCP\ReportExporterService;



final class ReportExportService {
    public function __construct(private ReportExporter $exporter)
    {
        
    }

    public function exportReport(){
        echo 'Start<br>';
        echo $this->exporter->serialize().'<br>';
        echo $this->exporter->export().'<br>';
    }
}