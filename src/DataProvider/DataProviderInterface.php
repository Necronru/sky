<?php


namespace DataProvider;


interface DataProviderInterface
{
    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function get(array $parameters);

}