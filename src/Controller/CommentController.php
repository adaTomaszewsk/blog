<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\Type\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserInterface $currentUser;
    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->currentUser = $security->getUser();
    }

    #[Route('/comment/{post}', name: 'app_comment')]
    public function getAllComments(Post $post, Request $request) :Response
    {

        $comments = $this->entityManager->getRepository(Comment::class)->findBy(['post' => $post]);
        $comment= new Comment();
        $form= $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedBy($this->currentUser);
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setPost($post);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_comment', ['post' => $post->getId()]);
        }
        return
            $this->render('blog/post/comment.html.twig', [
                'comments' => $comments,
                'post' => $post,
                'form'=> $form,
            ]);
    }
}
