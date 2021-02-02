<?php

namespace App\Entity;

use App\Repository\UniqueIdentifiersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UniqueIdentifiersRepository::class)
 *
 * @ORM\Table(
 *    uniqueConstraints={
 *        @UniqueConstraint(
 *          name="request_hash_uniq_idx",
 *          columns={"request_hash"}
 *     ),
 *    },
 * )
 *
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"requestHash"})
 */
class UniqueIdentifiers
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid")
     */
    private $requestHash;

    /**
     * @var RequestApproach
     *
     * @ORM\OneToOne(targetEntity="RequestApproach",
     *      mappedBy="uniqueIdentifiers",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     */
    private $request;

    /**
     * @var ArrayCollection|ChainData[]
     *
     * @ORM\OneToMany(
     *     targetEntity="ChainData",
     *     mappedBy="uniqueIdentifiers",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private $chainData;

    public function __construct()
    {
        $this->chainData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestHash()
    {
        return $this->requestHash;
    }

    public function setRequestHash($requestHash): self
    {
        $this->requestHash = $requestHash;

        return $this;
    }

    public function getRequest(): ?RequestApproach
    {
        return $this->request;
    }

    public function setRequest(?RequestApproach $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Collection|ChainData[]
     */
    public function getChainData(): Collection
    {
        return $this->chainData;
    }

    public function addChainData(ChainData $chainData): self
    {
        if (!$this->chainData->contains($chainData)) {
            $this->chainData[] = $chainData;
            $chainData->setUniqueIdentifiers($this);
        }

        return $this;
    }

    public function removeChainData(ChainData $chainData): self
    {
        if ($this->chainData->removeElement($chainData)) {
            // set the owning side to null (unless already changed)
            if ($chainData->getUniqueIdentifiers() === $this) {
                $chainData->setUniqueIdentifiers(null);
            }
        }

        return $this;
    }
}
