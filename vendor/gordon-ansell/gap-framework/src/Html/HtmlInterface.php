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
namespace GreenFedora\Html;

/**
 * HTML statement interface.
 */
interface HtmlInterface
{
		/**
	 * Render the open.
	 * 
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function renderOpen(?array $extraParams = null): string;
		 	
	/**
	 * Render the close.
	 * 
	 * @param 	string|null	$data 	Data.
	 * @return 	string
	 */
	public function renderClose(?string $data = null): string;

	/**
	 * Build the statement.
	 *
	 * @param 	string|null	$data 	Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string;
	
	/**
	 * Get the tag.
	 * 
	 * @return	string
	 */
	public function getTag(): string;

	/**
	 * Set the tag.
	 * 
	 * @param 	string 	$tag 	Tag to set.
	 * @return 	HtmlInterface
	 */
	public function setTag(string $tag): HtmlInterface;

	/**
	 * Set the data.
	 *
	 * @param 	mixed 	$data 	Data to set.
	 * @return  HtmlInterface
	 */
	public function setData($data) : self;

    /**
     * Set a parameter.
     * 
     * @param   string  $name   Name of parameter to set.
     * @param   mixed   $val    Value to set.
     * @return  HtmlInterface 
     */
    public function setParam(string $name, $val): self;
	
    /**
     * Set a parameter array.
     * 
     * @param   array   $params   Array of parameters.
     * @return  HtmlInterface 
     */
    public function setParams(array $params): self;

    /**
     * Append a parameter.
     * 
     * @param   string  $name   Name of parameter to set.
     * @param   mixed   $val    Value to set.
     * @return  HtmlInterface 
     */
    public function appendParam(string $name, $val): self;

	/**
	 * Get a parameter.
	 * 
	 * @param 	string 	$name 	Name of parameter to get.
	 * @param 	bool 	$throw 	Throw exceptions?
	 * @return 	mixed
	 * @throws  InvalidArgumentException
	 */
	public function getParam(string $name, bool $throw = true);

	/**
	 * Get all parameters.
	 * 
	 * @return 	array
	 */
	public function getParams(): array;

	/**
	 * Remove a parameter.
	 * 
	 * @param	string 	$key 	Parameter to remove.
	 * @return 	HtmlInterface 
	 */
	public function removeParam(string $key): self;

	/**
	 * Remove a parameter item.
	 * 
	 * @param	string 	$key 	Parameter to remove from.
	 * @param 	mixed 	$val 	Value to remove.
	 * @return 	HtmlInterface 
	 */
	public function removeParamItem(string $key, $val): self;

	/**
	 * Do we have a parameter?
	 * 
	 * @param 	string 	$name 	Name of parameter to check.
	 * @return 	bool
	 */
	public function hasParam(string $name): bool;

	/**
	 * Set the self-closing flag.
	 *
	 * @param 	bool 	$sc 	Value.
	 * @return  HtmlInterface
	 */
	public function setSelfClose(bool $sc = true) : self;

}
