<?php
/**
 * The file for the validate-file-size tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\ValidateFileSize;

use Jstewmc\TestCase\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Tests for the validate-file-size service tests
 */
class ValidateTest extends TestCase
{
	/* !Private properties */
	
	/**
	 * @var  string  the test filename
	 */
	private $filename;
	
	/**
     * @var  vfsStreamDirectory  the "root" virtual file system directory
     */
    private $root;
    
    /**
	 * @var  int  the file's size in bytes
	 */
	private $size;
    
	
	/* !Framework methods */
    
    /**
     * Called before every test
     *
     * @return  void
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('test');
        
        $this->filename = vfsStream::url('test/foo.php');
        
        $contents = 'foo';
        
        $this->size = strlen($contents);
        
        file_put_contents($this->filename, $contents);
        
        return;
    }
    
	
	/* !__construct() */
	
	/**
	 * __construct() should throw exception if min is negative
	 */
	public function testConstructThrowsExceptionIfMinIsNegative()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		(new Validate(-1));
		
		return;
	}
	
	/**
	 * __construct() should throw exception if max is negative
	 */
	public function testConstructThrowsExceptionIfMaxIsNegative()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		(new Validate(null, -1));
		
		return;
	}
	
	/**
	 * __construct() throws exception if min and max are invalid
	 */
	public function testConstructThrowsExceptionIfMinAndMinAreInvalid()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		(new Validate(999, 1));
		
		return;
	}
	
	/**
	 * __construct() should set the service's properties
	 */
	public function testConstructSetsPropertiesIfMinAndMaxAreValid()
	{
		$min = 1;
		$max = 999;
		
		$service = (new Validate($min, $max));
		
		$this->assertEquals($min, $this->getProperty('min', $service));
		$this->assertEquals($max, $this->getProperty('max', $service));
		
		return;
	}
	
	
	/* !__invoke() */
	
	/**
	 * __invoke() should throw exception if the file does not exist
	 */
	public function testInvokeThrowsExceptionIfFileDoesNotExist()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		(new Validate())(vfsStream::url('path/to/bar.php'));
		
		return;
	}
	
	/**
	 * __invoke() should return false if file is below min
	 */
	public function testInvokeReturnsFalseIfFileBelowMin()
	{
		$service = new Validate($this->size + 1);
		
		$this->assertFalse($service($this->filename));
		
		return;
	}
	
	/**
	 * __invoke() should return true if file is above min
	 */
	public function testInvokeReturnsTrueIfFileAboveMin()
	{
        $service = new Validate($this->size - 1);
        
        $this->assertTrue($service($this->filename));
        
        return;
	}
	
	/**
	 * __invoke() should return true if file is below max
	 */
	public function testInvokeReturnsTrueIfFileBelowMax()
	{
        $service = new Validate(null, $this->size + 1);
        
        $this->assertTrue($service($this->filename));
        
        return;
	}
	
	/**
	 * __invoke() should return false if file is above max
	 */
	public function testInvokeReturnsFalseIfFileAboveMax()
	{
        $service = new Validate(null, $this->size - 1);
        
        $this->assertFalse($service($this->filename));
        
        return;
	}
	
	/**
	 * __invoke() should return true if file is inside range
	 */
	public function testInvokeReturnsTrueIfFileIsInsideRange()
	{
        $service = new Validate($this->size - 1, $this->size + 1);
        
        $this->assertTrue($service($this->filename));
        
        return;
	}
	
	/**
	 * __invoke() should return false if file is outside range
	 */
	public function testInvokeReturnsFalseIfFileIsOutsideRange()
	{
        $service = new Validate(floor($this->size / 2), ceil($this->size / 2));
        
        $this->assertFalse($service($this->filename));
        
        return;
	}
	
	/**
	 * __invoke() should return false if file is above max
	 */
	public function testInvokeReturnsTrueIfMinAndMaxDoNotExist()
	{
        $service = new Validate();
        
        $this->assertTrue($service($this->filename));
        
        return;
	}
}
