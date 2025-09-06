<?php

    namespace MVCFrame\Tests\Classes;

    class MagicEightball {

        public static array $answers=[
            "It is certain.",
            "It is decidedly so.",
            "Without a doubt.",
            "Yes, definitely.",
            "You may rely on it.",
            "As I see it, yes.",
            "Most likely.",
            "Outlook good.",
            "Yes.",
            "Signs point to yes.",
            "Reply hazy, try again.",
            "Ask again later.",
            "Better not tell you now.",
            "Cannot predict now.",
            "Concentrate and ask again.",
            "Don't count on it.",
            "My reply is no.",
            "My sources say no.",
            "Outlook not so good.",
            "Very doubtful."
        ];

        public function shake(){
            $index = array_rand(static::$answers);
            $solution = static::$answers[$index];
            var_dump($solution);
            return $solution;
        }
    }

?>