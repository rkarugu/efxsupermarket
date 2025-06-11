<?php

namespace App\Interfaces;

interface GLTagInterface
{
    public function getGLTags();
    public function storeGLTags($data);
    public function updateGLTags($id,$data);
    public function destroyGLTags($id);
}
