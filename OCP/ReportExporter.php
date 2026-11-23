<?php


interface ReportExporter {
    public function serialize();

    public function export();
}