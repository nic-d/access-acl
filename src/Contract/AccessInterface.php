<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 14:56
 */

namespace Nybbl\AccessAcl\Contract;

/**
 * Interface AccessInterface
 * @package Nybbl\AccessAcl\Contract
 */
interface AccessInterface
{
    const ACCESS_GRANTED = 1;
    const AUTH_REQUIRED  = 2;
    const ACCESS_DENIED  = 3;
}