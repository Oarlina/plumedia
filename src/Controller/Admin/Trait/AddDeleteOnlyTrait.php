<?php

namespace App\Controller\Admin\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

trait AddDeleteOnlyTrait{
    
    public function configureActions(Actions $actions): Actions{
        // on donne des actions spécifique au trait ChangeReadOnlyTrait
        $actions
            // on supprime les actions d'ajout de suppression
            ->disable(Action::EDIT)
            // et on ajoute l'accès à la page de liste et ces détails
            ->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }
}