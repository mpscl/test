<?php
declare(strict_types=1);

namespace App\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class DepthExclusionStrategy implements ExclusionStrategyInterface
{

    public const MAX_DEPTH = 1;

    /**
     * @var int
     */
    private $maxDepth;

    public function __construct(int $maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return $this->isTooDeep($context);
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        return $this->isTooDeep($context);
    }

    private function isTooDeep(Context $context): bool
    {
        $depth = $context->getDepth();
        $metadataStack = $context->getMetadataStack();

        $nthProperty = 0;
        // iterate from the first added items to the lasts
        for ($i = $metadataStack->count() - 1; $i > 0; $i--) {
            $metadata = $metadataStack[$i];
            if ($metadata instanceof PropertyMetadata) {
                $metadata->maxDepth = $this->maxDepth;
                $nthProperty++;
                $relativeDepth = $depth - $nthProperty;

                if (null !== $metadata->maxDepth && $relativeDepth > $metadata->maxDepth) {
                    return true;
                }
            }
        }

        return false;
    }
}