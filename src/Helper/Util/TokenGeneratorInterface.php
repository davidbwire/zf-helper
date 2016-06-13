<?php

/*
 * This file is part of the FOSUserBundle package. It was modified by
 * David Bwire for use in the Helper Module
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Helper\Util;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
}
