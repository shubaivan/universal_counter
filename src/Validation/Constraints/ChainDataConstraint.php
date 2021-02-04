<?php

namespace App\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ChainDataConstraint extends Constraint
{
    public $message = 'This value {{ args }} should be present';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
