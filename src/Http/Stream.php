<?php
declare(strict_types=1);

// Next lines are required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.InvalidNoReturn
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace Stancer\Http;

use Stancer;
use Psr;

/**
 * Basic HTTP Stream.
 */
class Stream implements Psr\Http\Message\StreamInterface
{
    /**
     * @var string Stream content.
     */
    protected $content = '';

    /**
     * @var integer Total length.
     */
    protected $size = 0;

    /**
     * @var integer Actual position.
     */
    protected $position = 0;

    /**
     * @param string $content Content.
     */
    public function __construct(string $content)
    {
        $this->content = $content;
        $this->size = strlen($content);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString(): string
    {
        return $this->rewind()->getContents();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any.
     */
    public function detach()
    {
        return null;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return boolean
     */
    public function eof(): bool
    {
        return $this->position === $this->size;
    }

    /**
     * Returns the remaining contents in a string.
     *
     * @return string
     */
    public function getContents(): string
    {
        if (!$this->tell()) {
            return $this->content;
        }

        return $this->read($this->size);
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string|null $key Specific metadata to retrieve.
     * @return array|null Returns an associative array if no key is
     *   provided. Returns a specific key value if a key is provided and the
     *   value is found, or null if the key is not found.
     *
     * @phpstan-return array{}|null
     */
    public function getMetadata($key = null): ?array
    {
        if ($key) {
            return null;
        }

        return [];
    }

    /**
     * Get the size of the stream if known.
     *
     * @return integer|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return boolean
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return boolean
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return boolean
     */
    public function isWritable(): bool
    {
        return false;
    }

    /**
     * Read data from the stream.
     *
     * @param integer $length Read up to $length bytes from the object and return
     *   them. Fewer than $length bytes may be returned if underlying stream
     *   call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *   if no bytes are available.
     */
    public function read($length): string
    {
        if (!$length) {
            return '';
        }

        $content = substr($this->content, $this->position, $length);

        $this->seek($length, SEEK_CUR);

        return $content;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @return $this
     */
    public function rewind(): self
    {
        return $this->seek(0);
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param integer $offset Stream offset.
     * @param integer $whence Specifies how the cursor position will be calculated
     *   based on the seek offset. Valid values are identical to the built-in
     *   PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *   offset bytes SEEK_CUR: Set position to current location plus offset
     *   SEEK_END: Set position to end-of-stream plus offset.
     * @return $this
     */
    public function seek($offset, $whence = SEEK_SET): self
    {
        if ($whence === SEEK_CUR) {
            $this->position += $offset;
        }

        if ($whence === SEEK_END) {
            $this->position = $this->size + $offset;
        }

        if ($whence === SEEK_SET) {
            $this->position = $offset;
        }

        if ($this->position < 0) {
            $this->position = 0;
        }

        if ($this->position > $this->size) {
            $this->position = $this->size;
        }

        return $this;
    }

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @return integer Position of the file pointer.
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return integer Returns the number of bytes written to the stream.
     * @throws Stancer\Exceptions\BadMethodCallException For every call.
     */
    public function write($string): int
    {
        throw new Stancer\Exceptions\BadMethodCallException('This method is not implemented.');
    }
}
