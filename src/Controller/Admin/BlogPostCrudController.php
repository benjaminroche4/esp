<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Image;

class BlogPostCrudController extends AbstractCrudController
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {

    }

    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextareaField::new('summary', 'Résumé')
                ->setMaxLength(160)
                ->setHelp('Le résumé doit contenir au maximum 160 caractères')
            ,
            TextEditorField::new('content', 'Contenu de l\'article')->onlyOnDetail(),
            ImageField::new('mainPhoto', 'Photo de l\'article')
                ->setUploadDir('public/medias/blog/')
                ->setBasePath('medias/blog/')
                ->setUploadedFileNamePattern('[slug]-[uuid].[extension]')
                ->setFileConstraints(new Image(maxSize: '160K',mimeTypes: ['image/webp']))
                ->setHelp('Max 160K, format webp seulement')
            ,
            TextField::new('altMainPhoto', 'Balise ALT photo article'),
            AssociationField::new('category', 'Catégorie')->autocomplete(),
            BooleanField::new('status', 'Publié l\'article'),
            DateTimeField::new('createdAt', 'Date de création')->setDisabled()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $slug = $this->slugger->slug($entityInstance->getTitle())->lower();

        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        $entityInstance->setSlug($slug);
        parent::persistEntity($entityManager, $entityInstance);
    }
}
