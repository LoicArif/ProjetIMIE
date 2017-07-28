<?php

namespace HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        return $this->render('HomeBundle:app:home.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metiersAction()
    {
        // Récupération des répository et manager
        $JobRepository = $this->getDoctrine()->getRepository("AdminBundle:Job");

        $jobs = $JobRepository->getJobs();

        return $this->render('HomeBundle:app:metiers.html.twig', [
            "jobs" => $jobs,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function quizzAction()
    {
        return $this->render('HomeBundle:app:quizz.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getQuestionSetAction()
    {
        $questionSet = $this->get("GenerateQuestionSet")->getQuestionSet();

        return $this->json($questionSet);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function resolutionAction(Request $request)
    {
        $QuizzResolver = $this->get('QuizzResolver');

        $QuizzResolver->setResultats($request->get('responses'));

        $selectedJobId = $QuizzResolver->resolve();

        return $this->json(['href' => $this->generateUrl("home_metier", ['jobId' => $selectedJobId, 'bool' => 1])]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metierAction(Request $request)
    {
        $jobId = $request->attributes->get('jobId');
        $bool = $request->attributes->get('bool');

        $job = $this->getDoctrine()->getRepository("AdminBundle:Job")->getJobById($jobId);
        $jobPersonnalities = $this->getDoctrine()->getRepository("AdminBundle:JobPersonnality")->getJobPersonnalityByJobId($jobId);

        return $this->render('HomeBundle:app:metier.html.twig', [
            "job" => $job,
            "jobPersonnalities" => $jobPersonnalities,
            'bool' => $bool
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderFooterAction()
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");

        $parameters = $ParamRepo->getParameters();

        return $this->render('HomeBundle:layout:footer.html.twig', [
            'parameters' => $parameters,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mentionsLegalesAction()
    {
        $ParamRepo = $this->getDoctrine()->getRepository("AdminBundle:Parameters");

        $mentionsLegales = $ParamRepo->getParameterById(4);

        return $this->render('HomeBundle:app:mentionsLegales.html.twig', [
            'mentionsLegales' => $mentionsLegales,
        ]);
    }
}
