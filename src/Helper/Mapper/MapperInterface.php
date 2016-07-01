<?php

/*
 * Copyright 2014 Bitmarshals Digital <sanicms@bitmarshals.co.ke>.
 */

namespace Helper\Mapper;

/**
 * Ensure that each mapper can add edit and delete
 * 
 * ^^ under conceptualization
 * 
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
interface MapperInterface
{
    /**
     * For a to many relationship use an array
     * with a $data[links][resource_name] 
     * 
     * @param array|EntityInterface $data
     */
    public function add($data);

    public function replace($id, $params);

    public function remove($id);
}
