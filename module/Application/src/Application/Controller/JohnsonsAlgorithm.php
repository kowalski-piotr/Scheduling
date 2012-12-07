<?php

namespace Application\Controller;
use Application\Model\Task;

/**
 * Description of JohnsonsAlgorithm
 *
 * @author Kowalski Piotr
 */
class JohnsonsAlgorithm 
{
    public static function solve($data)
    {          
        if (($sortedTasks = self::TaskSort($data)) == 0) return 0;
        
        $currentTick = 0;
        $m1 = 0; $m2 = 1; $m3 = 2;
        $tasksCount = count($sortedTasks);
        $thirdMachineExists = array_key_exists(2, $sortedTasks[0]->Duration);
        
        for ($i = 0 ; $i < $tasksCount ; $i++)
        {
            $currentTask = $sortedTasks[$i];
            
            if ($i > 0) 
                $previusTask = $sortedTasks[$i - 1];

            $currentTask->Start[$m1] = $currentTick;
            $currentTick += $currentTask->Duration[$m1];
            $currentTask->End[$m1] = $currentTick;

            //Jeżeli czas poprzednio zakończonego zadania na M2 > od czasu aktualnie zakończonego na M1
            if ($i > 0 && $previusTask->End[$m2] > $currentTask->End[$m1]) 
                $currentTask->Start[$m2] = $previusTask->End[$m2];
            else
                $currentTask->Start[$m2] = $currentTask->End[$m1];

            $currentTask->End[$m2] = $currentTask->Start[$m2] + $currentTask->Duration[$m2];

            if ($i == $tasksCount - 1) $result['totalTime'] = $currentTask->End[$m2];
        }
        
        if ($thirdMachineExists)
        {
            for ($i = 0 ; $i < $tasksCount ; $i++)
            {
                $currentTask = $sortedTasks[$i];
                
                if ($i == 0)
                    $currentTask->Start[$m3] = $currentTask->End[$m2];
                
                if ($i > 0) 
                    $previusTask = $sortedTasks[$i - 1];
                
                //Jeżeli czas poprzednio zakończonego zadania na M3 > od czasu aktualnie zakończonego na M2
                if ($i > 0 && $previusTask->End[$m3] > $currentTask->End[$m2]) 
                    $currentTask->Start[$m3] = $previusTask->End[$m3];
                else
                    $currentTask->Start[$m3] = $currentTask->End[$m2];

                $currentTask->End[$m3] = $currentTask->Start[$m3] + $currentTask->Duration[$m3];

                if ($i == $tasksCount - 1) $result['totalTime'] = $currentTask->End[$m3];
            }
        }
        
        $result['tasks'] = $sortedTasks;
        return $result;
    }
        
    private static function TaskSort($tasks)
    {
        $taskList1 = array();
        $taskList2 = array();
        $toCompareTask = null;
        foreach ($tasks as $task)
        {
            $quantityOfMachines = count($task->Duration);

            if ($quantityOfMachines == 3)
            {
                //sprowadzamy do problemu 2 maszynowego ustalając czasy zadania wg: T1' = T1+T2, T2' = T2+T3
                $d1 = $task->Duration[0] + $task->Duration[1];
                $d2 = $task->Duration[1] + $task->Duration[2];

                $toCompareTask = new Task(array($d1, $d2));
                $toCompareTask->Number = $task->Number;
            }
            elseif ($quantityOfMachines == 2)
            {
                $toCompareTask = $task;
            }
            else return 0;

            if($toCompareTask->Duration[1] >= $toCompareTask->Duration[0])
                $taskList1[] = $toCompareTask;
            else $taskList2[] = $toCompareTask;
        }

        $sortTime1 = function($task1, $task2)
        {
            if ($task1->Duration[0] > $task2->Duration[0]) return 1;
            return 0;
        };
        $sortTime2 = function($task1, $task2)
        {
            if ($task1->Duration[1] < $task2->Duration[1]) return 1;
            return 0;
        };
        usort($taskList1, $sortTime1);
        usort($taskList2, $sortTime2);

        $sorted = array_merge($taskList1,$taskList2);

        foreach($sorted as $sortedTask)
        {
            foreach($tasks as $task)
            {
                if ($sortedTask->Number == $task->Number)
                {
                    $result[] = $task;
                }
            }
        }

        return $result;
    }
}

?>
