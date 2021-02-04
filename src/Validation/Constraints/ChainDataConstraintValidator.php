<?php

namespace App\Validation\Constraints;

use App\Entity\ChainData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ChainDataConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        /** @var $entity ChainData */
        if (!$entity instanceof ChainData) {
            return;
        }

        if (!$constraint instanceof ChainDataConstraint) {
            return;
        }

        if (!$entity->getUniqueIdentifiers()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ args }}', 'uniqueIdentifiers')
                ->atPath('uniqueIdentifiers')
                ->addViolation();
        }

        if ($entity->getUniqueIdentifiers()
            && $entity->getUniqueIdentifiers()->getChainData()->count() > 1
            && !$entity->getLeft()
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ args }}', 'left')
                ->atPath('left')
                ->addViolation();
        }
    }
}
