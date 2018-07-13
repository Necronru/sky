<?php


namespace Integration;

use DataProvider\DataProviderInterface;

class NullDataProvider implements DataProviderInterface
{
    /**
     * @param array $request
     *
     * @return mixed|null
     */
    public function get(array $request)
    {
        return null;
    }
}