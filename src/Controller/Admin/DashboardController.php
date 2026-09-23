<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Vacancy;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Vacancy');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fas fa-users')
            ->setAction('index');
        yield MenuItem::linkTo(VacancyCrudController::class, 'Vacancy', 'fas fa-briefcase')
            ->setAction('index');
        yield MenuItem::linkTo(AttributeCrudController::class, 'Attribute', 'fas fa-list')
            ->setAction('index');
        yield MenuItem::linkTo(AttributeCategoryCrudController::class, 'Category attribute', 'fas fa-folder')
            ->setAction('index');
        yield MenuItem::linkTo(UserAttributeCrudController::class, 'User attributes', 'fas fa-user-tag')
            ->setAction('index');
        // yield MenuItem::linkTo(SomeCrudController::class, 'The Label', 'fas fa-list');
    }
}
