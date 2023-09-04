<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\Type\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostController extends AbstractController
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

    #[Route('/', name: 'post_all_posts')]
    public function getPosts(): Response
    {
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        if(!$posts){
            throw $this->createNotFoundException('No post found');
        }

        return
            $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

#[Route('/post/createNew', name:'post_create_new')]
public function createNewPost(Request $request): Response
{
    $post = new Post();
    $form = $this->createForm(PostType::class, $post);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $post = $form->getData();
        $post->setAuthor($this->currentUser);
        $post->setCreationAt(new \DateTime('now'));
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->redirectToRoute('post_all_posts');
    }



    return $this->render('blog/post/new.html.twig',[
        'form' => $form,
        'title'=>'Create'
    ]);

}

#[Route('/post/editPost/{id}', name:'post_edit')]
public function editPost(int $id, Request $request):Response
{
        $post =  $this->entityManager->getRepository(Post::class)->findOneBy([
            'id'=> $id
]);
    $form = $this->createForm(PostType::class, $post);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $post = $form->getData();
        $post->setEditAt(new \DateTime('now'));
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->redirectToRoute('post_all_posts');
    }



    return $this->render('blog/post/new.html.twig',[
        'form' => $form,
        'title'=>'Edit'
    ]);

}

#[Route('/post/delete/{id}', name:'post_delete')]
public function deletePost(int $id, Request $request):Response
{
    $post =  $this->entityManager->getRepository(Post::class)->findOneBy([
        'id'=> $id
    ]);
    if($post) {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
        return $this->redirectToRoute('post_all_posts');

}

}
