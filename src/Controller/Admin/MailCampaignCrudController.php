<?php

namespace App\Controller\Admin;

use App\Entity\MailCampaign;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


class MailCampaignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MailCampaign::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('categories')->setFormTypeOptions([
                'multiple' => true,
                'expanded' => true,
            ]),
            TextField::new('subject'),
            TextEditorField::new('content'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('sentAt')->onlyOnIndex()
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $sendEmailAction = Action::new('send', 'Send')
            ->linkToRoute('admin_send_mail_campaign', function (MailCampaign $mailCampaign) {
                return ['id' => $mailCampaign->getId()];
            })
            ->setCssClass('btn btn-primary');

        return $actions
            ->add(Crud::PAGE_INDEX, $sendEmailAction)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            });
    }

    #[Route('/admin/mail-campaign/send/{id}', name: 'admin_send_mail_campaign')]
    public function sendEmail(int $id , UserRepository $userRepository, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $mailCampaign = $entityManager->getRepository(MailCampaign::class)->find($id);
        $users = $userRepository->findByCategories(($mailCampaign->getCategories()->toArray()));

        foreach ($users as $user) {
            $personalizedMessage = str_replace(
                ['{firstName}', '{lastName}'],
                [$user->getName(), $user->getSurname()],
                $mailCampaign->getContent()
            );

            $email = (new TemplatedEmail())
                ->from('noreply@example.com')
                ->to($user->getEmail())
                ->subject($mailCampaign->getSubject())
                ->html($personalizedMessage);

            $mailer->send($email);
        }

        $mailCampaign->setSentAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', 'Mail campaign sent successfully!');

        return $this->redirect($this->generateUrl('admin', [
            'crudControllerFqcn' => self::class,
        ]));
    }
}
