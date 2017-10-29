<?php
namespace App\Contracts;


interface ParserContract
{
    public function parse($source) : \Generator;
}