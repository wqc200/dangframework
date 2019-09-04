<?php

namespace Dang\Mvc\Router;

interface RouterInterface
{
    public function toUrl($param);
    public function fromUrl($url);
}
