<?php
declare(strict_types=1);

namespace MVQN\Annotations;

use MVQN\Collections\Collection;

/**
 * Class AnnotationCollection
 *
 * @package MVQN\Annotations
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class AnnotationCollection extends Collection
{


    /**
     * AnnotationCollection constructor.
     *
     * @param array|null $elements
     * @throws \Exception
     */
    public function __construct(?array $elements = [])
    {
        parent::__construct(Annotation::class, $elements);
    }



}