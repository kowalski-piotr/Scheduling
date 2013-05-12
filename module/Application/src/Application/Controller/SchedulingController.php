<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\View\Helper\GanttiChart;
use Application\Model\Task;
use Application\Model\JohnsonsAlgorithm;
use Zend\View\Model\JsonModel;

class SchedulingController extends AbstractActionController 
{
    public function liuAction() 
    {
        if ($this->request->isPost()) {
            $order = '';

            $data = $this->request->getPost();
            $taskCount = count($data['tableData']);

            for ($i = 0; $i < $taskCount; $i++) {
                $duration = $data['tableData'][$i][0];
                $arrival = $data['tableData'][$i][1];
                $deadline = $data['tableData'][$i][2];

                $tasks[] = new Task($duration, $arrival, $deadline);
            }

            $solvedTasks = LiuAlgorithm::solve($tasks);
            $input = array(array());
            $input[0]['label'] = 'Machine 1';

            if ($solvedTasks['totalTime'] < 30)
                $ticks = 30;
            else
                $ticks = $solvedTasks['totalTime'];

            foreach ($solvedTasks['tasks'] as $task) {
                $color = '#' . dechex(rand(0, 10000000));
                $taskNumber = $task->Number;
                $order .= 'T' . $taskNumber . ' ';

                $piecesOfTask = count($task->Start);
                for ($i = 0; $i < $piecesOfTask; $i++) {
                    $piece = array(
                        'label' => 'T' . $taskNumber,
                        'start' => $task->Start[$i],
                        'end' => $task->End[$i],
                        'color' => $color);

                    $input[0][] = $piece;
                }
            }

            $gantti = new GanttiChart(array(
                'totalTime' => $ticks,
                    ), $input);

            $result = array(
                'totalTime' => $solvedTasks['totalTime'],
                'lmax' => $solvedTasks['lmax'],
                'gantti' => $gantti->render(),
            );

            return new JsonModel($result);
        }
    }

    public function johnsonAction() {
        if ($this->request->isPost()) {
            $order = '';
            $ticks = 30;
            $input = array(array());
            $data = $this->request->getPost();
            $machinesCount = $data['machines'];
            $taskCount = count($data['tableData']);

            if ($machinesCount != 2 && $machinesCount != 3)
                return false;

            for ($i = 0; $i < $taskCount; $i++)
                $tasks[] = new Task($data['tableData'][$i]);

            if (!$solvedTasks = JohnsonsAlgorithm::solve($tasks, $machinesCount))
                return false;

            if ($solvedTasks['totalTime'] > 30)
                $ticks = $solvedTasks['totalTime'];

            foreach ($solvedTasks['tasks'] as $task) {
                $color = '#' . dechex(rand(0, 10000000));
                $taskNumber = $task->Number;
                $order .= 'T' . $taskNumber . ' ';

                for ($i = 0; $i < $machinesCount; $i++) {
                    $numberOfMachine = $i + 1;
                    $input[$i]['label'] = 'Machine ' . $numberOfMachine;

                    $block = array(
                        'label' => 'T' . $taskNumber,
                        'start' => $task->Start[$i],
                        'end' => $task->End[$i],
                        'color' => $color);

                    array_push($input[$i], $block);
                }
            }

            $gantti = new GanttiChart(array(
                'title' => 'Flowshop',
                'totalTime' => $ticks,
                    ), $input);

            $result = array(
                'totalTime' => $solvedTasks['totalTime'],
                'order' => $order,
                'gantti' => $gantti->render(),
            );

            return new JsonModel($result);
        }

        return new ViewModel(array(
            'gantti' => new GanttiChart(),
        ));
    }

}
