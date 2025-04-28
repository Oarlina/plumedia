<?php

namespace App\Service;

use Exception;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService {
    public function __construct(private ParameterBagInterface $params){

    }
    
    // je fais une méthode qui récupère l'image et peut aussi recuperer le nom du dossier ou elle sera enregistrer
    public function save (UploadedFile $picture, ?string $folder = ''):string{
        // je donne un nouveau nom a mon image
        $newName = uniqid().'.'.$picture->guessExtension();
        $picture->move('public/upload/user', $newName);

        return $newName;
    }

    public function __toString(){

    }
}