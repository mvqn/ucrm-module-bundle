<?php
declare(strict_types=1);

namespace MVQN\REST\Annotations;

use MVQN\Annotations\Annotation;
use MVQN\Annotations\AnnotationReader;
use MVQN\Annotations\AnnotationReaderException;

final class PatchRequiredAnnotation extends Annotation
{

    /**
     * @param array $existing
     * @param string|null $name
     * @return array|null
     *
     * @throws AnnotationReaderException
     */
    public function parse(array $existing = [], string &$name = null): array
    {
        if($this->type !== AnnotationReader::ANNOTATION_TYPE_PROPERTY)
            throw new AnnotationReaderException("@PatchRequired is only supported for properties!");

        $name = "patch-required";

        if($this->isValueEval($this->value))
        {
            $existing[$name] = "`{$this->value}`";
            return $existing;
        }
        elseif($this->value === null || $this->value === "")
        {
            $existing[$name] = true;
            return $existing;
        }
        else
        {
            // Exclude the item, as we do not know how to handle the 'value' specifically!
            return $existing;
        }
    }
}