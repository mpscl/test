<?php
declare(strict_types=1);

namespace App\Helpers;


use App\Serializer\Exclusion\DepthExclusionStrategy;

class SerializerHelper
{
    /**
     * Get depth parameter from the request.
     *
     * @return int
     */
    public static function getDepth(): int
    {
        $request = AppHelper::getRequestStack()->getCurrentRequest();

        return $request->query->getInt('depth', DepthExclusionStrategy::MAX_DEPTH);
    }
}