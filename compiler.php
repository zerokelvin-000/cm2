<?php
    class Compiler{
        private array $instructions = [
            "NOP",
            "MOV",
            "MOVI",
            "LOAD",
            "STORE",
            "ADD",
            "ADDI",
            "SUB",
            "SUBI",
            "AND",
            "OR",
            "XOR",
            "NOT",
            "JMP",
            "JZ",
            "HALT"
        ];
        private array $user_instructions;

        public function __construct($instructions){
            $this->user_instructions = $instructions;
        }

        private function pretty_array(array $input){
            $output = "";

            foreach($input as $element){
                $output .= "{$element}<br>";
            }

            return $output;
        }

        public function compile(){
            $output = "";
            $errors = [];

            foreach($this->user_instructions as $row => $instruction){
                if(!in_array($instruction, $this->instructions)){
                    array_push($errors, $row + 1);
                }

                $output .= "{$instruction}<br>";
            }

            return "{$output}<br><br>".$errors=[]?"Nessun errore trovato.":"Errori trovati nelle righe:<br>".$this->pretty_array($errors);
        }
    }

    $compiler = new Compiler([
        "SET r0 5",
        "MOVI 6",
    ]);

    echo $compiler->compile();