<?php

namespace Application\Controller;

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
     */
    public static function solve($data)
    {    
        $currentTick     = 0;
        $result          = array();
        $tasksEnded      = false;
        $machineSchedule = array();
        $gap             = array();
        
        $findMinDeadline = function($task1, $task2)
        {
            return $task1->Deadline > $task2->Deadline;
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
                
                //lista zawierająca czasy przestojów między zadaniami
                $gap[] = array_sum($machineSchedule);
                unset($machineSchedule);
                $machineSchedule[] = 0;
            } 
            else 
            {
                $currentTick++;
                $machineSchedule[] = 1;
            }
            
            //jeżeli wykonano wszystkie zadania 
            //zakończono algorytm
            foreach($data as $task)
            {
                if($task->Ended) $task->MergeTicks();
                else continue 2;
            }
            $tasksEnded = true; 
        }
        while (!$tasksEnded);
        
        $result['lmax']      = max($gap);
        $result['tasks']     = $data;
        $result['totalTime'] = $currentTick;
        
        return $result;
    }
}

?>
