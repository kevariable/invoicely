<?php

namespace Invoice\Base;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;

final class DataHydration
{
    /**
     * Create an instance using Valinor mapping
     */
    public static function make(string $className, mixed $data): mixed
    {
        try {
            return (new MapperBuilder())
                ->allowSuperfluousKeys()
                ->enableFlexibleCasting()
                ->mapper()
                ->map($className, Source::array($data));
        } catch (MappingError $error) {
            throw new \InvalidArgumentException(
                "Failed to hydrate {$className}: " . $error->getMessage(),
                previous: $error
            );
        }
    }

    /**
     * Create from array data
     */
    public static function fromArray(string $className, array $data): mixed
    {
        return self::make($className, $data);
    }

    /**
     * Normalize data by converting keys to snake_case
     */
    public static function normalize(array $data): array
    {
        $normalized = [];
        
        foreach ($data as $key => $value) {
            $snakeKey = self::toSnakeCase($key);
            
            if (is_array($value)) {
                $normalized[$snakeKey] = self::normalize($value);
            } else {
                $normalized[$snakeKey] = $value;
            }
        }
        
        return $normalized;
    }

    /**
     * Convert camelCase to snake_case
     */
    private static function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
}