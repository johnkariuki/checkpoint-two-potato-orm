<?php

namespace Potato\Tests;

use Potato\Manager\PotatoModel;

/**
 * Sample User class that extends PotatoModel.
 */
class User extends PotatoModel
{
    /**
     * If set, contains the table name.
     *
     * Overwrites $table field in PotatoModel
     *
     * @var string
     */
    protected static $table = 'user_table';

    /**
     * The unique ID for a particular table.
     *
     * Overwrites uniqueId field in PotatoModel
     *
     * @var string
     */
    protected static $uniqueId = 'user_id';
}
