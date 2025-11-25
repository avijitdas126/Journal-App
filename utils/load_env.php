<?php

function load_env() {
    $envPath = __DIR__ . '/../.env';   

    if (!file_exists($envPath)) {
        die("Env file not found at: $envPath");
    }

    $file = fopen($envPath, 'r');
    if($file){
        while(($line=fgets($file))!==false){
            putenv($line);
        }
    }
    fclose($file);
}

function Env($field){
    return trim(getenv($field));
}
