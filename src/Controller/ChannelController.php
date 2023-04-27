<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Form\ChannelType;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/channel")
 */
class ChannelController extends AbstractController
{
    /**
     * @Route("/", name="app_channel_index", methods={"GET"})
     */
    public function index(ChannelRepository $channelRepository): Response
    {
        return $this->render('channel/index.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_channel_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ChannelRepository $channelRepository): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $channelRepository->add($channel, true);

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('channel/new.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_channel_show", methods={"GET"})
     */
    public function show(Channel $channel, MessageRepository $messageRepository): Response
    {   
        // chercher les messager du canal en question
        $messages = $messageRepository->findBy(['channel' => $channel->getId()], []);

        return $this->render('channel/show.html.twig', [
            'channel' => $channel,
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_channel_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Channel $channel, ChannelRepository $channelRepository): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $channelRepository->add($channel, true);

            return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('channel/edit.html.twig', [
            'channel' => $channel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_channel_delete", methods={"POST"})
     */
    public function delete(Request $request, Channel $channel, ChannelRepository $channelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$channel->getId(), $request->request->get('_token'))) {
            $channelRepository->remove($channel, true);
        }

        return $this->redirectToRoute('app_channel_index', [], Response::HTTP_SEE_OTHER);
    }
}
