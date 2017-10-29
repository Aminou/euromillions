<?php

namespace Tests\Unit;


use Illuminate\Support\Collection;
use App\DataFilter;
use App\CSVParser;
use App\DataAnalyzer;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    protected $parser;

    private function getFiles()
    {
        return [
            'data/euromillions.csv',
            'data/euromillions_2.csv',
            'data/euromillions_3.csv',
        ];
    }

    private function getParser()
    {
        return new DataFilter(new CSVParser, $this->getFiles());
    }

    private function getAnalyser()
    {
        return new DataAnalyzer(new CSVParser, $this->getFiles());
    }

    /** @test **/
    public function it_tests_the_integrity_of_the_data()
    {
        $data = $this->getParser()->getData();
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->count() > 0);
    }

    /** @test **/
    public function it_test_if_number_is_the_lowest()
    {
        $analyser = $this->getAnalyser();

        $min = $analyser->getMinNumber();
        $max = $analyser->getMaxNumber();


        $this->assertTrue($min < $max);
    }

    /** @test **/
    public function it_test_that_we_receive_the_highest_five_numbers()
    {
        $maxNumbers = $this->getAnalyser()->getHighestNumbers();

        $this->assertCount(5, $maxNumbers);

    }

    /** @test **/
    public function it_test_that_we_receive_the_lowest_five_numbers()
    {
        $minNumbers = $this->getAnalyser()->getLowestNumbers();

        $this->assertCount(5, $minNumbers);

    }


    /** @test **/
    public function it_test_that_we_receive_a_string_of_the_highest_numbers()
    {
        $numbers = $this->getAnalyser()->printHighestNumbers();

        $this->assertNotEmpty($numbers);
        $this->assertTrue(is_string($numbers));

    }

    /** @test **/
    public function it_test_that_we_receive_a_string_of_the_lowest_numbers()
    {
        $numbers = $this->getAnalyser()->printLowestNumbers();

        $this->assertNotEmpty($numbers);
        $this->assertTrue(is_string($numbers));

    }
}