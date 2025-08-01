<?php

namespace Invoice\Base;

abstract readonly class DataReadonly
{
    /**
     * Create instance from array data using Valinor hydration
     */
    public static function hydrate(array $data): static
    {
        return DataHydration::fromArray(static::class, $data);
    }

    /**
     * Convert the DTO to an array with snake_case keys
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $result = [];
        
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            
            // Convert property name to snake_case
            $key = $this->toSnakeCase($property->getName());
            
            if ($value instanceof DataReadonly) {
                $result[$key] = $value->toArray();
            } elseif ($value instanceof ValueObject) {
                $result[$key] = $value->toValue();
            } elseif (is_array($value)) {
                $result[$key] = $this->convertArrayToSnakeCase($value);
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Convert camelCase to snake_case
     */
    private function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }

    /**
     * Convert array values recursively
     */
    private function convertArrayToSnakeCase(array $array): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $snakeKey = is_string($key) ? $this->toSnakeCase($key) : $key;
            
            if ($value instanceof DataReadonly) {
                $result[$snakeKey] = $value->toArray();
            } elseif ($value instanceof ValueObject) {
                $result[$snakeKey] = $value->toValue();
            } elseif (is_array($value)) {
                $result[$snakeKey] = $this->convertArrayToSnakeCase($value);
            } else {
                $result[$snakeKey] = $value;
            }
        }
        
        return $result;
    }
}