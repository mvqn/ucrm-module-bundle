<?php
declare(strict_types=1);

namespace MVQN\Annotations;

use MVQN\Collections\Collection;
use MVQN\Collections\CollectionException;

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
     * @throws CollectionException
     */
    public function __construct(?array $elements = [])
    {
        parent::__construct(Annotation::class, $elements);
    }



}