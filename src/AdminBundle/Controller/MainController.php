<?php

namespace AdminBundle\Controller;

use AdminBundle\AdminBundle;
use AdminBundle\Entity\Visitor;
use AdminBundle\Form\ParameterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * Affiche la page d'acceuil de l'administration
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        $em = $this->getDoctrine();
        $visitorRepo = $em->getRepository("AdminBundle:Visitor");

        $questions = $em->getRepository("AdminBundle:Question")->getNumberOfQuestionByTemperament();
        $visitorByCountry = $visitorRepo->getNumberOfConnectionByCountry();
        $registeredYears = $visitorRepo->getRegisteredYears();
        $visitorWhoSharedAndThoseWhoDont = $visitorRepo->getVisitorsWhoSharedAndThoseWhoDont();

        return $this->render('AdminBundle:app:home.html.twig', [
            "questions" => $questions,
            "visitorsByCountry" => $visitorByCountry,
            "registeredYears" => $registeredYears,
            "visitorWhoSharedAndThoseWhoDont" => $visitorWhoSharedAndThoseWhoDont,
        ]);
    }

    /**
     * Permet de récupérer les statistiques de métiers renvoyer
     *
     * @return JsonResponse
     */
    public function getMostDeliveredJobByQuizzAction()
    {
        $jobs = $this->getDoctrine()->getRepository("AdminBundle:Job")->getMostDeliveredJobByQuizz();

        return $this->json($jobs);
    }

    /**
     * Permet de reset les statistiques de métiers renvoyer
     *
     * @return JsonResponse
     */
    public function resetMostDeliveredJobByQuizzAction()
    {
        $this->getDoctrine()->getRepository("AdminBundle:Job")->resetMostDeliveredQuizz();

        return $this->json([]);
    }

    /**
     * Permet de récuperer les informations d'utilisation du quizz
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function visitorQuizzAction(Request $request)
    {
        $visitorRepo = $this->getDoctrine()->getRepository("AdminBundle:Visitor");
        $year = $request->attributes->get('year');

        $returnResult = null;

        $unAchieveData = $visitorRepo->getVisitorsForQuizzInformation(0, $year);
        $achieveData = $visitorRepo->getVisitorsForQuizzInformation(1, $year);
        $visitorData = $visitorRepo->getVisitorsForQuizzInformation(null, $year);

        return $this->json([
            "message" => "Ok",
            "value" => [
                "achieve" => $achieveData,
                "unachieve" => $unAchieveData,
                "visitor" => $visitorData
            ]
        ]);
    }

    /**
     * Renvois les données visiteurs par années
     *
     * @return JsonResponse
     */
    public function visitorAction(Request $request)
    {
        $year = $request->attributes->get('year');

        $visitorByYear = $this->getDoctrine()->getRepository("AdminBundle:Visitor")->getVisitorBy("MONTH", $year);

        return $this->json($visitorByYear);
    }

    /**
     * Renvoie les données visiteurs par browser (navigateur)
     *
     * @return JsonResponse
     */
    public function browserAction()
    {
        $browser = $this->getDoctrine()->getRepository("AdminBundle:Visitor")->getBrowser();

        return $this->json($browser);
    }

    /**
     * Affiche la page de gestion des utilisateurs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction(Request $request)
    {
        $userRepo = $this->getDoctrine()->getRepository("UserBundle:User");
        $upgrade = $request->get("up");
        $downgrade = $request->get('down');
        $userId = $request->get('userId');

        if (isset($upgrade) && $upgrade === "Upgrade user")
        {
            $userRepo->upgradeUserToAdmin($userId);
        }

        if (isset($downgrade) && $downgrade === "Downgrade user")
        {
            $userRepo->downgradeAdminToUser($userId);
        }

        $users = $userRepo->getUsers();

        return $this->render('AdminBundle:app:user.html.twig', [
            "users" => $users
        ]);
    }

    /**
     * Affiche la page des parametres utilisateur
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parametersAction(Request $request)
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");

        $parameters = $ParamRepo->getParametersWithout([4, 6, 8, 9, 10]);

        return $this->render('AdminBundle:app:parameter.html.twig', [
            "parameters" => $parameters
        ]);
    }

    /**
     * Modifie la page des parametres utilisateur
     *
     * @param Request $request
     * @return jsonResponse
     */
    public function updateParametersAction(Request $request)
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");

        $parameterId = $request->attributes->get('idParameter');
        $label = $request->get('label');
        $value = $request->get('value');

        if (!is_null($parameterId) && !is_null($label) && !is_null($value))
        {
            $ParamRepo->putParameter($parameterId, $label, $value);
        }


        return $this->json(["message" => "Modification(s) bien effectuée(s)"]);
    }


    /**
     * Modifie la page des mentionsLégales utilisateur
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mentionLegaleAction(Request $request)
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");
        $em = $this->getDoctrine()->getManager();

        $parameter = $ParamRepo->getParameterById(4);
        $form = $this->createForm(ParameterType::class, $parameter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $parameter->setValue($form['value']->getData());

            $em->persist($parameter);
            $em->flush();
        }


        return $this->render('AdminBundle:app:mentionLegale.html.twig', [
            "parameter" => $parameter,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homePresentationAction(Request $request)
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");
        $em = $this->getDoctrine()->getManager();

        $parameter = $ParamRepo->getParameterById(6);
        $form = $this->createForm(ParameterType::class, $parameter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $parameter->setValue($form['value']->getData());

            $em->persist($parameter);
            $em->flush();
        }


        return $this->render('AdminBundle:app:homePresentation.html.twig', [
            "parameter" => $parameter,
            "form" => $form->createView()
        ]);
    }


    /**
     * Modifie le paramètre de la taille de l'image
     * @param Request $request
     * @return JsonResponse
     */
    public function resizeImageHandlerAction(Request $request)
    {
        $paramId = $request->attributes->get('idParameter');

        $value = $request->get('value');

        $bool = $this->getDoctrine()->getRepository("AdminBundle:Parameters")->putValueOfParameterById($paramId, $value);

        if ($bool)
        {
            return $this->json(['message' => "Modification(s) bien effectuée(s)"]);
        }

        return $this->json(['message' => "Erreur lors de la modification"]);
    }

    /**
     * Permet de modifier les couleurs depuis l'interface administrateur
     * On récupère le service correspondant
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function colorAction()
    {
        $colors = $this->container->get('admin.parametersColorHandler')->getColors();

        return $this->render('AdminBundle:app:color.html.twig', [
            "primary" => $colors['primary'],
            "secondary" => $colors['secondary'],
            "text" => $colors['text'],
        ]);
    }

    public function putColorAction(Request $request)
    {
        $id = $request->get('id');
        $label = $request->get('label');
        $value = $request->get('value');

        if (!is_null($label) && !is_null($value) && !is_null($id))
        {
            $paramRepo = $this->getDoctrine()->getRepository('AdminBundle:Parameters');

            $color = $paramRepo->putParameter($id, $label, $value);

            if (!$color)
            {
                return $this->json([
                    'message' => 'Erreur lors de la modification.'
                ]);
            }
            else
            {
                return $this->json([
                    'message' => 'Modification bien effectuée.',
                    'color' => $color
                ]);
            }
        }
        return $this->json([
            'message' => 'Erreur lors de la modification.'
        ]);


    }
}
