<?php

namespace App\Entity;

use App\Entity\Traits\TraitBaseEntity;
use App\Entity\Traits\TraitTimestampable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Question
{
    use TraitTimestampable;
    use TraitBaseEntity;

    /**
     * @var Int $idQuestion
     *
     * @ORM\Column(name="id_question", type="integer")
     * @ORM\Id
     * @JMS\Groups({"question.details", "survey.ext.details", "response.ext.details", "test.ext.details"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idQuestion;

    /**
     * @var String $titleQuestion
     *
     * @ORM\Column(name="title_question", type="string", length=255)
     * @JMS\Groups({"question.details", "survey.ext.details", "response.ext.details", "test.ext.details"})
     * @Assert\NotBlank()
     */
    private $titleQuestion;

    /**
     * @var QuestionFormat $format
     *
     * @ORM\Column(nullable=true)
     * @JMS\Groups({"question.details", "survey.ext.details", "response.ext.details", "test.ext.details"})
     * @ORM\ManyToOne(targetEntity="App\Entity\QuestionFormat")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_format", referencedColumnName="id")
     * })
     *
     */
    private $format;

    /**
     * @var Survey $survey
     * @JMS\Groups({"question.details", "response.ext.details"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey", inversedBy="questions")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_survey", referencedColumnName="id_survey", onDelete="CASCADE")
     * })
     */
    private $survey;

    /**
     *  @var ArrayCollection $responses
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Response", mappedBy="question", cascade={"persist", "remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     * @JMS\Groups({"question.details", "survey.ext.details"})
     * @Assert\Valid()
     */
    private $responses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getIdQuestion(): ?int
    {
        return $this->idQuestion;
    }

    public function getTitleQuestion(): ?string
    {
        return $this->titleQuestion;
    }

    public function setTitleQuestion(?string $titleQuestion): self
    {
        $this->titleQuestion = $titleQuestion;

        return $this;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * @return Collection|Response[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setQuestion($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->contains($response)) {
            $this->responses->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getQuestion() === $this) {
                $response->setQuestion(null);
            }
        }

        return $this;
    }

    public function getFormat(): ?QuestionFormat
    {
        return $this->format;
    }

    public function setFormat(?QuestionFormat $format): self
    {
        $this->format = $format;

        return $this;
    }
}