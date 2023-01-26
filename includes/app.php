<?php 

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::CreateImmutable(__DIR__);
$dotenv->safeload();
require 'funciones.php';
require 'database.php';

// Conectarnos a la base de datos
use Model\ActiveRecord;
use Dotenv\Dotenv;
ActiveRecord::setDB($db);