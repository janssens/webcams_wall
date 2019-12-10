<?php

include __DIR__.'/vendor/autoload.php';
include __DIR__.'/inc/App.php';
use Symfony\Component\Yaml\Yaml;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$errors = App::sysCheck(__DIR__);
if ($errors)
    die($errors);

$app = new App(Yaml::parseFile(__DIR__.'/data/app.yaml'));
$cams = Yaml::parseFile(__DIR__.'/data/cameras.yaml');

try{
    $loader = new FilesystemLoader(__DIR__.'/themes/'.$app->getConf('theme').'/');
    $twig = new Environment($loader, ['cache' => __DIR__.'/var/cache',]);
    $twig->addGlobal('app', $app);
    echo $twig->render('index.html.twig', ['cams' => $cams]);
} catch (Exception $e){
    var_dump($e);
    die();
}

