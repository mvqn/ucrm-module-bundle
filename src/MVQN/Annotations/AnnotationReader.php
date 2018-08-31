<?php
declare(strict_types=1);

namespace MVQN\Annotations;

use MVQN\Common\{Arrays, ArraysException, Casing, Strings};
use phpDocumentor\Reflection\Types\Resource_;

/**
 * Class AnnotationReader
 *
 * @package MVQN\Annotations
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class AnnotationReader
{
    // =================================================================================================================
    // CONSTANTS
    // -----------------------------------------------------------------------------------------------------------------

    public const ANNOTATION_TYPE_NONE               = 0;
    public const ANNOTATION_TYPE_CLASS              = 1;
    public const ANNOTATION_TYPE_METHOD             = 2;
    public const ANNOTATION_TYPE_PROPERTY           = 4;
    public const ANNOTATION_TYPE_ANY                = 7;

    protected const ANNOTATION_PATTERN              = "/(?:\*)(?:[\t ]*)?@([\w\_\-\\\\]+)(?:[\t ]*)?(.*)$/m";
    protected const ANNOTATION_PATTERN_JSON         = "/ (\{.*\})/";
    protected const ANNOTATION_PATTERN_ARRAY        = "/ (\[.*\])/";
    protected const ANNOTATION_PATTERN_EVAL         = "/ (\`.*\`)/";

    protected const ANNOTATION_PATTERN_ARRAY_NAMED  = "/([\w\_\-]*)(?:\[\])/";

    protected const ANNOTATION_PATTERN_VAR_TYPE     = '/^([\w\|\[\]\_]+)\s*(?:\$(\w+))?(.*)?/';

    protected const ANNOTATION_PATTERN_PROPERTY     = '/^property-*(read|write|)\s+(\w+)\s+(\$\w+)\s+(.*)$/';


    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    protected $class = "";

    protected $uses = [];






    public function __construct(string $class)
    {
        $this->class = $class;
        $this->uses = $this->getUseStatements($this->getReflectedClass()->getFileName());
    }


    protected function parse(int $type, string $docblock): array
    {

        // Build a collection of valid lines ONLY!  IF none exist, simply return empty-handed...
        if(!preg_match_all(self::ANNOTATION_PATTERN, $docblock, $matches))
            return [];

        // Create a collection to store valid mappings.
        $params = [];



        // Loop through each matched line!
        for($i = 0; $i < count($matches[0]); $i++)
        {
            $key = $matches[1][$i];
            $value = trim($matches[2][$i]); // Remove trailing '\r' that we cannot seem to RegEx out of there!

            // Check for an Annotation class name...
            if(Strings::startsWithUpper($key) || Strings::contains($key, "\\"))
            {
                $annotationClass = $this->findAnnotationClass($key);

                /** @var Annotation $instance */
                $instance = new $annotationClass($type, $value);
                $params = $instance->parse($params, $annotationName);
                continue;
            }

            // Count the total number of occurrences of this particular @<param>...
            $count = count(array_keys($matches[1], $key, true));

            // Handle JSON objects!
            if(preg_match(self::ANNOTATION_PATTERN_JSON, $value, $match))
            {
                $value = json_decode($value, true);
            }
            else
                // Handle Array objects!
                if(preg_match(self::ANNOTATION_PATTERN_ARRAY, $value, $match))
                {
                    // TODO: Determine best way to handle the cases where a property has a type of Type[]!

                    // For now, we just remove the [] at the end of the type name...
                    if(preg_match(self::ANNOTATION_PATTERN_ARRAY_NAMED, $value, $named_match))
                        $value = str_replace("[]", "", $value);

                    $value = eval("return ".$value.";");
                }
                else
                    // Handle Eval objects!
                    if(preg_match(self::ANNOTATION_PATTERN_EVAL, $value, $match))
                    {
                        // TODO: Determine the best way to handle this scenario!
                        //$value = eval("return ".$value.";");
                    }

            // Cleanup both arrays and JSON values, removing leading and trailing whitespace.
            $value = is_array($value) ? array_map("trim", $value) : trim($value);

            // IF there is more than one occurrence of this @<param>...
            if($count > 1)
            {
                // THEN check to see if this is the first occurrence...
                if(!array_key_exists($key, $params))
                {
                    // AND append this value directly to the array, if it is!
                    $params[$key] = $value;
                }
                else
                {
                    // OTHERWISE, append this value to the existing array!

                    // IF the current value is NOT an array...
                    if(!is_array($value))
                    {
                        // THEN, assume the other values under this @<param> are also NOT arrays...
                        if(!is_array($params[$key]))
                        {
                            $oldValue = $params[$key];
                            $params[$key] = [];
                            $params[$key][] = $oldValue;
                        }

                        $params[$key][] = $value;
                    }
                    else
                    {


                        $params[$key] = array_merge($params[$key], $value);
                    }

                }
            }
            else
            {
                // OTHERWISE, this is the only occurrence, simply append it to the array!
                $params[$key] = $value;
            }

        }

        //$this->parameters = $params;
        return $params;


    }








    /**
     * Parses any 'use' statements included in this class file.
     *
     * @param string $file The path to the file for parsing.
     * @return array Returns an array containing the pairing between class/alias and fully qualified class.
     */
    protected function getUseStatements(string $file): array
    {
        $tokens = token_get_all(file_get_contents($file));

        $uses = [];
        $building = false;
        $current = "";

        foreach($tokens as $token)
        {
            // Check to see if we've encountered the class declaration...
            if(is_array($token) && $token[0] === T_CLASS)
            {
                // And break if we have, as we do not want to search beyond here!
                break;
            }

            // Check to see if the current token is the "use" statement...
            if(is_array($token) && $token[0] === T_USE)
            {
                // And if so, start building the namespace.
                $building = true;
                $current = "";
            }

            // Keep appending tokens as long as they are part of the 'use' statement...
            if(is_array($token) && $token[0] !== T_USE && $building)
            {
                $current .= $token[1];
            }

            // Check to see if a semicolon is reached while building the 'use' statement...
            if(!is_array($token) && $token === ";" && $building)
            {
                $building = false;

                // Handle situations where 'as' is used...
                if(Strings::contains($current, " as "))
                {
                    $parts = array_map("trim", explode(" as ", $current));
                    $uses[$parts[1]] = $parts[0];
                }
                else
                {
                    $parts = array_map("trim", explode("\\", $current));
                    $uses[$parts[count($parts) - 1]] = trim($current);
                }
            }
        }

        // Return the array of 'use' pairs of <class> => <fully qualified class>
        return $uses;
    }

    /**
     * Returns the fully qualified class name, even if a non-qualified or aliased class name is provided.
     *
     * @param array $uses An array of 'use' statements, as provided by <b>ClassAnnotationReader->getUseStatements()</b>.
     * @param string $class The class name to be used for the lookup.
     * @return string|null Returns the fully qualified class name.
     *
     * @throws AnnotationReaderException
     */
    public function findAnnotationClass(string $class): ?string
    {
        $annotationClass = "";

        // Handle exact class name matches, including aliases classes...
        if(array_key_exists($class, $this->uses))
            $annotationClass = $this->uses[$class];

        // Handle fully qualified class names...
        if($annotationClass === "" && in_array($class, $this->uses))
        {
            $key = array_search($class, $this->uses);
            $annotationClass = $this->uses[$key];
        }

        // Make certain the class exists before continuing...
        if ($annotationClass !== "" && class_exists($annotationClass)) {
            // Also make certain the class extends 'Annotation'...
            if (!is_subclass_of($annotationClass, Annotation::class, true)) {
                throw new AnnotationReaderException("The annotation class '$annotationClass' must extend '" .
                    Annotation::class . "'!");
            }

            // Return the fully qualified class, if nothing went wrong!
            return $annotationClass;
        }

        // If nothing else matched, then throw an exception!
        throw new AnnotationReaderException("The annotation class '$class' must exist and extend '" .
            Annotation::class . "'!");
        //return null;
    }



    public function getReflectedClass()
    {
        $class = new \ReflectionClass($this->class);
        return $class;
    }

    public function getClassAnnotations(): array
    {
        return $this->parse(self::ANNOTATION_TYPE_CLASS, $this->getReflectedClass()->getDocComment());
    }

    public function getClassAnnotation(string $name): array
    {
        $params = $this->getClassAnnotations();
        return array_key_exists($name, $params) ? $params[$name] : [];
    }

    public function getClassAnnotationsLike(string $pattern): array
    {
        $params = $this->getClassAnnotations();

        $matches = [];

        foreach($params as $key => $value)
            if(preg_match($pattern, $key))
                $matches[$key] = $value;

        return $matches;
    }

    public function hasClassAnnotation(string $name): bool
    {
        $params = $this->getClassAnnotations();
        return array_key_exists($name, $params);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function getReflectedMethods(int $filter = 0): array
    {
        $class = new \ReflectionClass($this->class);
        return $class->getMethods($filter);
    }

    public function getReflectedMethod(string $name)
    {
        $class = new \ReflectionClass($this->class);
        return $class->getMethod($name);
    }

    public function getMethodAnnotations(string $name): array
    {
        $docblock = $this->getReflectedMethod($name)->getDocComment();
        $params = $this->parse(self::ANNOTATION_TYPE_METHOD, $docblock);

        return $params;
    }

    public function getMethodAnnotation(string $name): array
    {
        $params = $this->getMethodAnnotations($name);
        return array_key_exists($name, $params) ? $params[$name] : [];
    }

    public function getMethodAnnotationsLike(string $name, string $pattern): array
    {
        $params = $this->getMethodAnnotations($name);
        $matches = preg_grep($pattern, $params);

        return $matches;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function getReflectedProperties(int $filter = 0): array
    {
        $class = new \ReflectionClass($this->class);
        return $class->getProperties($filter);
    }

    public function getReflectedProperty(string $name)
    {
        $class = new \ReflectionClass($this->class);
        return $class->getProperty($name);
    }

    public function getPropertyAnnotations(string $name): array
    {
        $docblock = $this->getReflectedProperty($name)->getDocComment();
        $params = $this->parse(self::ANNOTATION_TYPE_PROPERTY, $docblock);

        // Get the line containing the '@var <type> ...' declaration.
        $var = array_key_exists("var", $params) ? $params["var"] : null;

        // Ensure the end-user has included a valid DocBlock for this property!
        if($var === null)
            throw new AnnotationReaderException("AnnotationReader->getPropertyInfo() could not find a valid ".
                "'@var [type] \$[name] [description]' entry in the DocBlock");

        // Initialize a collection to store the information about this property.
        $info = [];

        // IF there is a RegEx match for the 'types' and 'name'...
        if(preg_match(self::ANNOTATION_PATTERN_VAR_TYPE, $var, $matches))
        {
            // THEN create an array of all of the types found.
            $info["types"] = array_map(
                function($value)
                {
                    $value = str_replace("[]", "", $value);
                    return trim($value);
                },
                array_filter(explode("|", $matches[1]))
            );

            // AND the name, if found.
            if(count($matches) > 2)
                $info["name"] = trim($matches[2]);

            // AND the description, if found.
            if(count($matches) > 3)
                //$info["description"] = trim(str_replace($matches[0], "", $var));
                $info["description"] = trim($matches[3]);
        }

        $params["var"] = $info;

        // Finally, return the info collection!
        return $params;
    }

    public function getPropertyAnnotation(string $name, string $key): array
    {
        $params = $this->getPropertyAnnotations($name);
        return array_key_exists($key, $params) ? $params[$key] : [];
    }

    public function getPropertyAnnotationsLike(string $name, string $pattern): array
    {
        $params = $this->getPropertyAnnotations($name);
        $matches = preg_grep($pattern, $params);

        return $matches;
    }


}