<?php

namespace Controller;

class PatientsMetricsController
{
    public function index($patientId)
    {
        echo 'displaying patient ' . $patientId . ' metrics index';
    }

    public function get($patientId, $metricId, $flag = null)
    {
        echo 'getting patient ' . $patientId . ' metrics ' . $metricId . (!empty($flag) ? ' with flagParam ' . $flag : '');
    }

    public function create($patientId)
    {
        echo 'theoretically creating a patient ' . $patientId . ' a new metric with contents of $_POST';
    }

    public function update($patientId, $metricId)
    {
        echo 'updating patient ' . $patientId . ' metric ' . $metricId;
    }

    public function delete($patientId, $metricId)
    {
        echo 'deleting patient ' . $patientId . ' metric ' . $metricId;;
    }
}
