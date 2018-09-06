<?php
declare(strict_types=1);

namespace MVQN\REST\Annotations;

use MVQN\Annotations\Annotation;
use MVQN\Annotations\AnnotationReader;

final class EndpointAnnotation extends Annotation
{

    private $mode = Annotation::COMBINE_MODE_MERGE;



    /**
     * @param array $existing
     * @param string|null $name
     * @return array|null
     * @throws \Exception
     */
    public function parse(array $existing = [], string &$name = null): array
    {
        if($this->type !== AnnotationReader::ANNOTATION_TYPE_CLASS)
            throw new \Exception("[MVQN\Annotations\AnnotationReader] @Endpoints is only supported for classes!");

        $name = "endpoints";

        if($this->isValueJSON($this->value) || $this->isValueArray($this->value))
        {
            return $this->combineResults($existing, $name, $this->value, $this->mode);
        }
        else
        {
            // ?
            return [];
        }
    }
}