<?php
declare(strict_types=1);
namespace Bitmotion\Auth0\Extractor\PropertyTypeExtractor;

use Bitmotion\Auth0\Domain\Model\Auth0\ResourceServer\Scope;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class ResourceServerExtractor implements PropertyTypeExtractorInterface
{
    private $reflectionExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    public function getTypes($class, $property, array $context = [])
    {
        if ($property === 'scopes') {
            return [
                new Type(
                    Type::BUILTIN_TYPE_OBJECT,
                    true,
                    Scope::class . '[]'
                ),
            ];
        }

        return $this->reflectionExtractor->getTypes($class, $property, $context);
    }
}