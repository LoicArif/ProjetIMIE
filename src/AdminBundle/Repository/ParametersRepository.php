<?php

namespace AdminBundle\Repository;

use AdminBundle\Entity\Parameters;

/**
 * ParametersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParametersRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Renvoie les champs parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->findAll();
    }

    /**
     * Renvoie un seul paramter
     *
     * @param $id
     *
     * @return null|object
     */
    public function getParameterById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function postParameters($label, $value)
    {
        $em = $this->getEntityManager();

        $parameters = new Parameters();

        $parameters->setLabel($label);
        $parameters->setValue($value);

        $em->persist($parameters);
        $em->flush();

        return $parameters;
    }

    /**
     * Modifie les paramètres
     *
     * @param $id
     * @param $label
     * @param $value
     *
     * return null|object
     */
    public function putParameters($id,$label, $value)
    {
        $em = $this->getEntityManager();
        $parameters = $this->getParameterById($id);
        $parameters->setLabel($label);
        $parameters->setValue($value);

        $em->persist($parameters);
        $em->flush();

        return $parameters;
    }

    /**
     * Supprime les paramètres
     *
     * @param $id
     */
    public function deleteParameters($id)
    {
        $em = $this->getEntityManager();
        $parameters = $this->getParameterById($id);

        $em->remove($parameters);
        $em->flush();
    }

    /**
     * Renvoie true si le paramètre existe déjà
     *
     * @param $label
     * @param $value
     *
     * @return bool
     */
    public function checkIfParametersAlreadyExist($label, $value)
    {
        $isHereOrNot = $this->getEntityManager()->createQueryBuilder()
            ->select("params")
            ->from("AdminBundle:Parameters", "params")
            ->where("params.label = :label")
            ->andWhere("params.value = :value")
            ->setParameter(":label", $label)
            ->setParameter(":value", $value)
            ->getQuery()
            ->getResult();

        if(count($isHereOrNot))
            return true;

        return false;
    }
}