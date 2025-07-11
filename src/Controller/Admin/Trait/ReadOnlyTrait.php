<?php

namespace App\Controller\Admin\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

trait ReadOnlyTrait{
    
    public function configureActions(Actions $actions): Actions{
        // on donne des actions spécifique au trait ReadOnlyTrait
        $actions
            // on supprime les actions d'ajout de suppression et de mise à jour
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            // et on ajoute l'accès à la page de liste et ces détails
            ->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }
}