<?php

namespace AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Temperament
 */
class Temperament
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $temperament;

    /**
     * @var string
     */
    private $opposedTemperament;

    /**
     * @var ArrayCollection
     */
    private $jobTemperaments;

    /**
     * @var ArrayCollection
     */
    private $responses;

    /**
     * @var ArrayCollection
     */
    private $questions;

    public function __construct()
    {
        $this->jobTemperaments = new ArrayCollection();
        $this->responses = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param Question $questions
     **/
    public function setQuestions(Question $questions)
    {
        $this->questions->add($questions);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param Response $responses
     **/
    public function setResponses(Response $responses)
    {
        $this->responses->add($responses);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getJobTemperaments()
    {
        return $this->jobTemperaments;
    }

    /**
     * @param JobTemperament $jobTemperaments
     **/
    public function setJobTemperaments(JobTemperament $jobTemperaments)
    {
        $this->jobTemperaments->add($jobTemperaments);
        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Temperament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set temperament
     *
     * @param string $temperament
     *
     * @return Temperament
     */
    public function setTemperament($temperament)
    {
        $this->temperament = $temperament;

        return $this;
    }

    /**
     * Get temperament
     *
     * @return string
     */
    public function getTemperament()
    {
        return $this->temperament;
    }

    /**
     * Set opposedTemperament
     *
     * @param string $opposedTemperament
     *
     * @return Temperament
     */
    public function setOpposedTemperament($opposedTemperament)
    {
        $this->opposedTemperament = $opposedTemperament;

        return $this;
    }

    /**
     * Get opposedTemperament
     *
     * @return string
     */
    public function getOpposedTemperament()
    {
        return $this->opposedTemperament;
    }

    public function __toString()
    {
        return $this->getId()."";
    }
}

