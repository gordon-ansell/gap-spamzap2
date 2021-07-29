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

use GreenFedora\Html\HtmlInterface;
use GreenFedora\Html\Exception\InvalidArgumentException;

/**
 * HTML statement base class.
 */
class Html implements HtmlInterface
{
	/**
	 * Tag.
	 * @var string
	 */
	protected $tag = '';

    /**
     * Parameters.
     * @var array
     */
    protected $params = [];

    /**
     * Data.
     * @var string|null
     */
    protected $data = null;
	
	/**
	 * Self closing?
	 * @var bool|null
	 */
	protected $selfClose = null;	

	/**
	 * Value param = data?
	 * @var bool|null
	 */
	protected $valueData = null;

	/**
	 * Self closing tags.
	 * @var array
	 */
	protected $selfClosers = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link',
		'meta', 'param', 'source', 'track', 'wbr'];

    /**
     * Constructor.
     * 
     * @param   string      $tag        HTML tag.
     * @param   array       $params     Parameters.
     * @param   string|null $data       Data.
     * @param   bool|null   $selfClose  Self-closing?
	 * @param 	bool|null   $valueData  Treat value param as data.
	 * @return 	void
     */
    public function __construct(string $tag, array $params = [], ?string $data = null, 
		?bool $selfClose = null, ?bool $valueData = null)
    {
        $this->tag = $tag;
        $this->setParams($params);
        $this->data = $data;
		$this->setSelfClose($selfClose);
		$this->setValueData($valueData);
    }

	/**
	 * Render the open.
	 * 
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function renderOpen(?array $extraParams = null): string
	{
		if ($this->valueData and array_key_exists('value', $this->params)) {
			if (null === $this->data) {
				$this->data = $this->params['value'];
				unset($this->params['value']);
			}
		}

		$ret = '<' . $this->tag;

        $paramsArr = $this->params;
        if (!is_null($extraParams) and count($extraParams) > 0) {
            foreach ($extraParams as $k => $v) {
                if (isset($paramsArr[$k])) {
                    $paramsArr[$k] .= ' ' . $v;
                } else {
                    $paramsArr[$k] = $v;
                }
            }
        }
		
		$params = '';
		foreach ($paramsArr as $key => $value) {
			$standalone = is_bool($value);


			if (!empty($value) or $standalone) {
				if ('' != $params) {
					$params .= ' ';
				}
				if ($standalone) {
                    $params .= $key;
				} else {
					$params .= $key . '="' . $value . '"';
				}
			} 
		}
		
		if ('' != $params) {
			$params = ' ' . $params;
		}
		
        $ret .= $params;

		if ($this->selfClose) {
			$ret .= ' />';
		} else {
			$ret .= '>';
		}

        return $ret;
	}
		 	
	/**
	 * Render the close.
	 * 
	 * @param 	string|null	$data 	Data.
	 * @return 	string
	 */
	public function renderClose(?string $data = null): string
	{

		$ret = '';
		
		if ((null === $data) and (null !== $this->data)) {
			$data = $this->data;
		}
		if (null !== $data) {
			$ret .= $data;
		}
		$ret .= $this->beforeClosingTag();
		$ret .= '</' . $this->tag . '>';
		
		return $ret;
	}

	/**
	 * Render the statement.
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
	{
		$ret = $this->renderOpen($extraParams); 
		
		if (!$this->selfClose) {
			$ret .= $this->renderClose($data);
		}

        /*
        if (isset($this->params['type']) and 'checkbox' == $this->params['type']) {
            var_dump(htmlentities($ret));
            \wp_die('html1');
        }
        */
		return $ret;
	}

	/**
	 * Get the tag.
	 * 
	 * @return	string
	 */
	public function getTag(): string
	{
		return $this->tag;
	}

	/**
	 * Set the tag.
	 * 
	 * @param 	string 	$tag 	Tag to set.
	 * @return 	HtmlInterface
	 */
	public function setTag(string $tag): HtmlInterface
	{
		$this->tag = $tag;
		return $this;
	}
	
	/**
	 * Set the data.
	 *
	 * @param 	mixed 	$data 	Data to set.
	 * @return  HtmlInterface
	 */
	public function setData($data) : HtmlInterface
	{
		$this->data = $data;
		return $this;
	}

    /**
     * Set a parameter.
     * 
     * @param   string  $name   Name of parameter to set.
     * @param   mixed   $val    Value to set.
     * @return  HtmlInterface 
     */
    public function setParam(string $name, $val): HtmlInterface
    {
        $this->params[$name] = $val;
        return $this;
    }
	
    /**
     * Set a parameter array.
     * 
     * @param   array   $params   Array of parameters.
     * @return  HtmlInterface 
     */
    public function setParams(array $params): HtmlInterface
    {
		foreach ($params as $key => $value) {
			$this->setParam($key, $value);
		}
        return $this;
    }

    /**
     * Append a parameter.
     * 
     * @param   string  $name   Name of parameter to set.
     * @param   mixed   $val    Value to set.
     * @return  HtmlInterface 
     */
    public function appendParam(string $name, $val): HtmlInterface
    {
		if (!array_key_exists($name, $this->params)) {
        	$this->params[$name] = $val;
		} else {
			$this->params[$name] .= ' ' . $val;
		}
        return $this;
    }

	/**
	 * Get a parameter.
	 * 
	 * @param 	string 	$name 	Name of parameter to get.
	 * @param 	bool 	$throw 	Throw exceptions?
	 * @return 	mixed
	 * @throws  InvalidArgumentException
	 */
	public function getParam(string $name, bool $throw = true)
	{
		if (array_key_exists($name, $this->params)) {
			return $this->params[$name];
		} else if ($throw) {
			throw new InvalidArgumentException(sprintf("Html statement does not have parameter '%s'", $name));
		}
		return null;
	}

	/**
	 * Get all parameters.
	 * 
	 * @return 	array
	 */
	public function getParams(): array
	{
		return $this->params;
	}

	/**
	 * Remove a parameter.
	 * 
	 * @param	string 	$key 	Parameter to remove.
	 * @return 	HtmlInterface 
	 */
	public function removeParam(string $key): HtmlInterface
	{
		unset($this->params[$key]);
		return $this;
	}

	/**
	 * Remove a parameter item.
	 * 
	 * @param	string 	$key 	Parameter to remove from.
	 * @param 	mixed 	$val 	Value to remove.
	 * @return 	HtmlInterface 
	 */
	public function removeParamItem(string $key, $val): HtmlInterface
	{
		if (!$this->hasParam($key)) {
			return $this;
		}
		$p = explode(' ', $this->getParam($key));
		$new = [];
		foreach ($p as $item) {
			if ($item != $val) {
				$new[] = $item;
			}
		}
		$this->setParam($key, implode(' ', $new));
		return $this;
	}

	/**
	 * Do we have a parameter?
	 * 
	 * @param 	string 	$name 	Name of parameter to check.
	 * @return 	bool
	 */
	public function hasParam(string $name): bool
	{
		return array_key_exists($name, $this->params);
	}

	/**
	 * Set the self-closing flag.
	 *
	 * @param 	bool 	$sc 	Value.
	 * @return  HtmlInterface
	 */
	public function setSelfClose(?bool $sc = true) : HtmlInterface
	{
		if (null === $sc) {
			$this->selfClose = in_array($this->tag, $this->selfClosers);
		} else {
			$this->selfClose = $sc;
		}
		return $this;
	}

	/**
	 * Set the value = data flag.
	 *
	 * @param 	bool 	$sc 	Value.
	 * @return  HtmlInterface
	 */
	public function setValueData(?bool $vd = true) : HtmlInterface
	{
		if (null === $vd) {
			$this->valueData = !in_array($this->tag, $this->selfClosers);
		} else {
			$this->valueData = $vd;
		}
		return $this;
	}

	/**
	 * Build anything before the closing tag.
	 *
	 * @return string
	 */
	protected function beforeClosingTag() : string
	{
		return '';
	}	
}
