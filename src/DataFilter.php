<?php
namespace App;

use App\Contracts\ParserContract;
use Illuminate\Support\Collection;

class DataFilter
{
    protected $parser;
    protected $sources;

    protected static $boules = [
        'boule_1',
        'boule_2',
        'boule_3',
        'boule_4',
        'boule_5'
    ];

    protected static $etoile = [
        'etoile_1',
        'etoile_2'
    ];

    protected static $dates = [
        'date_de_tirage'
    ];

    public function __construct(ParserContract $parser, array $sources)
    {
        $this->parser = $parser;
        $this->sources = $sources;
    }

    private function parse(callable $filter) : Collection
    {
        $data = [];

        foreach($this->sources as $source) {

            $generator = $this->parser->parse($source);

            while ($generator->valid()) {
                $data[] = $this->filter($generator->current(), $filter);
                $generator->next();
            }
        }

        return collect($data);
    }

    private function filter($data, callable $closure) : array
    {
        return array_filter($data, $closure, ARRAY_FILTER_USE_KEY);
    }

    protected function filterNumber() : callable
    {
        return $this->callableFilter(self::$boules);
    }

    protected function filterStars() : callable
    {
        return $this->callableFilter(self::$etoile);
    }

    protected function filterBoth() : callable
    {
        return $this->callableFilter(array_merge(self::$boules, self::$etoile));
    }

    protected function filterNumberWithDates() : callable
    {
        return $this->callableFilter(array_merge(self::$boules, self::$dates));
    }

    protected function filterStarsWithDates() : callable
    {
        return $this->callableFilter(array_merge(self::$etoile, self::$dates));
    }

    private function callableFilter($filter) : callable
    {
        return function($key) use ($filter) {
            return in_array($key, $filter);
        };
    }

    public function getData() : Collection
    {
        return $this->data($this->filterBoth());
    }

    public function getStarData() : Collection
    {
        return $this->data($this->filterStars());
    }

    public function getNumberData() : Collection
    {
        return $this->data($this->filterNumber());
    }

    public function getDateAndNumber() : Collection
    {
        return $this->parse($this->filterNumberWithDates());
    }

    public function getDateAndStars() : Collection
    {
        return $this->parse($this->filterStarsWithDates());
    }

    private function data(callable $filter) : Collection
    {
        return $this->parse($filter)->flatten();
    }

}
