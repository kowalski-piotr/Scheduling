<?php

namespace Application\Model;

/**
 * Liu Algorithm
 *
 * @author Kowalski Piotr
 */
class LiuAlgorithm 
{
    /**
     * Implementacja algorytmu Liu,
     * kryterium uszeregowania Lmax
     * 
     * 
     */
    public static function solve($data)
    {    
        $currentTick     = 0;
        $result          = array();
        $tasksEnded      = false;
        $lmax            = "";
        
        $findMinDeadline = function($task1, $task2)
        {
            return $task1->Deadline > $task2->Deadline;
        };
        
        $findLmax = function($task1, $task2)
        {
            return $task1->Delay < $task2->Delay;
        };
        
        do
        {
            $availableTask = array();
            foreach($data as $task)
            {
                if ($task->Duration == 0) continue;
                
                //szukanie zadań które w danym cyklu (ticku) mogą zostać wykonane
                if ($task->Arrival <= $currentTick && !$task->Ended)
                    $availableTask[] = $task;
            }
            
            if (array_key_exists(0, $availableTask))
            {
                //spośród dostępnych zadań szukane 
                //jest to z najbliższym deadline'em
                usort($availableTask, $findMinDeadline);

                $currentTask          = $availableTask[0];
                $currentTask->Start[] = $currentTick;
                $currentTask->End[]   = ++$currentTick; 
                  
                if (--$currentTask->Duration == 0)
                    $currentTask->Ended = true;
            } 
            else $currentTick++;

            
            //jeżeli wykonano wszystkie zadania 
            //zakończono algorytm
            foreach($data as $task)
            {
                if($task->Ended) 
                    $task->MergeTicks();
                else 
                    continue 2;
            }
            $tasksEnded = true; 
        }
        while (!$tasksEnded);
        
        //wyszukiwanie Lmax
        foreach($data as $task)
        {
            $delay = $task->Deadline-end($task->End) ;
            if($delay < 0)
                $task->Delay = -$delay;
        }
        
        $lmaxTaskList = $data;
        usort($lmaxTaskList, $findLmax);
        $lmax .= "T" . $lmaxTaskList[0]->Number . 
                 " - Delay : " . $lmaxTaskList[0]->Delay;
        
        $result['lmax']      = $lmax;
        $result['tasks']     = $data;
        $result['totalTime'] = $currentTick;
        
        return $result;
    }
}

?>
