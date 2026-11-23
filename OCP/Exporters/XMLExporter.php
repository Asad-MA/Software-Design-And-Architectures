<?php

final class XMLExporter implements ReportExporter{

public function __construct()
    {
        echo '<h4>XML Exporter is Initialized!</h4>';
    }

public function serialize()
    {
        return 'Serialized Data XML';
    }

    public function export(){
        return 'XML DATA';
    }
}