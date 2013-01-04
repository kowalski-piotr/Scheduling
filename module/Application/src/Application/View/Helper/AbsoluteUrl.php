<?php
// ./module/Application/src/Application/View/Helper/AbsoluteUrl.php
namespace Application\View\Helper;
 
use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
 
class AbsoluteUrl extends AbstractHelper
{
    protected $request;
 
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
 
    public function __invoke()
    {
        //return 
        $url = $this->request->getUri()->normalize()->toString();
        $result= rtrim($url, 'a..z.'); 
        return $result;
    }
}
?>
