<?php

namespace App\Controller;

use App\Entity\TODO;
use App\Form\TODOType;
use App\Repository\TODORepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TODOController extends AbstractController
{
    /**
     * @Route("/", name="todo_index", methods={"GET"})
     */
    public function index(Request $request, TODORepository $tODORepository): Response
    {
        $status = $request->query->get('status');
        if ($status === "afaire") {
            $todos = $tODORepository->findBy(['status' => false]);
        } elseif ($status === 'terminee') {
            $todos = $tODORepository->findBy(['status' => true]);
        } else {
            $todos = $tODORepository->findAll();
        }

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * @Route("/new", name="todo_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $todo = new TODO();
        $form = $this->createForm(TODOType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setCreatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('todo_index');
        }

        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="todo_show", methods={"GET"})
     */
    public function show(TODO $todo): Response
    {
        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="todo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TODO $todo): Response
    {
        $form = $this->createForm(TODOType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('todo_index');
        }

        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="todo_delete", methods={"POST"})
     */
    public function delete(Request $request, TODO $todo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($todo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('todo_index');
    }
}


