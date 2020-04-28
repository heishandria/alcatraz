<?php

namespace App\Entity;

use App\Entity\Traits\TraitTimestampable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Provider\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Survey
 *
 * @ORM\Table(name="survey")
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Survey
{
    use TraitTimestampable;

    /**
     * @var Int $idSurvey
     *
     * @ORM\Column(name="id_survey", type="integer")
     * @ORM\Id
     * @JMS\Groups({"survey.details", "question.ext.details", "response.ext.details", "test.ext.details"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idSurvey;

    /**
     * @var String $titleSurvey
     *
     * @ORM\Column(name="title_survey", type="string", length=255)
     * @JMS\Groups({"survey.details", "test.ext.details"})
     * @Assert\NotBlank()
     */
    private $titleSurvey;

    /**
     * @var String $description
     *
     * @ORM\Column(name="description_survey", type="string", length=255, nullable=true)
     * @JMS\Groups({"survey.details", "test.ext.details"})
     * @Assert\NotBlank()
     */
    private $description;

    /**
     *  @var ArrayCollection $questions
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="survey", cascade={"persist", "remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     * @JMS\Groups({"survey.details", "survey.ext.details"})
     * @Assert\Valid()
     */
    private $questions;

    /**
     * @var Int $durationSurvey
     *
     * @ORM\Column(name="duration_survey", type="integer")
     * @Assert\NotBlank()
     * @JMS\Groups({"survey.details"})
     */
    private $durationSurvey;

    /**
     * @var Boolean
     * @ORM\Column(name="is_active", type="boolean")
     * @JMS\Groups({"survey.details"})
     * @Assert\NotNull()
     */
    private $active;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getIdSurvey(): ?int
    {
        return $this->idSurvey;
    }

    public function getTitleSurvey(): ?string
    {
        return $this->titleSurvey;
    }

    public function setTitleSurvey(string $titleSurvey): self
    {
        $this->titleSurvey = $titleSurvey;

        return $this;
    }

    public function getDurationSurvey(): ?int
    {
        return $this->durationSurvey;
    }

    public function setDurationSurvey(int $durationSurvey): self
    {
        $this->durationSurvey = $durationSurvey;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setSurvey($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getSurvey() === $this) {
                $question->setSurvey(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}