<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\TraitTimestampable;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository")
 */
class Test
{
    use TraitTimestampable;

    /**
     * @var Int $idTest
     *
     * @ORM\Column(name="id_test", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"test.details"})
     */
    private $idTest;

    /**
     * @var Survey $survey
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_survey", referencedColumnName="id_survey")
     * })
     * @JMS\Groups({"test.details", "test.ext.details"})
     *
     */
    private $survey;

    /**
     *  @var ArrayCollection $decisions
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Decision", mappedBy="test", cascade={"persist", "remove"})
     * @Assert\Valid()
     * @JMS\Groups({"test.ext.details"})
     */
    private $decisions;

    /**
     * @var Int $scoring
     *
     * @ORM\Column(name="scoring", type="integer")
     * @Assert\NotBlank()
     * @JMS\Groups({"test.details"})
     */
    private $scoring;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->decisions = new ArrayCollection();
    }

    public function getScoring(): ?int
    {
        return $this->scoring;
    }

    public function setScoring(int $scoring): self
    {
        $this->scoring = $scoring;

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
     * @return Collection|Decision[]
     */
    public function getDecisions(): Collection
    {
        return $this->decisions;
    }

    public function addDecision(Decision $decision): self
    {
        if (!$this->decisions->contains($decision)) {
            $this->decisions[] = $decision;
            $decision->setTest($this);
        }

        return $this;
    }

    public function removeDecision(Decision $decision): self
    {
        if ($this->decisions->contains($decision)) {
            $this->decisions->removeElement($decision);
            // set the owning side to null (unless already changed)
            if ($decision->getTest() === $this) {
                $decision->setTest(null);
            }
        }

        return $this;
    }

    public function getIdTest(): ?int
    {
        return $this->idTest;
    }
}
