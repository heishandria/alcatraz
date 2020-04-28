<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Trait TraitTimestampable
 *
 * @ORM\HasLifecycleCallbacks()
 */
trait TraitTimestampable
{
    /**
     * TraitTimestampable $ignoredFields
     *
     * @var array
     *
     * @JMS\Exclude()
     */
    protected $ignoredFields = [];

    /**
     * TraitTimestampable $created
     *
     * @ORM\Column(name="_created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @JMS\Exclude()
     *
     * @var DateTime
     */
    private $created;

    /**
     * TraitTimestampable $updated
     *
     * @ORM\Column(name="_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @JMS\Exclude()
     *
     * @var DateTime
     */
    private $updated;

    /**
     * @return array
     */
    public function getIgnoredFields(): array
    {
        return $this->ignoredFields;
    }

    /**
     * @param array $ignoredFields
     */
    public function setIgnoredFields(array $ignoredFields): void
    {
        $this->ignoredFields = $ignoredFields;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated(?DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(?DateTime $created): void
    {
        $this->created = $created;
    }
}
