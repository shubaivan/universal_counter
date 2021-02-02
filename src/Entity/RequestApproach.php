<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use App\Repository\RequestApproachRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RequestApproachRepository::class)
 */
class RequestApproach implements EntityValidatorException
{
    const SERIALIZED_GROUP_GET_ONE = 'get_request_approach_by_id';

    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Annotation\Groups({
     *     RequestApproach::SERIALIZED_GROUP_GET_ONE
     * })
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({
     *     RequestApproach::SERIALIZED_GROUP_GET_ONE
     * })
     */
    private $ipAddress;

    /**
     * @var UniqueIdentifiers
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @ORM\OneToOne(targetEntity="UniqueIdentifiers",
     *     inversedBy="request",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Annotation\Groups({
     *     RequestApproach::SERIALIZED_GROUP_GET_ONE
     * })
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

    public function getIdentity()
    {
        return $this->id;
    }
}
