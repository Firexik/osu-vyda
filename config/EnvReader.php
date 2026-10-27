<?php

function read_env(){
    // Create this .env file in the project root. And fill it with your credentials
    // keys:
    // i.e.:
    // 'DB_USER'='root'
    return parse_ini_file('.env');
}