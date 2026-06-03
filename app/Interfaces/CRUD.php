<?php

namespace App\Interfaces;

interface CRUD
{
    public function getList($request);

    public function getDetail($id);

    public function saveData($request);

    public function updateData($request, $id);

    public function deleteData($id);
}
