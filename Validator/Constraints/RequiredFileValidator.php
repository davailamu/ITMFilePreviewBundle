<?php
/**
 * Created by PhpStorm.
 * User: fixer.developer
 * Date: 8/16/18
 * Time: 10:28 AM
 */

namespace ITM\FilePreviewBundle\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RequiredFileValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof UploadedFile) {
            return;
        }

        $object = $this->context->getObject();

        /** create action */
        if (!$this->em->contains($object)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        /** edit action */
        $originalEntityData = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        $fieldName = $this->context->getPropertyName();

        if (!$originalEntityData[$fieldName]) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
