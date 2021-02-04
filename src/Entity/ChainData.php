<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use App\Repository\ChainDataRepository;
use App\Validation\Constraints\ChainDataConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass=ChainDataRepository::class)
 *
 * @ORM\Table(
 *    uniqueConstraints={
 *        @UniqueConstraint(
 *          name="left_right_uniq_idx",
 *          columns={"right_id", "left_id", "unique_identifiers_id"}
 *     ),
 *     @UniqueConstraint(
 *          name="brand_slug_idx",
 *          columns={"unique_identifiers_id", "chain_data_name"}),
 *     @ORM\UniqueConstraint(
 *          name="carriage_uniq_index",
 *          columns={"unique_identifiers_id", "carriage"},
 *          options={"where": "(carriage != 'f')"}
 *     )
 *    }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"right", "left", "uniqueIdentifiers"}, groups={ChainData::SERIALIZED_GROUP_POST})
 * @UniqueEntity(fields={"chainDataName", "uniqueIdentifiers"}, groups={ChainData::SERIALIZED_GROUP_POST})
 * @ChainDataConstraint(groups={ChainData::VALIDATION_GROUP_RELATION})
 */
class ChainData implements EntityValidatorException
{
    const SERIALIZED_GROUP_GET_ONE = 'get_chain_data_by_id';
    const SERIALIZED_GROUP_POST = 'post_chain_data';
    const VALIDATION_GROUP_RELATION = 'validate_chain_data_relation';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Annotation\Groups({
     *     ChainData::SERIALIZED_GROUP_GET_ONE
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Annotation\Groups({
     *     ChainData::SERIALIZED_GROUP_POST, ChainData::SERIALIZED_GROUP_GET_ONE
     * })
     * @Assert\NotBlank(groups={ChainData::SERIALIZED_GROUP_POST})
     * @SWG\Property(description="chain data name for current elemnt.")
     */
    private $chainDataName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": "0"})
     * @Annotation\Groups({
     *     ChainData::SERIALIZED_GROUP_POST, ChainData::SERIALIZED_GROUP_GET_ONE
     * })
     * @Assert\NotNull(groups={ChainData::SERIALIZED_GROUP_POST})
     * @SWG\Property(description="carriage position for current element.")
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
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     * @SWG\Property(description="element by left side for current element.")
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
     * @SWG\Property(description="element by right side for current element.")
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
        if ($right->getLeft() !== $this) {
            $right->setLeft($this);
        }

        return $this;
    }

    /**
     * @return $this|null
     */
    public function move()
    {
        $this->setCarriage(false);
        $chainConfiguration = $this->getUniqueIdentifiers()->getChainConfiguration();
        if ($chainConfiguration) {
            $model = null;
            switch ($chainConfiguration->getDirection()) {
                case ChainConfiguration::DIRECTION_UP:
                    if (!$this->getRight()) {
                        throw new BadRequestException('in this direction ' . ChainConfiguration::DIRECTION_UP
                            . ' item was ended');
                    }
                    $this->getRight()->setCarriage(true);
                    $this->setCarriage(false);
                    $model = $this->getRight();
                    break;
                case ChainConfiguration::DIRECTION_DOWN:
                    if (!$this->getLeft()) {
                        throw new BadRequestException('in this direction ' . ChainConfiguration::DIRECTION_DOWN
                            . ' item was ended');
                    }
                    $this->getLeft()->setCarriage(true);
                    $this->setCarriage(false);
                    $model = $this->getLeft();
                    break;
                default:
                    break;
            }
            return $model;
        }
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->id;
    }
}
