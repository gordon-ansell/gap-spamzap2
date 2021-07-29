<?php

/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Table;

use GreenFedora\Filter\FilterInterface;

/**
 * Table column interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface ColumnInterface
{
    /**
     * Get the name.
     * 
     * @var string
     */
    public function getName(): string;

    /**
     * Set the title.
     * 
     * @param  string  $title   Title.
     * @return ColumnInterface 
     */
    public function setTitle(string $class): ColumnInterface;

    /**
     * Add a header class.
     * 
     * @param   string  $class  Class to add.
     * @return  ColumnInterface
     */
    public function addHdrClass(string $class): ColumnInterface;

    /**
     * Set the header class.
     * 
     * @param   string  $class  Class to set.
     * @return  ColumnInterface
     */
    public function setHdrClass(string $class): ColumnInterface;

    /**
     * Add a body class.
     * 
     * @param   string  $class  Class to add.
     * @return  ColumnInterface
     */
    public function addBodyClass(string $class): ColumnInterface;

    /**
     * Set the body class.
     * 
     * @param   string  $class  Class to set.
     * @return  ColumnInterface
     */
    public function setBodyClass(string $class): ColumnInterface;

    /**
     * Add a filter.
     * 
     * @param   FilterInterface     $filter     New filter.
     * @return  ColumnInterface 
     */
    public function addFilter(FilterInterface $filter): ColumnInterface;

    /**
     * Filter the field.
     * 
     * @param   mixed   $source     Source to filter.
     * @return  mixed
     */
    public function filter($source);

    /**
     * Set the column status.
     * 
     * @param   int     $status     Status to set.
     * @return  ColumnInterface
     */
    public function setStatus(int $status) : ColumnInterface;

    /**
     * Set the column status hidden.
     * 
     * @param   int     $status     Status to set.
     * @return  ColumnInterface
     */
    public function setHidden() : ColumnInterface;

    /**
     * See if column is hidden.
     * 
     * @return  bool
     */
    public function isHidden(): bool;

    /**
     * Render the header.
     * 
     * @param   string      $class      Additional classes.
     * @param   string      $tag        Tag to render.
     * @param   bool        $isBlank    Blank.
     * @return  string
     */
    public function renderHdr(?string $class = null, ?string $tag = null, bool $isBlank = false): string;

    /**
     * Render the body.
     * 
     * @param   mixed   $data   Data to render.
     * @param   string  $class  Additional classes.
     * @param   string  $tag    Tag to render.
     * @return  string
     */
    public function renderBody($data, ?string $class = null, ?string $tag = null): string;
}
