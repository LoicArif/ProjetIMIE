<?php

namespace AdminBundle\Repository;

use AdminBundle\Entity\Temperament;
use AdminBundle\Entity\Question;
use AdminBundle\Entity\Response;


/**
 * RESPONSERepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResponseRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Renvois toutes les réponses enregistrer en base
     *
     * @return array
     */
    public function getResponses()
    {
        return $this->findAll();
    }

    /**
     * Renvois la réponse correspondant à un id
     *
     * @param $id
     *
     * @return null|object
     */
    public function getResponseById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Renvois les réponses pour une question
     *
     * @param $questionId
     * @return array|bool
     */
    public function getResponseByQuestionId($questionId)
    {
        $question = $this->getEntityManager()->getRepository("AdminBundle:Question")->getQuestionById($questionId);

        return $this->findBy(['question' => $question]);
    }

    /**
     * Créer une réponse
     *
     * @param $label
     * @param $value
     * @param $image
     * @param Question $question
     * @param Temperament $temperament
     *
     * @return Response|bool
     */
    public function postResponse($label, $value, $image, Question $question, Temperament $temperament)
    {

        if (!$this->checkIfResponseAlreadyExist($value, $question, $temperament) && $value != 50)
        {
            $em = $this->getEntityManager();

            $response = new Response();

            $response->setLabel($label)
                ->setValue($value)
                ->setImageName($image)
                ->setQuestion($question)
                ->setUpdatedAt(new \DateTime())
                ->setTemperament($temperament);

            $em->persist($response);
            $em->flush();

            return $response;
        }

        return false;
    }

    /**
     * Modifie une réponse
     *
     * @param $id
     * @param $label
     * @param $value
     * @param $image
     * @param Temperament $temperament
     *
     * @return bool|object
     */
    public function putResponse($id, $label, $value, $image, Temperament $temperament)
    {
        $em = $this->getEntityManager();
        if ($value != 50) {
            $response = $this->getResponseById($id);

            if (!$this->checkIfResponseAlreadyExist($value, $response->getQuestion(), $temperament, $id)) {
                $response->setLabel($label)
                    ->setValue($value)
                    ->setImage($image)
                    ->setUpdatedAt(new \DateTime())
                    ->setTemperament($temperament);

                $em->persist($response);
                $em->flush();

                return $response;
            }
        }
        return false;
    }

    /**
     * Supprime une réponse
     * @param $id
     */
    public function deleteResponse($id)
    {
        $em = $this->getEntityManager();

        $response = $this->getResponseById($id);

        $em->remove($response);
        $em->flush();
    }

    /**
     * Renvois true si la réponse existe déjà
     *
     * @param $label
     *
     * @return bool
     */
    public function checkIfResponseAlreadyExist($value, Question $question, Temperament $temperament, $id = null)
    {
        if ($value > 50) {
            $query = "r.value > 50";
        } else if ($value < 50) {
            $query = "r.value < 50";
        }

        return $this->checkerProcess($query, $question, $temperament, $id);
    }

    /**
     *
     * Méthode factoriser pour voir si en base il n'éxiste pas une réponse avec un même type de personnalité pour une quéstion
     * @param $string
     * @param $question
     * @param $temperament
     * @param $id
     * @return bool
     */
    private function checkerProcess($string, $question, $temperament, $id)
    {
        $responses = $this->getEntityManager()->createQueryBuilder()
            ->select("r")
            ->from("AdminBundle:Response", "r")
            ->where("r.question = :question")
            ->andWhere("r.temperament = :temperament")
            ->andWhere($string)
            ->setParameters([
                ":question" => $question,
                ":temperament" => $temperament,
            ])
            ->getQuery()
            ->getResult();

        /**
         * True == Entité présente et non demandé en modification ==> modification refuser
         * False == Entité non présente ou présente et demande de modification => Modification autoriser
         */

        $return = false;

        // Si on a une réponse
        if (count($responses)) {
            // Si l'id présent en paramètre est null
            if (is_null($id)) {
                // On renvois true
                $return = true;
            } else {
                // En renvois true
                $return = true;

                // Si l'id présent en paramètre correspond a celui d'un des résultats trouver on renvois false
                foreach ($responses as $response) {
                    if ($response->getId() == $id) {
                        $return = false;
                    }
                }
            }
        }

        return $return;
    }
}
