<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChoiceRepository")
 */
class Decision
{
    /**
     * @var Int $idDecision
     *
     * @ORM\Column(name="id_decision", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Exclude()
     */
    private $idDecision;

    /**
     * @var Test $test
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Test")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_test", referencedColumnName="id_test")
     * })
     * @JMS\Exclude()
     */
    private $test;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dynamicResponse;

    /**
     * @var Question $question
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Question")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_question", referencedColumnName="id_question")
     * })
     * @JMS\Groups({"test.ext.details"})
     */
    private $question;

    /**
     * @var Response $response
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Response")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_response", referencedColumnName="id_response", nullable=true)
     * })
     * @JMS\Groups({"test.ext.details"})
     */
    private $response;

    public function getDynamicResponse(): ?string
    {
        return $this->dynamicResponse;
    }

    public function setDynamicResponse(?string $dynamicResponse): self
    {
        $this->dynamicResponse = $dynamicResponse;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getIdDecision(): ?int
    {
        return $this->idDecision;
    }
}
