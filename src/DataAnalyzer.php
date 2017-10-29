<?php
namespace App;

use App\Contracts\ParserContract;
use Illuminate\Support\Collection;

class DataAnalyzer
{
    protected $filter;
    protected static $keys = [
        'boule',
        'etoile'
    ];

    const NATURAL_SORTING = 1;
    const ASC_SORTING = 2;
    const DESC_SORTING = 3;

    public function __construct(ParserContract $parser, $dataSources)
    {
        $this->filter = new DataFilter($parser, $dataSources);
    }

    public function data() : Collection
    {
        return $this->filter->getData();
    }

    public function stars() : Collection
    {
        return $this->filter->getStarData();
    }

    public function numbers() : Collection
    {
        return $this->filter->getNumberData();
    }

    public function totalDraws() : int
    {
        return $this->filter->getData()->count();
    }

    public function getOccurence(array $data, $sorting = self::NATURAL_SORTING) : array
    {
        $occurence = [];

        foreach($data as $number) {
            $occurence[$number] = isset($occurence[$number]) ? ++$occurence[$number] : 1;
        }

        if ($sorting === self::NATURAL_SORTING) {
            ksort($occurence);
        }

        if ($sorting === self::ASC_SORTING) {
            asort($occurence);
        }

        if ($sorting === self::DESC_SORTING) {
            arsort($occurence);
        }

        return $occurence;
    }

    public function getMinStar()
    {
        return $this->getStar(self::ASC_SORTING);
    }

    public function getMaxStar()
    {
        return $this->getStar(self::DESC_SORTING);
    }

    public function getLowestStars()
    {
        return $this->getStars(self::ASC_SORTING);
    }

    public function getHighestStars()
    {
        return $this->getStars(self::DESC_SORTING);
    }

    public function getMinNumber()
    {
        return $this->getNumber(self::ASC_SORTING);
    }

    public function getMaxNumber()
    {
        return $this->getNumber(self::DESC_SORTING);
    }

    public function getLowestNumbers()
    {
        return $this->getNumbers(self::ASC_SORTING);
    }

    public function getHighestNumbers()
    {
        return $this->getNumbers(self::DESC_SORTING);
    }

    public function getNumberOccurence($number = 1)
    {
        if ($number > 50 || $number < 1) {
            return 0;
        }

        return $this->getOccurence($this->numbers()->toArray())[$number];
    }

    private function printNumbers($numbers)
    {
        return implode(' ', array_keys($numbers));
    }

    public function printHighestStars()
    {
        return $this->printNumbers($this->getHighestStars());
    }

    public function printLowestStars()
    {
        return $this->printNumbers($this->getLowestStars());
    }

    public function printHighestNumbers()
    {
        return $this->printNumbers($this->getHighestNumbers());
    }

    public function printLowestNumbers()
    {
        return $this->printNumbers($this->getLowestNumbers());
    }

    private function getNumber($sorting)
    {
        $collection = $this->getOccurence($this->numbers()->toArray(), $sorting);

        return key($collection);
    }

    private function getNumbers($sorting, $amount = 5)
    {
        $data = $this->getOccurence($this->numbers()->toArray(), $sorting);

        return array_slice($data, 0, $amount, true);
    }

    private function getStar($sorting)
    {
        $data = $this->getOccurence($this->stars()->toArray(), $sorting);

        return key($data);
    }

    private function getStars($sorting, $amount = 2)
    {
        $data = $this->getOccurence($this->stars()->toArray(), $sorting);

        return array_slice($data, 0, $amount, true);
    }

}