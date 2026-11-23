<?php

// require_once __DIR__ . '/ReportExporter.php';


final class CSVExporter implements ReportExporter{

    public function __construct()
    {
        echo '<h4>CSV Exporter is Initialized!</h4>';
    }

    public function serialize()
    {
        return 'Serialized Data CSV';
    }

    public function export(){
        return 'CSV DATA';
    }
}