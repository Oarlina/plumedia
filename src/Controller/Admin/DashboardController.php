<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Story;
use App\Entity\Category;
use App\Controller\Admin\User2CrudController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Plumedia');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Accueil', 'fa fa-home', 'app_home');
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-table-columns');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-icons', Category::class);
        yield MenuItem::linkToCrud('Histoires', 'fa fa-book', Story::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
