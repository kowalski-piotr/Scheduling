<?php

namespace Application\View\Helper;

class GanttiChart {

  function __construct($params=array(), $data = null) {
    
    $defaults = array(
      'title'      => 'chart',
      'cellwidth'  => 25,
      'cellheight' => 35,
      'totalTime'  => 30,
    );
    
    $this->options = array_merge($defaults, $params);    
    $this->data    = $data == null ? array() : $data;
    $this->cellstyle = 'style="width: ' . $this->options['cellwidth'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    
    $this->blocks = $this->data;
    $this->first = 1;
    $this->last  = $this->options['totalTime'];

  }

  function render() 
  {
    $html = array();
    
    // common styles    
    $cellstyle  = 'style="line-height: ' . $this->options['cellheight'] . 'px; height: ' . $this->options['cellheight'] . 'px"';
    $wrapstyle  = 'style="width: ' . $this->options['cellwidth'] . 'px"';
    $totalstyle = 'style="width: ' . ($this->last*$this->options['cellwidth']) . 'px"';
    // start the diagram    
    $html[] = '<figure class="gantt" style="width: 960px; margin-left: auto;margin-right: auto;">';    

    // set a title if available
    if($this->options['title']) {
      $html[] = '<figcaption>' . $this->options['title'] . '</figcaption>';
    }

    // sidebar with labels
    $html[] = '<aside>';
    $html[] = '<ul class="gantt-labels" style="margin-top: ' . (($this->options['cellheight']*2)+1) . 'px">';
    foreach($this->blocks as $i => $block) {
      $html[] = '<li class="gantt-label"><strong ' . $cellstyle . '>' . $block['label'] . '</strong></li>';      
    }
    $html[] = '</ul>';
    $html[] = '</aside>';

    // data section
    $html[] = '<section class="gantt-data">';
        
    // data header section
    $html[] = '<header>';

    // months headers
    $html[] = '<ul class="gantt-months" ' . $totalstyle . '>';

    $html[] = '<li class="gantt-month" style="width: ' . ($this->options['cellwidth'] * $this->last) . 'px"><strong ' . $cellstyle . '>' . 'Ticks' . '</strong></li>';
                      
    $html[] = '</ul>';    

    // days headers
    $html[] = '<ul class="gantt-days" ' . $totalstyle . '>';
    for ($i = 1; $i <= $this->last; $i++) {

      $weekend = '';
      $today   = ' today';

      $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '>' . $i . '</span></li>';
    }                      
    $html[] = '</ul>';    
    
    // end header
    $html[] = '</header>';

    // main items
    $html[] = '<ul class="gantt-items" ' . $totalstyle . '>';
        
    foreach($this->blocks as $i => $block) {
      
      $html[] = '<li class="gantt-item">';
      
      // days
      $html[] = '<ul class="gantt-days">';
      for ($i = 1; $i <= $this->last; $i++) {

        $weekend = '';
        $today   = ' today' ;

        $html[] = '<li class="gantt-day' . $weekend . $today . '" ' . $wrapstyle . '><span ' . $cellstyle . '>' . $i . '</span></li>';
      }                      
      $html[] = '</ul>';    

      foreach($block as $key => $item)
      {
        if($key === 'label') continue;
      // the block
        $days   = $item['end'] - $item['start'] ; 
        $offset = $item['start'] ;
        $top    = round($i * ($this->options['cellheight'] + 1));
        $left   = round($offset * $this->options['cellwidth']);
        $width  = round($days * $this->options['cellwidth'] - 9);
        $height = round($this->options['cellheight']-8);
        $color  = $item['color'];
        $html[] = '<span class="gantt-block" style="background: '.$color.'; left: ' . $left . 'px; width: ' . $width . 'px; height: ' . $height . 'px"><strong class="gantt-block-label">' . $item['label'] . '</strong></span>';
      }
      $html[] = '</li>';
    
    }
    
    $html[] = '</ul>';    
    
    // end data section
    $html[] = '</section>';    

    // end diagram
    $html[] = '</figure>';

    return implode('', $html);
      
  }
  
  function __toString() {
    return $this->render();
  }

}
