<?php

namespace App\Entity;

use App\Repository\ChainDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=ChainDataRepository::class)
 *
 * @ORM\Table(
 *    uniqueConstraints={
 *        @UniqueConstraint(
 *          name="left_right_uniq_idx",
 *          columns={"right_id", "left_id"}
 *     ),
 *     @ORM\UniqueConstraint(
 *          name="carriage_uniq_index",
 *          columns={"unique_identifiers_id", "carriage"},
 *          options={"where": "(carriage != 'f')"}
 *     )
 *    }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"right", "left"})
 */
class ChainData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chainDataName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default": "0"})
     */
    private $carriage = false;

    /**
     * @var UniqueIdentifiers
     *
     * @ORM\ManyToOne(targetEntity="UniqueIdentifiers", inversedBy="chainData")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $uniqueIdentifiers;

    /**
     * @var ChainData
     *
     * @ORM\OneToOne(targetEntity="ChainData",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $left;

    /**
     * @var ChainData
     *
     * @ORM\OneToOne(targetEntity="ChainData",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $right;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChainDataName(): ?string
    {
        return $this->chainDataName;
    }

    public function setChainDataName(string $chainDataName): self
    {
        $this->chainDataName = $chainDataName;

        return $this;
    }

    public function getCarriage(): ?bool
    {
        return $this->carriage;
    }

    public function setCarriage(?bool $carriage): self
    {
        $this->carriage = $carriage;

        return $this;
    }

    public function getUniqueIdentifiers(): ?UniqueIdentifiers
    {
        return $this->uniqueIdentifiers;
    }

    public function setUniqueIdentifiers(?UniqueIdentifiers $uniqueIdentifiers): self
    {
        $this->uniqueIdentifiers = $uniqueIdentifiers;

        return $this;
    }

    public function getLeft(): ?self
    {
        return $this->left;
    }

    public function setLeft(?self $left): self
    {
        $this->left = $left;
        $left->setRight($this);

        return $this;
    }

    public function getRight(): ?self
    {
        return $this->right;
    }

    public function setRight(?self $right): self
    {
        $this->right = $right;
        $right->setLeft($this);

        return $this;
    }
}
