<?php

namespace Application\Controller;

/**
 * Description of LiuAlgorithm
 *
 * @author Kowalski Piotr
 */
class LiuAlgorithm 
{
    public static function solve($data)
    {    
        $currentTick = 0;
        $result = array();
        $tasksEnded = false;
        $machineSchedule = array();
        $gap = array();
        
        $findMinDeadline = function($task1, $task2)
        {
            if ($task1->Deadline > $task2->Deadline) return 1;
            return 0;
        };
        
        do
        {
            $availableTask = array();
            foreach($data as $task)
            {
                if ($task->Duration == 0) continue;
                if ($task->Arrival <= $currentTick && !$task->Ended)
                    $availableTask[] = $task;
            }
            
            if (array_key_exists(0, $availableTask))
            {
                usort($availableTask, $findMinDeadline);

                $currentTask = $availableTask[0];
                $currentTask->Start[] = $currentTick;
                $currentTask->End[]   = ++$currentTick; 

                if (--$currentTask->Duration == 0)
                    $currentTask->Ended = true;
                
                $gap[] = array_sum($machineSchedule);
                unset($machineSchedule);
                $machineSchedule[] = 0;
            } 
            else 
            {
                $currentTick++;
                $machineSchedule[] = 1;
            }
            
            foreach($data as $task)
            {
                if($task->Ended) $task->MergeTicks();
                else continue 2;
            }
            $tasksEnded = true; 
        }
        while (!$tasksEnded);
        
        $result['lmax'] = max($gap);
        $result['tasks'] = $data;
        $result['totalTime'] = $currentTick;
        
        return $result;
    }
}

?>
