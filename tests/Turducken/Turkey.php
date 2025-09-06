<?php

    namespace MVCFrame\Tests\Turducken;
    use MVCFrame\Tests\Interfaces\EngastrationInterface;
    use MVCFrame\Tests\Turducken\Duck;
    use MVCFrame\Tests\Turducken\Chicken;

    class Turkey implements EngastrationInterface {
        public ?Duck $duck;
        private ?string $chef;
        public function __construct(Duck $duck, string $chef="Julia Child"){
            $this->duck = $duck;
            $this->chef = $chef;
        }
        public function vocalize(): void{
            var_dump("Gobble Gobble!");
        }
        public function yesChef(){var_dump($this->chef);}
    }
?>