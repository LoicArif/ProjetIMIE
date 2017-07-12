<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Job;
use AdminBundle\Entity\JobPersonnality;
use AdminBundle\Entity\Response;
use AdminBundle\Entity\Temperament;
use AdminBundle\Form\JobTemperamentType;
use AdminBundle\Form\JobType;
use AdminBundle\Form\TemperamentType;
use AdminBundle\Form\QuestionType;
use AdminBundle\Form\ResponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends Controller
{
    /**
     * Affiche la page de gestion des questions
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function questionsAction(Request $request)
    {
        // Récupération des répository
        $QuestionRepo = $this->getDoctrine()->getRepository("AdminBundle:Question");
        $TemperamentRepo = $this->getDoctrine()->getRepository("AdminBundle:Temperament");

        $formQuestion = $this->createForm(QuestionType::class);

        $formQuestion->handleRequest($request);

        if($formQuestion->isSubmitted() && $formQuestion->isValid())
        {
            $QuestionRepo->postQuestion($formQuestion);
        }

        $questions = $QuestionRepo->getQuestions();

        $createResponseForms = [];
        foreach ($questions as $question)
        {
            $createResponseForms[] = ["question" => $question, "formResponse" => $this->createForm(ResponseType::class)->createView()];
        }


        $temperaments = $TemperamentRepo->getTemperaments();

        return $this->render("AdminBundle:app:questions.html.twig", [
            "formQuestion" => $formQuestion->createView(),
            "questions" => $questions,
            "temperaments" => $temperaments,
            "createResponseForms" => $createResponseForms,
        ]);
    }

    /**
     * Permet de modifier une question
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function questionPutAction(Request $request)
    {
        $QuestionRepo = $this->getDoctrine()->getRepository("AdminBundle:Question");

        $questionId = $request->attributes->get('idQuestion');
        $formData = $request->get('data');

        if(!is_null($questionId) && !is_null($formData))
        {
            $result = $QuestionRepo->putQuestion($questionId, $formData);
            return $this->json(["message" => "Le changement de la question est bien effectué", "name" => $result->getLabel()]);
        }

        return $this->json(["message" => "Erreur lors de la modification"]);
    }

    /**
     * Permet de supprimer une question
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function questionDeleteAction(Request $request)
    {
        $QuestionRepo = $this->getDoctrine()->getRepository("AdminBundle:Question");

        $questionId = $request->attributes->get('idQuestion');

        if(!is_null($questionId))
        {
            $QuestionRepo->deleteQuestion($questionId);
            return $this->json(["message" => "La suppression de la question est bien effectué"]);
        }

        return $this->json(["message" => "Erreur pendant la suppression"]);
    }

    /**
     * Génère la vue partielle response
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function responsesAction(Request $request)
    {
        $ResponseRepo = $this->getDoctrine()->getRepository("AdminBundle:Response");
        $questionId = $request->attributes->get('idQuestion');
        $TemperamentRepo = $this->getDoctrine()->getRepository("AdminBundle:Temperament");

        $temperaments = $TemperamentRepo->getTemperaments();
        $responses = $ResponseRepo->getResponseByQuestionId($questionId);

        $formUpdateResponses = [];

        foreach($responses as $response)
        {
            $formUpdateResponses[] = [
                "questionId" => $questionId,
                "response" => $response,
                "formUpdateResponse" => $this->createForm(ResponseType::class, $response)->createView()
            ];
        }

        return $this->json($this->renderView("AdminBundle:app:response.html.twig", [
            "responses" => $responses,
            "temperaments" => $temperaments,
            "formUpdateResponses" => $formUpdateResponses,
        ]));
    }

    /**
     * Permet de créer une question
     *
     * @param Request $request
     *
     */
    public function responsePostAction(Request $request)
    {
        $ResponseRepo = $this->getDoctrine()->getRepository("AdminBundle:Response");
        $idQuestion = $request->attributes->get('idQuestion');

        $form = $this->createForm(ResponseType::class);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted())
        {
            $ResponseRepo->postResponse($form);

            return $this->json([
                "message" => "La création de la réponse c'est bien effectué",
                "idQuestion" => $idQuestion,
            ]);
        }

        return $this->render("AdminBundle:app:questions.html.twig");
    }

    /**
     * Permet la mise à jours d'une réponse en ajax
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function responseUpdateAction(Request $request)
    {
        $ResponseRepo = $this->getDoctrine()->getRepository("AdminBundle:Response");

        $responseId = $request->attributes->get('idResponse');

        $form = $this->createForm(ResponseType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $response = $ResponseRepo->putResponse($responseId, $form);
            if(!$response)
                return $this->json([
                    'message' => "Problème lors de l'enregistrement, vérifier que les informations entrées soit valide.\n Il ne peut pas y avoir deux fois le même type d'équilibre pour un tempérament."
                ]);

            return $this->json(["message" => "La modification de la réponse c'est bien effectué"]);
        }

        return $this->json(["message" => "Erreur lors de la modification de la réponse"]);
    }

    /**
     * Permet de supprimer une réponse
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function responseDeleteAction(Request $request)
    {
        $ResponseRepo = $this->getDoctrine()->getRepository("AdminBundle:Response");
        $idResponse = $request->attributes->get('idResponse');
        $response = $ResponseRepo->getResponseById($idResponse);

        if(!is_null($idResponse))
        {
            $ResponseRepo->deleteResponse($idResponse);
        }

        return $this->json(["message" => "La suppression de la question c'est bien effectué", "idQuestion" => $response->getQuestion()->getId()]);
    }
}
