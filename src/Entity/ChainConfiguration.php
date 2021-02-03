<?php

namespace App\Entity;

use App\Exception\EntityValidatorException;
use App\Repository\ChainConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;


/**
 * @ORM\Entity(repositoryClass=ChainConfigurationRepository::class)
 */
class ChainConfiguration implements EntityValidatorException
{
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';

    const SERIALIZED_GROUP_GET_ONE = 'get_chain_configuration_by_id';
    const SERIALIZED_GROUP_POST = 'post_chain_configuration';

    const VALID_GROUP_UNIQ_IDENTITY = 'valid_uuid';

    private static $enumDirection = [
        self::DIRECTION_DOWN => 0,
        self::DIRECTION_UP => 1,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Annotation\Groups({
     *     ChainConfiguration::SERIALIZED_GROUP_POST, ChainConfiguration::SERIALIZED_GROUP_GET_ONE
     * })
     * @Assert\NotBlank(groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     */
    private $chainMainName;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     * @Annotation\Groups({
     *     ChainConfiguration::SERIALIZED_GROUP_POST, ChainConfiguration::SERIALIZED_GROUP_GET_ONE
     * })
     * @Assert\NotBlank(groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     * @Assert\NotEqualTo(value="0", groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     * @SWG\Property(description="chain start value.", default="1")
     */
    private $startValue;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     * @Annotation\Groups({
     *     ChainConfiguration::SERIALIZED_GROUP_POST, ChainConfiguration::SERIALIZED_GROUP_GET_ONE
     * })
     * @SWG\Property(description="chain step.", default="1")
     * @Assert\NotBlank(groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     * @Assert\NotEqualTo(value="0", groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     */
    private $increment = 1;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false, options={"default":1})
     * @Annotation\Groups({
     *     ChainConfiguration::SERIALIZED_GROUP_POST
     * })
     * @Annotation\Accessor(setter="setDirectionAccessor")
     * @SWG\Property(description="chain direction up or down.", default="up(1)")
     * @Assert\NotBlank(groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     * @Assert\NotEqualTo(value="0", groups={ChainConfiguration::SERIALIZED_GROUP_POST})
     */
    private $direction = 1;

    /**
     * @var UniqueIdentifiers
     *
     * @ORM\OneToOne(
     *     targetEntity="UniqueIdentifiers",
     *     mappedBy="chainConfiguration"
     *     )
     * @Assert\NotBlank(groups={ChainConfiguration::VALID_GROUP_UNIQ_IDENTITY})
     */
    private $uniqueIdentifier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChainMainName(): ?string
    {
        return $this->chainMainName;
    }

    public function setChainMainName(string $chainMainName): self
    {
        $this->chainMainName = $chainMainName;

        return $this;
    }

    public function getStartValue(): ?string
    {
        return $this->startValue;
    }

    public function setStartValue(string $startValue): self
    {
        $this->startValue = $startValue;

        return $this;
    }

    public function getIncrement(): ?int
    {
        return $this->increment;
    }

    public function setIncrement(int $increment): self
    {
        $this->increment = $increment;

        return $this;
    }

    public function getDirection(): ?string
    {
        if (!array_search($this->direction, self::$enumDirection, true)) {
            throw new BadRequestHttpException('available enum' . implode(',', self::$enumDirection));
        }
        return array_search($this->direction, self::$enumDirection, true);
    }

    public function setDirection(int $direction): self
    {
        if (array_search($direction, self::$enumDirection, true) === false) {
            throw new BadRequestHttpException('available enum' . implode(',', self::$enumDirection));
        }

        $this->direction = $direction;

        return $this;
    }

    public function getUniqueIdentifier(): ?UniqueIdentifiers
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(?UniqueIdentifiers $uniqueIdentifier): self
    {
        // unset the owning side of the relation if necessary
        if ($uniqueIdentifier === null && $this->uniqueIdentifier !== null) {
            $this->uniqueIdentifier->setChainConfiguration(null);
        }

        // set the owning side of the relation if necessary
        if ($uniqueIdentifier !== null && $uniqueIdentifier->getChainConfiguration() !== $this) {
            $uniqueIdentifier->setChainConfiguration($this);
        }

        $this->uniqueIdentifier = $uniqueIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->getId();
    }

    /**
     * @return string|null
     *
     * @Annotation\VirtualProperty()
     * @Annotation\SerializedName("directionType")
     * @Annotation\Groups({ChainConfiguration::SERIALIZED_GROUP_GET_ONE})
     * @Annotation\Type("string")
     */
    public function getDirectionValue()
    {
        return $this->getDirection();
    }

    public function setDirectionAccessor($directioin)
    {
        return $this->setDirection($directioin);
    }
}
