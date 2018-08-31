<?php
declare(strict_types=1);

namespace MVQN\REST\Annotations;

use MVQN\Annotations\Annotation;
use MVQN\Annotations\AnnotationReader;
use MVQN\Annotations\AnnotationReaderException;

final class ExcludeIdAnnotation extends Annotation
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
        if($this->type !== AnnotationReader::ANNOTATION_TYPE_CLASS)
            throw new AnnotationReaderException("@ExcludeId is only supported for classes!");

        $name = "excludeId";

        $existing[$name] = true;
        return $existing;

    }
}