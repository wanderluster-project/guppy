<?php

namespace Guppy;

class ShellExecutor
{
    public function exec($cmd, $variables){
        $escapedVars = [];
        foreach($variables as $variable){
            $escapedVars[] = escapeshellarg($variable);
        }
        $escapedCmd =  vsprintf($cmd, $escapedVars);
        exec($escapedCmd);
    }
}