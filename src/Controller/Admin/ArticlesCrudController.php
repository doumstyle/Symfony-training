<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticlesCrudController extends AbstractCrudController
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }

    public function persistEntity(EntityManagerInterface $entityMgmt, $entityInstance): void
    {
        if (!$entityInstance instanceof Articles)
            return;
        $user = $this->security->getUser();
        $entityInstance->setUser($user);

        parent::persistEntity($entityMgmt, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('title'),
            TextareaField::new('content'),
            ImageField::new('image')->setBasePath('images/articles/')->setUploadDir('/public/images/articles/')->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('category')->setFormTypeOption('choice_label', 'name')->setLabel('Categories')->formatValue(function ($value, $entity) {
                return implode('/', $entity->getCategory()->map(function ($category) {
                    return $category->getName();
                })->toArray());
            }),
            AssociationField::new('user')->hideOnForm()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}
