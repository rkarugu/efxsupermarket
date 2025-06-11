<?php

namespace App\Interfaces;

interface ProjectInterface
{
    public function getProjects();
    public function storeProject($data);
    public function updateProject($id,$data);
    public function destroyProject($id);
}
