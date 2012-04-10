<?php
/*
 *  $Id$  
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once 'phing/system/io/OutputStream.php';
require_once 'phing/system/io/PhingFile.php';

/**
 * Output stream subclass for file streams.
 * 
 * @package   phing.system.io
 */
class StringOutputStream extends OutputStream {
    
    /**
     * @var string The associated string, i.e. the buffer receiving the content written to the stream.
     */
    protected $buffer;
	
	protected $opencount;
    
    /**
     * Construct a new StringOutputStream.
     * @throws Exception - if invalid argument specified.
     * @throws IOException - if unable to open file.
     */
    public function __construct() {
		$this->buffer = "";
		$this->opencount = 1;
		
        $stream = @fopen("php://temp", "a+b");
        if ($stream === false) {
            throw new IOException("Unable to open php://temp for writing: " . $php_errormsg);
        }
        parent::__construct($stream);
    }
    
    
    /**
     * Closes attached stream, flushing output first.
     * @throws IOException if cannot close stream (note that attempting to close an already closed stream will not raise an IOException)
     * @return void
     */
    public function close() {
        if ($this->stream === null) {
            return;
        }
        $this->flush();
		
		// Read what we have written.
		rewind($this->stream);
		$this->buffer = stream_get_contents($this->stream);
		
		$this->opencount--;
		if ($this->opencount <= 0) {
			if (false === @fclose($this->stream)) {
				$msg = "Cannot close " . $this->getResource() . ": $php_errormsg";
				throw new IOException($msg);
			}
			$this->stream = null;
		}
    }
    
    /**
     * Returns a string representation of the attached PHP stream.
     * @return string
     */
    public function __toString() {
        return (string) $this->stream;
    }

    /**
     * Returns a content written to the stream 
     * @return string
     */
    public function getContent() {
        if ($this->stream) {
			$this->flush();
			
			// Read what we have written.
			rewind($this->stream);
			$this->buffer = stream_get_contents($this->stream);
		}
		
        return $this->buffer;
    }
}

