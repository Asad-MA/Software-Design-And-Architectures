<?php

// require_once __DIR__ . '/ReportExporter.php';


final class JSONExporter implements ReportExporter{
    public function __construct()
    {
        echo '<h4>JSON Exporter is Initialized!</h4>';
    }
    public function serialize()
    {
        return 'Serialized Data JSON';
    }

    public function export(){
        return 'JSON DATA';
    }
}