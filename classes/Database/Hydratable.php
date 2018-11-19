<?php

namespace CRMConnector\Database;

/**
 * Interface Hydrateable
 * @package CRMConnector\Database
 */
interface Hydratable
{
    /**
     * @param array $array
     * @return mixed
     */
    public function fromArray(array $array);
}