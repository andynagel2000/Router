<?php

namespace Controller;

class PatientsController
{
    public function index()
    {
        echo 'displaying patient index';
    }

    public function get($patientId)
    {
        echo 'getting patient ' . $patientId;
    }

    public function create()
    {
        echo 'theoretically creating a patient with contents of $_POST';
    }

    public function update($patientId)
    {
        echo 'updating patient ' . $patientId;
    }

    public function delete($patientId)
    {
        echo 'deleting patient ' . $patientId;
    }
}
