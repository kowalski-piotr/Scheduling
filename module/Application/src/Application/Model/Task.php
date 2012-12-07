<?php

namespace Application\Model;

class Task 
{
    static public $quantity;
    public $Start;
    public $End;
    public $Duration;
    public $Number;
    public $Deadline;   //czas ostatecznego terminu wykonania
    public $Arrival;    //czas przybycia/gotowości zadania
    public $Ended;      //bool
    
    public function __construct($duration, $arrival = null, $deadline = null) 
    {
        $this->Start = array();
        $this->End   = array();
        $this->Duration = $duration;
        $this->Number = ++Task::$quantity;
        
        $this->Arrival = $arrival;
        $this->Deadline = $deadline;
        
        if ($this->Duration == 0)
            $this->Ended = true;
        else
            $this->Ended = false;
    }
    
    public function MergeTicks()
    {
        $ticksCount = count($this->Start);
        for($i = 0 ; $i < $ticksCount ; $i++)
        {
            if ($i < count($this->Start)-1 && $this->End[$i] != null)
            {
                if ($this->End[$i] == $this->Start[$i+1])
                {    
                    $this->End[$i] = $this->End[$i+1];
                    unset($this->Start[$i+1]);
                    $this->Start = array_values($this->Start);
                    unset($this->End[$i+1]);
                    $this->End = array_values($this->End);
                    $i--;
                }
            }
        }
    }

}

?>
