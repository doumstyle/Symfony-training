<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\Profiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProfilesCrudController extends AbstractCrudController
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function persistEntity(EntityManagerInterface $entityMgmt, $entityInstance): void
    {
        if (!$entityInstance instanceof Profiles)
            return;
        $user = $this->security->getUser();
        $entityInstance->setUser($user);

        parent::persistEntity($entityMgmt, $entityInstance);
    }
    public static function getEntityFqcn(): string
    {
        return Profiles::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextareaField::new('description'),
            ImageField::new('picture')->setBasePath('images/profiles')->setUploadDir('/public/images/profiles')->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            DateField::new('date_of_birth'),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
            AssociationField::new('user')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}
