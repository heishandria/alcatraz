<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Trait TraitBaseEntity
 *
 * @ORM\HasLifecycleCallbacks()
 */
trait TraitBaseEntity
{
    /**
     * @var Int
     *
     * @ORM\Column(name="_order", type="integer")
     * @Assert\NotBlank()
     * @JMS\Groups({"default"})
     */
    private $order;

    /**
     * @var Boolean
     * @ORM\Column(name="is_active", type="boolean")
     * @JMS\Groups({"default"})
     * @Assert\NotNull()
     */
    private $active;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getActive()
    {
        return (bool)$this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = (bool)$active;

        return $this;
    }
}