<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="messages", methods={"GET"})
     */
    public function getAllMessages()
    {
        $repo = $this->getDoctrine()->getRepository(Message::class);
        return new JsonResponse(
            array_map(
                function (Message $data) {
                    return $data->toArray();
                },
                $repo->findAll()
            ), 200
        );
    }
    
    /**
     * @Route("/messages/new", name="post_message", methods={"POST"})
     * @param Request $request
     *
     * @return mixed
     */
    public function postAddNewMessage(Request $request)
    {
        $message = new Message();
        $form    = $this->createForm(MessageType::class, $message);
        $this->processForm($request, $form);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            return new JsonResponse($message->toArray(), JsonResponse::HTTP_CREATED);
        }
        
        return $this->createValidationErrorResponse($form);
    }
    
    /**
     * Helper method to always return formatted error message.
     *
     * @param FormInterface $form
     *
     * @return JsonResponse
     */
    private function createValidationErrorResponse(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);
        $data   = [
            'type'   => 'validation_error',
            'title'  => 'There was a validation error',
            'errors' => $errors,
        ];
        return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * Extract errors from the form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
    
    /**
     * Submit form with data from request.
     *
     * @param Request       $request
     * @param FormInterface $form
     */
    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $form->submit($data, true);
    }
}
