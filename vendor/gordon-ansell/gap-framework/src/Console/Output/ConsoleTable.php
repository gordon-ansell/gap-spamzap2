<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Console\Output;

use GreenFedora\Console\Output\ConsoleTableColumn;
use GreenFedora\Stdlib\Level;

/**
 * A pseudo table for console output.
 */
class ConsoleTable
{
    /**
     * Columns.
     * @var ConsoleTableColumn[]
     */
    protected $cols = null;

    /**
     * Separating spaces.
     * @var int
     */
    protected $sep = 2;

    /**
     * Display the header?
     * @var bool
     */
    protected $hdr = true;

    /**
     * Additional lines.
     * @var callable[]
     */
    protected $additionalLines = [];

    /**
     * Constructor.
     * 
     * @param   int     $sep    Separating spaces between columns.
     * @param   bool    $hdr    Display the header?
     * @return  void
     */
    public function __construct(int $sep = 2, bool $hdr = true)
    {
        $this->sep = $sep;
        $this->hdr = $hdr;
    }

    /**
     * Add a column.
     * 
     * @param   string                          $field          Data field.
     * @param   string|null                     $title          Title.
     * @param   string                          $align          Alignment.
     * @param   int                             $max            Max length.
     * @return  ConsoleTable
     */
    public function addCol(string $field, ?string $title = null, string $align = 'left', int $max = 0): ConsoleTable
    {
        $this->cols[$field] = new ConsoleTableColumn($field, $title, $align, $max);
        return $this;
    }

    /**
     * Add an additional line.
     * 
     * @param   callable        $func       Callable function.
     * @return  ConsoleTable
     */
    public function addLine(callable $func): ConsoleTable
    {
        $this->additionalLines[] = $func;
        return $this;
    }

    /**
     * Parse some data through this table.
     * 
     * @param   array           $data   Array of data to parse.
     * @return  array                   Array of lines.
     */
    public function parse(array $data) : array
    {
        // Run a first parse to get the maximum lengths.
        foreach ($data as $record) {
            foreach ($record as $field => $val) {
                if (array_key_exists($field, $this->cols)) {
                    $this->cols[$field]->checkMax($val);
                }
            }
        }

        // Now run the second parse to create all the lines.
        $lines = [];

        if (true === $this->hdr) {
            $line1 = '';
            $line2 = '';
            foreach ($this->cols as $field => $col) {
                $min = $col->dataMax;
                if (0 !== $col->max and $col->max < $min) {
                    $min = $col->max; 
                }

                $title = $col->title;

                if (0 === $min) {
                    $min = strlen($title);
                }

                if (strlen($title) > $min) {
                    $title = substr($title, 0, $min);
                }

                $pad = $col->dataMax;
                if (0 !== $col->max and $col->max < $col->dataMax) {
                    $pad = $col->max;
                }

                if ('' != $line1) {
                    $line1 .= str_repeat(' ', $this->sep);
                    $line2 .= str_repeat(' ', $this->sep);
                }

                if ('left' === $col->align) {
                    $line1 .= str_pad($title, $pad, ' ', STR_PAD_RIGHT);
                    $line2 .= str_pad(str_repeat('-', strlen($title)), $pad, ' ', STR_PAD_RIGHT);
                } else if ("centre" === $col->align) {
                    $line1 .= str_pad($title, $pad, ' ', STR_PAD_BOTH);
                    $line2 .= str_pad(str_repeat('-', strlen($title)), $pad, ' ', STR_PAD_BOTH);
                } else {
                    $line1 .= str_pad($title, $pad);
                    $line2 .= str_pad(str_repeat('-', strlen($title)), $pad);
                }

            }
            $lines[] = $line1;
            $lines[] = $line2;
        }

        echo '--' . PHP_EOL;

        $count = 0;
        foreach ($data as $record) {
            $line = '';
            foreach ($this->cols as $field => $col) {
                if (array_key_exists($field, $record)) {
                    $val = $record[$field];
                } else {
                    $val = null;
                }

                if (is_null($val)) {
                    $val = '';
                }

                if (is_string($val)) {
                    $val = preg_replace('/[[:^print:]]/', '', $val);
                }

                $tmp = '';
                $pad = $col->dataMax;
                if (0 !== $col->max and $col->max < $col->dataMax) {
                    $tmp = substr(strval($val), 0, $col->max);
                    $pad = $col->max;
                    if (strlen(strval($tmp)) < $col->max) {
                        $tmp = $val;
                        $pad = $col->max;
                    }   
                } else {
                    $tmp = $val;
                }

                if (0 === $pad) {
                    $pad = strlen($col->title);
                }

                if ('' != $line) {
                    $line .= str_repeat(' ', $this->sep);
                }

                $tmp = strval($tmp);

                $op = '';
                if ('left' === $col->align) {
                    $op = str_pad($tmp, $pad, ' ', STR_PAD_RIGHT);
                } else if ("centre" === $col->align) {
                    $op = str_pad($tmp, $pad, ' ', STR_PAD_BOTH);
                } else {
                    $op = str_pad($tmp, $pad, ' ', STR_PAD_LEFT);
                }

                $line .= $op;
            }
            $lines[] = $line;

            if (count($this->additionalLines) > 0) {
                foreach ($this->additionalLines as $c) {
                    $result = $c($record);
                    if (!is_null($result)) {
                        $lines[] = $result;
                    }
                }
            }
            $count++;
        }

        // Return the lines.
        return $lines;
    }

    /**
     * Output the lines.
     * 
     * @param   OutputInterface     $op         Where to output.
     * @param   array               $entries    Entries to parse.
     * @return  void
     */
    public function output(OutputInterface $op, array $entries)
    {
        $lines = $this->parse($entries);

        foreach ($lines as $line) {
            if (!is_array($line)) {
                $op->notice($line);
            } else {
                $op->writeln($line[0], $line[1]);
            }
        }
    }

}
