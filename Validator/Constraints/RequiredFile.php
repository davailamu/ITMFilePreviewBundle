<?php
/**
 * Created by PhpStorm.
 * User: fixer.developer
 * Date: 8/16/18
 * Time: 10:26 AM
 */

namespace ITM\FilePreviewBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RequiredFile extends Constraint
{
    public $message = 'The file is required';
}
