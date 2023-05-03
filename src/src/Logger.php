<?php

namespace Codilar\Witch;

class Logger
{

    public function log($message, $lineBreak = false)
    {
        echo sprintf("\n[%s]: %s", date('Y-m-d H:i:s'), $message);
        if ($lineBreak) {
            echo "\n\n";
        }
    }

}
