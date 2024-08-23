<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractDashboardController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/admin/send-email', name: 'admin_send_email')]
    public function sendEmail(Request $request, EntityManagerInterface $em, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('categories', ChoiceType::class, [
                'choices' => $em->getRepository(Category::class)->findAll(),
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('message', TextareaType::class,[
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $selectedCategories = $data['categories'];
            $messageContent = $data['message'];

            $users = $userRepository->findByCategories($selectedCategories);

            foreach ($users as $user) {
                $personalizedMessage = str_replace(
                    ['{firstName}', '{lastName}'],
                    [$user->getName(), $user->getSurname()],
                    $messageContent
                );

                $email = (new TemplatedEmail())
                    ->from('noreply@example.com')
                    ->to($user->getEmail())
                    ->subject('Message to You')
                    ->html($personalizedMessage);

                $mailer->send($email);
            }

            $this->addFlash('success', 'E-mails have been sent!');

            return $this->redirectToRoute('admin_send_email');
        }

        return $this->render('admin/email/send_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}