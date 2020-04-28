<?php

namespace App\Entity;

use App\Entity\Traits\TraitBaseEntity;
use App\Entity\Traits\TraitTimestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Response
 *
 * @ORM\Table(name="response")
 * @ORM\Entity(repositoryClass="App\Repository\ResponseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Response
{
    use TraitTimestampable;
    use TraitBaseEntity;

    /**
     * @var Int $idResponse
     *
     * @ORM\Column(name="id_response", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"response.details", "survey.ext.details", "question.ext.details", "test.ext.details"})
     */
    private $idResponse;

    /**
     * @var String $contentResponse
     *
     * @ORM\Column(name="content_response", type="string", length=255)
     * @JMS\Groups({"response.details", "survey.ext.details", "question.ext.details", "test.ext.details"})
     * @Assert\NotBlank()
     */
    private $contentResponse;

    /**
     * @var Question $question
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="responses")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="id_question", referencedColumnName="id_question", onDelete="CASCADE")
     * })
     * @JMS\Groups({"response.details", "response.ext.details"})
     */
    private $question;

    /**
     * @var Boolean $isGoodResponse
     *
     * @ORM\Column(name="is_good_response", type="boolean", options={"default"=FALSE})
     * @Assert\NotNull()
     * @JMS\Groups({"response.details", "survey.ext.details", "question.ext.details", "test.ext.details"})
     */
    private $isGoodResponse;

    /**
     * @var Int $scoring
     *
     * @ORM\Column(name="scoring", type="integer")
     * @Assert\NotBlank()
     * @JMS\Groups({"response.details", "survey.ext.details", "question .ext.details", "test.ext.details"})
     */
    private $scoring;

    public function getIdResponse(): ?int
    {
        return $this->idResponse;
    }

    public function getContentResponse(): ?string
    {
        return $this->contentResponse;
    }

    public function setContentResponse(string $contentResponse): self
    {
        $this->contentResponse = $contentResponse;

        return $this;
    }

    public function getIsGoodResponse(): ?bool
    {
        return $this->isGoodResponse;
    }

    public function setIsGoodResponse(bool $isGoodResponse): self
    {
        $this->isGoodResponse = $isGoodResponse;

        return $this;
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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}