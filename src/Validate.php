<?php
/**
 * The file for the validate-file-size service
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\ValidateFileSize;

use InvalidArgumentException;

/**
 * The validate-file-size service
 *
 * @since  0.1.0
 */
class Validate
{
	/* !Private properties */
	
	/**
	 * @var    int|null  the maximum file size in bytes (inclusive)
	 * @since  0.1.0
	 */
	private $max;
	
	/**
	 * @var    int|null  the minimum file size in bytes (inclusive)
	 * @since  0.1.0
	 */
	private $min;
	
	
	/* !Magic methods */
	
	/**
	 * Called when the service is constructed
	 *
	 * @param   int|null  $min  the min file size in bytes (inclusive) (optional)
	 * @param   int|null  $max  the max file size in bytes (inclusive) (optional)
	 * @throws  InvalidArgumentException  if $min is negative
	 * @throws  InvalidArgumentException  if $max is negative
	 * @throws  InvalidArgumentException  if $max is less than $min
	 * @since   0.1.0
	 */
	public function __construct(int $min = null, int $max = null)
	{
		// if min exists and is negative
		if ($min !== null && $min < 0) {
			throw new InvalidArgumentException(
				__METHOD__ . "() expects parameter one, min, to be a positive int "
					. "or zero"
			);	
		}
		
		// if max exists and is negative
		if ($max !== null && $max < 0) {
			throw new InvalidArgumentException(
				__METHOD__ . "() expects paramter one, max, to be a positive int "
					. "or zero"
			);
		}
		
		// if min and max exist and the max is less than the min, short-circuit
		if ($min !== null && $max !== null && $max < $min) {
			throw new InvalidArgumentException(
				__METHOD__ . "() expects parameter one, min, to be less than or "
					. "equal to parameter two, max"
			);
		}
		
		$this->min = $min;
		$this->max = $max;
	}
	
	/**
	 * Called when the service is treated like a function
	 *
	 * @param   string  $filename  the file name
	 * @return  bool
	 * @throws  InvalidArgumentException  if $filename is not readable
	 * @since   0.1.0
	 */
	public function __invoke(string $filename): bool
	{
		// if the file is not readable, short-circuit
		if ( ! is_readable($filename)) {
			throw new InvalidArgumentException(
				__METHOD__ . "() expects parameter one, filename, to be the "
					. "absolute path to a readable file"
			);
		}
		
		// otherwise, get the filesize
		$size = filesize($filename);
		
		// compare the file size based on limits
		if ($this->min && $this->max) {
			$isValid = $size >= $this->min && $size <= $this->max;
		} elseif ($this->min) {
			$isValid = $size >= $this->min;
		} elseif ($this->max) {
			$isValid = $size <= $this->max;
		} else {
			// otherwise, there are no limits!
			$isValid = true;
		}
		
		return $isValid;
	}
}
