# validate-file-size
Validate a file's size.

```php
use Jstewmc\ValidateFileSize\Validate;

// set the file's name
$filename = '/path/to/foo.php';

// create a three-byte file
file_put_contents($filename, 'foo');

// check the file's size
(new Validate(0, 1))($filename);    // returns false
(new Validate(0, 999))($filename);  // returns true
```

Keep in mind, the _min_ and _max_ sizes are inclusive.

That's about it!

## Author

[Jack Clayton](mailto:clayjs0@gmail.com)

## License

[MIT](https://github.com/jstewmc/validate-file-size/blob/master/LICENSE)

## Version

### 0.1.0, August 24, 2017

* Initial release
