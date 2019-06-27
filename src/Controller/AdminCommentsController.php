<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentsController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $repo, Pagination $pagination, $page)
    {
        $pagination->setEntityClass(Comment::class)
                   ->setPage($page);

        return $this->render('admin/comments/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet de modifier un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * 
     * @param Comment $comment
     * @param ObjectManager $manager
     * @param Request $request
     * 
     * @return Response
     */ 
    public function edit(Comment $comment, ObjectManager $manager, Request $request) {

        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire n° {$comment->getId()} a bien été modifié !"
            );

            return $this->redirectToRoute('admin_comment_index');
        }


        return $this->render('admin/comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     *
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager) {
        $manager->remove($comment);
        $manager->flush();
        
        $this->addFlash(
            'success',
            "Le commentaire a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comment_index');
    }
}
