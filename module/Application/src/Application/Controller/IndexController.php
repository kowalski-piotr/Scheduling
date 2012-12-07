<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\GanttiChart;
use Application\Model\Task;
use Zend\View\Model\JsonModel;


class IndexController extends AbstractActionController
{
    public function solveJohnsonAction()
    {
        $order = '';
        
        if ($this->request->isPost()) 
        {
            $ticks = 30;
            $data = $this->request->getPost();
            $machinesCount = $data['machines'];
            $taskCount = count($data['tableData']);

            for($i = 0 ; $i < $taskCount ; $i++)
                $tasks[] = new Task($data['tableData'][$i]);
            
            $solvedTasks = JohnsonsAlgorithm::solve($tasks);
            $input = array(array());
            
            if ($solvedTasks['totalTime'] > 30)
                $ticks = $solvedTasks['totalTime'];


            foreach($solvedTasks['tasks'] as $task)
            {
                $color = '#' . dechex(rand(0,10000000));
                $taskNumber = $task->Number;
                $order .= 'T'.$taskNumber.' ';
                
                for ($i = 0 ; $i < $machinesCount ; $i++)
                { 
                    $numberOfMachine = $i + 1;
                    $input[$i]['label'] = 'Machine '. $numberOfMachine;
                    
                    $block = array(
                        'label' => 'T'.$taskNumber,
                        'start' => $task->Start[$i],
                        'end'   => $task->End[$i],
                        'color' => $color);
                                        
                    array_push($input[$i], $block);  
                }
            }
            
            $gantti = new GanttiChart(array(
                'title'      => 'Flowshop',
                'cellwidth'  => 25,
                'cellheight' => 35,
                'totalTime'  => $ticks,
                ),$input);

            $result = array(
                'totalTime' => $solvedTasks['totalTime'],
                'order'     => $order,
                'gantti'    => $gantti->render(),
                );

            return new JsonModel($result);
        }
    }
    
    public function solveLiuAction()
    {
        $order = '';
        
        if ($this->request->isPost()) 
        {
            $data = $this->request->getPost();
            $taskCount = count($data['tableData']);

            for($i = 0 ; $i < $taskCount ; $i++)
            {
                $duration   = $data['tableData'][$i][0];
                $arrival    = $data['tableData'][$i][1];
                $deadline   = $data['tableData'][$i][2];
                
                $tasks[] = new Task($duration,$arrival,$deadline);
            } 
            
            $solvedTasks = LiuAlgorithm::solve($tasks);
            $input = array(array());
            $input[0]['label'] = 'Machine 1';
            
            if ($solvedTasks['totalTime'] < 30)
                $ticks = 30;
            else $ticks = $solvedTasks['totalTime'];
            
            foreach($solvedTasks['tasks'] as $task)
            {
                $color = '#' . dechex(rand(0,10000000));
                $taskNumber = $task->Number;
                $order .= 'T'.$taskNumber.' ';

                $piecesOfTask = count($task->Start);
                for ($i = 0 ; $i < $piecesOfTask ; $i++)
                { 
                    $piece = array(
                        'label' => 'T'.$taskNumber,
                        'start' => $task->Start[$i],
                        'end'   => $task->End[$i],
                        'color' => $color);
                                        
                    $input[0][] = $piece ; 
                }
            }
            
            $gantti = new GanttiChart(array(
                'totalTime'  => $ticks,
                ),$input);

            $result = array(
                'totalTime' => $solvedTasks['totalTime'],
                'lmax'      => $solvedTasks['lmax'],
                'gantti'    => $gantti->render(),
                );

            return new JsonModel($result);
        }
    }
    
    public function indexAction()
    { 
        return new ViewModel(array(
            'gantti' => new GanttiChart(),
            ));
    }
    
    public function johnsonAction()
    {
        return new ViewModel(array(
            'gantti' => new GanttiChart(),
            ));
    }
    
    public function liuAction()
    {       
        return new ViewModel(array(
            'gantti' => new GanttiChart(),
            ));
    }  
}
