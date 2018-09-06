<?php
declare(strict_types=1);

namespace MVQN\Annotations;

use MVQN\Collections\Collectible;

/**
 * Class Annotation
 *
 * @package MVQN\Annotations
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class Annotation extends Collectible
{
    protected const PATTERN_JSON    = "/(\{.*\})/";
    protected const PATTERN_ARRAY   = "/(\[.*\])/";
    protected const PATTERN_EVAL    = "/(\`.*\`)/";


    protected const COMBINE_MODE_OVERWRITE  = 1;
    protected const COMBINE_MODE_MERGE      = 2;



    /** @var string $value */
    protected $value = "";

    /** @var string $type  */
    protected $type = "";



    public function __construct(int $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }


    /**
     * @param array $existing
     * @param string|null $name
     * @return array|null
     */
    public abstract function parse(array $existing = [], string &$name = null): array;


    /**
     * @param array $existing
     * @param string $name
     * @param array $value
     * @param int $mode
     * @return array
     * @throws \Exception
     */
    protected function combineResults(array $existing, string $name, $value,
        int $mode = Annotation::COMBINE_MODE_OVERWRITE): array
    {
        if(is_array($value) && array_key_exists($name, $existing))
        {
            switch($mode)
            {
                case Annotation::COMBINE_MODE_OVERWRITE:
                    $existing[$name] = $value;
                    break;

                case Annotation::COMBINE_MODE_MERGE:
                    $existing[$name] = array_merge($existing[$name], is_array($value) ? $value : [$value]);
                    break;

                default:
                    throw new \Exception("[MVQN\Annotations\AnnotationReader] Unsupported MODE: '$mode'");
            }

        }
        else
        {
            $existing[$name] = $value;
        }

        return $existing;
    }


    protected function isValueJSON(string &$value = null): bool
    {
        $result = preg_match(Annotation::PATTERN_JSON, $this->value) == true;

        if($result)
            $value = json_decode($this->value, true);


        return $value !== null && $result;
    }

    protected function isValueArray(string &$value = null): bool
    {
        $result = preg_match(Annotation::PATTERN_ARRAY, $this->value) == true;

        if($result)
            $value = eval("return {$this->value};");

        return $value !== null && $result;
    }

    protected function isValueEval(string &$value = null): bool
    {
        $result = preg_match(Annotation::PATTERN_EVAL, $this->value) == true;

        if($result)
            $value = str_replace("`", "", $this->value);

        return $value !== null && $result;
    }

}