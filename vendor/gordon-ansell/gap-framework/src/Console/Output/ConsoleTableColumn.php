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

use GreenFedora\Console\Output\Exception\InvalidArgumentException;

/**
 * A column for a console table.
 * 
 * @property-read   string  $field  
 * @property-read   string  $title  
 * @property-read   string  $align
 * @property-read   int     $max  
 * @property-read   int     $dataMax    
 */
class ConsoleTableColumn
{
    /**
     * Title.
     * @var string
     */
    protected $title = null;

    /**
     * Data field.
     * @var string
     */
    protected $field = null;

    /**
     * Alignment.
     * @var string
     */
    protected $align = 'left';

    /**
     * Maximum length.
     * @var int
     */
    protected $max = 0;

    /**
     * The actual max of the data.
     * @var int
     */
    protected $dataMax = 0;

    /**
     * Constructor.
     * 
     * @param   string                          $field          Data field.
     * @param   string|null                     $title          Title.
     * @param   string                          $align          Alignment.
     * @param   int                             $max            Maximum length.
     * @return  void
     */
    public function __construct(string $field, ?string $title = null, string $align = 'left', int $max = 0)
    {
        $this->field = $field;
        if (is_null($title)) {
            $title = ucfirst($this->field);
        } 
        $this->title = $title;
        $this->align = $align;
        $this->max = $max;
    }

    /**
     * Set the data max.
     * 
     * @param   int     $len    Length to set it to.
     * @return  void
     */
    public function setDataMax(int $len)
    {
        $this->dataMax = $len;
    }

    /**
     * Possibly set the datamax based on an input string.
     * 
     * @param   mixed   $test   Data to test.
     * @return  mixed           The input variable is returned.
     */
    public function checkMax($test)
    {
        if (strlen(strval($test)) > $this->dataMax) {
            $this->dataMax = strlen(strval($test));
        }
        return $test;
    }

    /**
     * Getter.
     * 
     * @param   string      $name   Field to get.
     * @return  mixed
     * @throws  InvalidArgumentException
     */
    public function __get(string $name)
    {
        if (in_array($name, ['field', 'title', 'align', 'max', 'dataMax'])) {
            return $this->$name;
        }
        throw new InvalidArgumentException(sprintf("'%s' is not a valid console table column field."));
    }

}
