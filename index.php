<?php
    class Block{
        private int $cube_type;
        private array $position;
        private array $connections;
        private array $more;
        private string $result;

        private int $cube_type_bits = 7;
        private int $position_bits = 16;
        private int $connections_bits = 16;
        private int $more_bits = 8;

        public function __construct(int $cube_type, array $position, array $connections, array $more = [0]){
            $this->cube_type = $cube_type;
            $this->position = $position;
            $this->connections = $connections;
            $this->more = $more;
        }

        private function int_to_binary($value){
            return decbin($value);
        }

        private function variable_length($value){
            return count(str_split($value));
        }

        private function int_to_binary_length($value){
            return $this->variable_length($this->int_to_binary($value));
        }

        private function declare_position_values(){
            $position_count = count($this->position);

            if($position_count != 3){
                die("Position array must have only three values ({$position_count} detected).");
            }

            $this->result = "";

            foreach($this->position as $key => $position_value){
                if(!is_int($position_value)){
                    die("Trying to use a non-integer value to indicate the position.");
                }

                $binary_position = $this->int_to_binary($position_value);
                $variable_length = $this->variable_length($binary_position);

                $this->result .= $variable_length . ($key == 2 ? null : ",");
            }
        }

        private function add_cube_type(){
            $this->result .= ";";
            $this->result .= "{$this->cube_type}:";
        }

        private function add_positions(){
            foreach($this->position as $key => $position_value){
                if(!is_int($position_value)){
                    die("Trying to use a non-integer value to indicate the position.");
                }

                if($position_value < 0 || $this->int_to_binary_length($position_value) > $this->position_bits){
                    $range_max = pow(2,$this->position_bits);
                    die("Position out of range 0 to {$range_max}.");
                }

                $this->result .= $position_value . ($key == 2 ? null : ",");
            }
        }

        private function add_connections(){
            if(empty($this->connections)){
                return $this->result;
            }

            $this->result .= "|";

            foreach($this->connections as $key => $connection){
                if(!is_int($connection)){
                    die("Trying to use a non-integer value to indicate a cube ID.");
                }

                $this->result .= $connection . ($key == count($this->connections) - 1 ? null : ",");
            }
        }

        private function add_more_info(){
            $this->result .= "&";

            foreach($this->more as $key => $more_info){
                $this->result .= $more_info . ($key == count($this->more) - 1 ? null : ",");
            }
        }

        public function get_representation(){
            $this->declare_position_values();
            $this->add_cube_type();
            $this->add_positions();

            if(empty($this->connections)){
                $this->add_more_info();
                return $this->result;
            }

            $this->add_connections();
            $this->add_more_info();

            return $this->result;
        }

        private static function interpretate_output($output){
            $result = $output;

            $result = explode("|", $result);
            $static_info = $result[0];
            $dynamic_info = $result[1];

            $position_binary_length = explode(";", $static_info)[0];
            $position_binary_length = explode(",", $position_binary_length);

            $cube_type = explode(";", $static_info)[1];
            $cube_type = explode(":", $cube_type)[0];

            $position = explode(";", $static_info)[1];
            $position = explode(":", $position)[1];

            $connections = explode("&", $dynamic_info)[0];
            $more_info = explode("&", $dynamic_info)[1];

            $output = [];

            array_push($output, $position_binary_length, $cube_type, $position, $connections, $more_info);

            return $output;
        }

        public static function get_bits_representation($output, $readable = false){
            $result = Block::interpretate_output($output);
            $output = "";


            foreach($result as $key => $element){
                if(!is_array($element)){
                    $output .= decbin(ord($element)) . ($readable ? " " : null);
                    continue;
                }

                foreach($element as $key => $character){
                    $output .= decbin(ord($character)) . ($readable ? " " : null);
                }
            }

            return $output;
        }
    }

    $block = new Block(1, [4,4,18], [1], [1]);
    //echo $block->get_representation();

    echo $block::get_bits_representation($block->get_representation(), true);

    //var_dump($block->interpretate_output());