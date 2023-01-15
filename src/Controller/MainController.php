<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request, EntityManagerInterface $em, MessageRepository $messageRepo): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        $messages = $messageRepo->findAll();


        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('app_main');
        }


        return $this->render('main/index.html.twig', ['form' => $form->createView(), 'messages' => $messages]);
    }

    #[Route('/message/reset',name:'app_message_reset',methods:"delete")]
    public function resetTchat(EntityManagerInterface $em){
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('message'));
        return $this->redirectToRoute('app_main');
    }
}
