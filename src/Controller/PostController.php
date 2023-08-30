<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\Type\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_blog')]
    public function getPosts(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();
            if(!$posts){
                 throw $this->createNotFoundException('No post found');
            }

        return
            $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

#[Route('/post/createNew', name:'post_create_new')]
public function createNewPost(): Response
{
    $post = new Post();
    $form = $this->createForm(PostType::class, $post);

    return $this->render('blog/post/new.html.twig',[
        'form' => $form,
    ]);

}
}
