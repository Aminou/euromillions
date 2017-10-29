<?php
namespace App;

use App\Contracts\ParserContract;

class CSVParser implements ParserContract
{
    protected $delimiter;
    private $headers = [];
    private $columnNumber;

    public function __construct($delimiter = ';')
    {
        $this->delimiter = $delimiter;
    }

    public function parse($source) : \Generator
    {
        try {

            $handle = fopen($source, 'r');

            $this->setHeaders(fgetcsv($handle, 0, $this->delimiter));

            while (false !== ($data = fgetcsv($handle, 0, $this->delimiter))) {
                yield $this->parseLine($data);
            }

            fclose($handle);

        } catch (\Exception $e) {
            return 'the file does not exists.';
        }
    }

    private function parseLine($line) : array
    {
        //fix to handle column differences in the dataset
        $this->fixColumnNumbers(count($line));

        return array_combine($this->headers, $line);
    }

    private function fixColumnNumbers($lineCount) : void
    {
        if ($this->columnNumber < $lineCount) {
            $this->headers[] = '';
            $this->columnNumber++;
        }

        if ($this->columnNumber > $lineCount) {
            array_pop($this->headers);
            $this->columnNumber--;
        }
    }

    private function setHeaders($firstLine) : void
    {
        $this->columnNumber = count($firstLine);
        $this->headers = $firstLine;
    }

}