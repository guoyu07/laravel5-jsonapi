<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 10/16/15
 * Time: 8:59 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApiSerializer\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class MappingFactory
 * @package NilPortugues\Laravel5\JsonApiSerializer\Mapper
 */
class MappingFactory extends \NilPortugues\Api\Mapping\MappingFactory
{
    /**
     * @var array
     */
    protected static $eloquentClasses = [];

    /**
     * @param string $className
     *
     * @return array
     */
    protected static function getClassProperties($className)
    {
        if (class_exists($className, true)) {
            $reflection = new ReflectionClass($className);
            $value = $reflection->newInstanceWithoutConstructor();

            if (is_subclass_of($value, Model::class, true)) {
                $attributes =  array_merge(
                    Schema::getColumnListing($value->getTable()),
                    self::getRelationshipMethodsAsPropertyName($className, $reflection)
                );

                self::$eloquentClasses[$className] = $attributes;

                return self::$eloquentClasses[$className];
            }

        }

        return parent::getClassProperties($className);
    }

    /**
     * @param string          $className
     * @param ReflectionClass $reflection
     *
     * @return array
     */
    protected static function getRelationshipMethodsAsPropertyName($className, ReflectionClass $reflection)
    {
        $methods = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (ltrim($method->class, "\\") === ltrim($className, "\\")) {
                $methods[] = $method->name;
            }
        }

        return $methods;
    }
}
