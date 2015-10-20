<?php

namespace BaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BaseBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
