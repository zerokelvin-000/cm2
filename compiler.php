<?php
    class Compiler{
        private array $instructions = [
            "NOP", // salta l'istruzione
            "MOV", // copia register1 in register2 (params => Rx, Ry)
            "MOVI", // carica valore di 8 bit in un register (params => n, Rx)
            "LOAD", // carica in un register il valore della RAM all'indirizzo A (params => Rx, A)
            "STORE", // salva il valore della RAM all'indirizzo A in un register (params => Rx, A)
            "ADD", // somma register1 e register2, salva in register1 (params => Rx, Ry)
            "ADDI", // somma il valore di 8 bit a un register, salva in register1 (params => n, Rx)
            "SUB", // sottrare register2 da register1, salva in register1 (params => Rx, Ry)
            "SUBI", // sottrare il valore di 8 bit da un register, salva in register1 (params => n, Rx)
            "AND", // esegue AND bitwise di register 1 e 2 (params => Rx, Ry)
            "OR", // esegue OR bitwise di register 1 e 2 (params => Rx, Ry)
            "XOR", // esegue XOR bitwise di register 1 e 2 (params => Rx, Ry)
            "NOT", // esegue NOT bitwise di register 1 e 2 (params => Rx, Ry)
            "JMP", // salta all'indirizzo della ROM A di 8 bit (params => n)
            "JMPZ", // salta all'indirizzo della ROM A se il flag Z è attivo (params => n)
            "HALT" // ferma l'esecuzione del programma
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

        private function int2bit($input){
            return decbin($input);
        }

        private function value_id($array, $search_value){
            foreach($array as $key => $value){
                if($value == $search_value){
                    return $key;
                }
            }
            return $value;
        }

        private function adjust_bit_value(int $target_length, $value){
            $value_length = count(str_split($value));
            $reversed_string = strrev($value); // in binario io conto da sinistra a destra
            $difference = $target_length - $value_length;

            if($difference < 0){
                return null; // se volessimo dire che vogliamo 3 caratteri dal valore 1011101 perderemmo dati
            }

            for($x = 0; $x < $difference; $x++){
                $reversed_string .= "0";
            }

            return $reversed_string;
        }

        private function manage_arguments($instruction, $arguments){

        }

        public function compile(){
            $output = "";
            $errors = [];

            foreach($this->user_instructions as $row => $instruction){
                $instruction_arguments = explode(" ", $instruction);

                if(!in_array($instruction_arguments[0], $this->instructions)){
                    array_push($errors, $row+1 .": '{$instruction_arguments[0]}' non è un istruzione.");
                    continue;
                }
                
                $opcode = $this->value_id($this->instructions, $instruction_arguments[0]);
                $opcode = $this->int2bit($opcode);
                $opcode = $this->adjust_bit_value(4, $opcode);

                $params = "";

                if(count($instruction_arguments) > 1){
                    $argument_array = [];

                    for($x = 0; $x < count($instruction_arguments) - 1; $x++){
                        array_push($argument_array, $instruction_arguments[$x]);
                    }

                    // TODO $params = $this->manage_arguments($instruction, $argument_array);
                }

                $output .= "{$opcode}<br>";
            }

            if($errors!=[]){
                return "{$output}<br><br>Errori trovati nelle righe:<br>".$this->pretty_array($errors);
            }

            return "{$output}<br><br>Nessun errore trovato.";
        }
    }

    $compiler = new Compiler([
        "MOV r1 r2",
    ]);

    echo $compiler->compile();