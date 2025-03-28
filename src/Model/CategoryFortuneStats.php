<?php

namespace App\Model;

class CategoryFortuneStats
{
    public function __construct(
        public $fortunesPrinted,
        public $fortunesAverage,
        public $name
    ) {
    }
}
