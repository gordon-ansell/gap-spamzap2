<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Domain;

/**
 * Type codes.
 */
class TypeCodes
{
    // Types.
    const TYPE_COMMENT = 1;
    const TYPE_REG = 2;
    const TYPE_CONTACT = 3;
    const TYPE_INFO = 4;
    const TYPE_LOSTPASSWORD = 5;
    const TYPE_LOGIN = 6;

    // TYPE strings.
    const TYPESTRS_TYPE = [
        self::TYPE_COMMENT => 'Comment',
        self::TYPE_REG => 'Register',
        self::TYPE_CONTACT => 'Contact',
        self::TYPE_INFO => 'Info',
        self::TYPE_LOSTPASSWORD => 'Lost Password',
        self::TYPE_LOGIN => 'Login',
    ];

    // TYPE strings short.
    const TYPESTRS_TYPESHORT = [
        self::TYPE_COMMENT => 'Com',
        self::TYPE_REG => 'Reg',
        self::TYPE_CONTACT => 'Con',
        self::TYPE_INFO => 'Inf',
        self::TYPE_LOSTPASSWORD => 'Pass',
        self::TYPE_LOGIN => 'Lgn',
    ];

    // Statuses.
    const STATUS_BLOCK = 1;
    const STATUS_ALLOW = 2;
    const STATUS_ERROR = 3;
    const STATUS_INFO = 4;

    // STATUS strings.
    const TYPESTRS_STATUS = [
        self::STATUS_BLOCK => 'Block',
        self::STATUS_ALLOW => 'Allow',
        self::STATUS_ERROR => 'Error',
        self::STATUS_INFO => 'Info',
    ];

    // Match types.
    const MT_LOGGED_IN          =   1;
    const MT_NEW_RULE           =   2;
    const MT_REG_ERROR          =   3;
    const MT_PASSED             =   4;
    const MT_IP_BLOCK           =   5;
    const MT_IP_ALLOW           =   6;
    const MT_DOM_AURL           =   7;
    const MT_DOM_EMAIL          =   8;
    const MT_DOM_COMMENT        =   9;
    const MT_EMAIL_BLOCK        =   10;
    const MT_STRING_BLOCK_COM   =   11;
    const MT_STRING_BLOCK_USER  =   12;
    const MT_BLOCK_ALL          =   13;
    const MT_LP_ERROR           =   14;
    const MT_LOGIN_ERROR        =   15;
    const MT_LOGIN_AUTH         =   16;

    // MATCH type strings.
    const TYPESTRS_MT = [
        self::MT_LOGGED_IN          => "Logged In",
        self::MT_NEW_RULE           => "New Rule",
        self::MT_REG_ERROR          => "Reg Error",
        self::MT_PASSED             => "Passed",
        self::MT_IP_BLOCK           => "IP Blocked",
        self::MT_IP_ALLOW           => "IP Whitelisted",
        self::MT_DOM_AURL           => "Author URL Domain",
        self::MT_DOM_EMAIL          => "Email Domain",
        self::MT_DOM_COMMENT        => "Comment Domain",
        self::MT_EMAIL_BLOCK        => "Email Blocked",    
        self::MT_STRING_BLOCK_COM   => "String Comment",
        self::MT_STRING_BLOCK_USER  => "String Username",
        self::MT_BLOCK_ALL          => "Block All",
        self::MT_LP_ERROR           => "Lost Pass Error",
        self::MT_LOGIN_ERROR        => "Login Error",
        self::MT_LOGIN_AUTH         => "Auth Process", 
    ];
}