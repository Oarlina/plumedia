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
    public function save(UploadedFile $picture, ?string $folder = ''):string{
        // je donne un nouveau nom a mon image en lui donnant un nom unique 
        // afin que deux user puisse avoir le meme et garanti l'uniciter entre les images
        // guessExtension permet de devinerl'extension de l'image afin que l'on n'ai pas a chercher son format plus tard
        
        $newName = $folder.'-'.uniqid().'.'.$picture->guessExtension();
        $picture->move('uploads/'.$folder.'/', $newName);

        return $newName;
    }

}