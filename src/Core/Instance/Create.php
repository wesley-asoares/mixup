<?php

namespace UPFlex\MixUp\Core\Instance;

use ReflectionClass;
use ReflectionException;

abstract class Create
{
    protected static array $params;
    protected static string $parent_class;

    /**
     * @param $class
     * @return array
     * @throws ReflectionException
     */
    protected static function execute($class): array
    {
        $class_instances = [];

        if (is_subclass_of($class, self::$parent_class)) {
            if (call_user_func([$class, 'isSelfInstance'])) {
                // Get info class
                $reflection = new ReflectionClass($class);

                // Return in abstract
                if ($reflection->isAbstract()) {
                    return [];
                }

                // Filter
                $const_params = $reflection->getConstructor()->getParameters();
                $class_child_params = self::getParamsClass($reflection);

                // Check parameters
                if (count($const_params) <= count($class_child_params)) {
                    $instance = new $class(...$class_child_params);
                    $class_instances[] = $instance->isSelfInstance() ? $instance : [];
                } elseif (defined('WP_DEBUG') && true === WP_DEBUG) {
                    error_log(sprintf('%s %s', __('Parameters were not set correctly. Class:'), $class));
                }
            }
        }

        return array_filter($class_instances);
    }

    /**
     * @throws ReflectionException
     */
    protected static function getChildClasses($namespace): void
    {
        $classes = Finder::getClassesInNamespace($namespace);
        if (!$classes) {
            return;
        }

        $classes = array_filter($classes, fn($class) => class_exists($class));

        if (!$classes) {
            return;
        }

        foreach ($classes as $class) {
            self::execute($class);
        }
    }

    /**
     * @param $reflection
     * @return array
     */
    protected static function getParamsClass($reflection): array
    {
        $class_child_params = [];
        $constructor = $reflection->getConstructor();

        // Define parameters
        foreach ($constructor->getParameters() as $param) {
            $class_child_params[] = self::$params[$param->name] ?? null;
        }

        // Filter
        return array_filter($class_child_params);
    }

    /**
     * @param string $namespace
     * @param string $parent_class
     * @param array $params
     * @param string $dir
     * @return bool
     * @throws ReflectionException
     */
    public static function run(string $namespace, string $parent_class, array $params = [], string $dir = ''): bool
    {
        // Seta valores
        self::$params = $params;
        self::$parent_class = $parent_class;

        // Pega todos os namespaces
        $namespaces = Finder::getClassesInNamespace($namespace, $dir);

        if (!$namespaces) {
            return false;
        }

        // Pega todas as classes
        foreach ($namespaces as $namespace) {
            self::execute($namespace);
            self::getChildClasses($namespace);
        }

        return true;
    }
}
