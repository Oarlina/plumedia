<?php

namespace App\Service;

use Exception;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService {
    public function __construct(private ParameterBagInterface $params){

    }
    
    /*
    etape : Pour les uploads d'images
         creer dossier uploads pour separer les images 
        faire le uniqid afin d'eviter erreur si plus tard il faut la suppression de l'image et eviter de mettre des données sensible
        on donne une taille max au fichier pour eviter de mettre des fichiers trop lourd
         et on demande un certain format afin d'éviter de recevoir autre chose qu'une image c'est a dire eviter des failles XSS (injecter du code malveillant par exemple un fichier .jpg.php)
    */


    // je fais une méthode qui récupère l'image et peut aussi recuperer le nom du dossier ou elle sera enregistrer
    public function save(UploadedFile $picture, ?string $folder = ''):string{
        // je donne un nouveau nom a mon image en lui donnant un nom unique afin que deux user puisse avoir le meme et garanti l'uniciter entre les images
        // guessExtension permet de devinerl'extension de l'image afin que l'on n'ai pas a chercher son format plus tard
        $newName = uniqid().'.'.$picture->guessExtension();
        $picture->move('public/uploads/'.$folder, $newName);

        return $newName;
    }
        
    public function __toString(){

    }
}