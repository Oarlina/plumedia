<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\Image;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{

    // use Trait\ReadOnlyTrait;
    use Trait\ChangeOnlyTrait;

    #[Route('Admin/User', name:'admin_user')]
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('pseudo'),
            EmailField::new('email')->hideOnIndex(),
            BooleanField::new('isVerified')->onlyOnIndex(),
            BooleanField::new('password')->onlyOnIndex(),
            ImageField::new('avatar')->setBasePath('uploads/user')->setUploadDir('public/uploads/user')->setUploadedFileNamePattern('user_[slug].[extension]')->setRequired(false),
            ArrayField::new('roles')->hideOnIndex(),
            DateTimeField::new('createAccount')->hideOnIndex()
        ];
    }


}
