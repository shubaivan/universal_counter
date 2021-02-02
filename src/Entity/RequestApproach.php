<?php

namespace App\Entity;

use App\Repository\RequestApproachRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=RequestApproachRepository::class)
 */
class RequestApproach
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ipAddress;

    /**
     * @var UniqueIdentifiers
     *
     * @ORM\OneToOne(targetEntity="UniqueIdentifiers",
     *     inversedBy="request",
     *     orphanRemoval=true
     *     )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $uniqueIdentifiers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUniqueIdentifiers(): ?UniqueIdentifiers
    {
        return $this->uniqueIdentifiers;
    }

    public function setUniqueIdentifiers(?UniqueIdentifiers $uniqueIdentifiers): self
    {
        // unset the owning side of the relation if necessary
        if ($uniqueIdentifiers === null && $this->uniqueIdentifiers !== null) {
            $this->uniqueIdentifiers->setRequest(null);
        }

        // set the owning side of the relation if necessary
        if ($uniqueIdentifiers !== null && $uniqueIdentifiers->getRequest() !== $this) {
            $uniqueIdentifiers->setRequest($this);
        }

        $this->uniqueIdentifiers = $uniqueIdentifiers;

        return $this;
    }
}
