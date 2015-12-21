<?php
namespace AppBundle\Controller;

use Imagine\Gd\Imagine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Cookie;
#use AppBundle\Weibo;
#use Imagine\Image\Box;
#use Imagine\Image\Point;
#use Imagine\Image\ImageInterface;
#use Symfony\Component\Filesystem\Filesystem;

#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="_index")
	 */
	public function indexAction(Request $request)
	{
		return $this->render('AppBundle:default:index.html.twig');
	}
	/**
	 * @Route("/types/{status}", name="_types")
	 */
	public function typesAction(Request $request, $status = null)
	{
		return $this->render('AppBundle:default:types.html.twig');
	}
	/**
	 * @Route("/measuring", name="_measuring")
	 */
	public function measuringAction(Request $request)
	{
        $type = $request->get('type') ? : 1;
        if( is_array($type))
            $type_id = $type[0];
        $result_type = $this->getDoctrine()->getRepository('AppBundle:Type')->find($type_id);
		$cities =  $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
		return $this->render('AppBundle:default:measuring.html.twig',array(
			'cities'=> $cities,
            'type'=> $result_type,
		));
	}
	/**
	 * @Route("/calculate", name="_calculate")
	 */
	public function calculateAction(Request $request)
	{
		$grade = $request->get('grade') ? : 1;
		$regional = $request->get('regional') ? : 1;
		$status = $request->get('status') ? : 1;
		$type = $request->get('type') ? : 1;
		$regional = $request->get('regional') ? : 1;
		$standard = $request->get('standard') ? : 1;
		$layer = $request->get('layer') ? : 1;
		$area = $request->get('area') ? : 1;
		$repository = $this->getDoctrine()->getRepository('AppBundle:Rate');
		$result = $repository->findOneBy(array('grade'=>$grade,
			'regional'=>$regional,
			'status'=>$status,
			'type'=>$type,
			'regional'=>$regional,
			'standard'=>$standard,
			'layer'=>$layer,
		));
        if( null == $result ){
            return new Response('参数不正确，无法计算');
        }
		$rate = $result->getRate();
		$data = '
				<div class="cInfo">
            <div class="ciLine">单房收益（人民币）：2234</div>
            <div class="ciLine">净资本化率：'.$rate.'%</div>
            <div class="ciLine">测算结果（人民币）：266,800,000</div>
            <div class="ciLine">单价（元/平方米）：15546</div>
            <div class="ciLine">评估时间：8/21/15</div>
            <div class="ciLine">敏感性分析：<font class="grayFont">（纵轴租金变化率/横轴收益率）</font></div>
        </div>
        <div class="cBr"></div>
        
        <div class="scrollBlock">
            <table>
                <tr>
                    <th width="100">&nbsp;</th>
                    <th>4.00%</th>
                    <th>4.50%</th>
                    <th>5.00%</th>
                    <th>5.50%</th>
                    <th>6.00%</th>
                </tr>
                <tr>
                    <td>10%</td>
                    <td>619,600,000</td>
                    <td>583,400,000</td>
                    <td>550,400,000</td>
                    <td>520,100,000</td>
                    <td>520,100,000</td>
                </tr>
                <tr>
                    <td>5%</td>
                    <td>619,600,000</td>
                    <td>583,400,000</td>
                    <td>550,400,000</td>
                    <td>520,100,000</td>
                    <td>520,100,000</td>
                </tr>
                <tr>
                    <td>0</td>
                    <td>619,600,000</td>
                    <td class="redTd">583,400,000</td>
                    <td>550,400,000</td>
                    <td>520,100,000</td>
                    <td>520,100,000</td>
                </tr>
                <tr>
                    <td>-5%</td>
                    <td>619,600,000</td>
                    <td>583,400,000</td>
                    <td>550,400,000</td>
                    <td>520,100,000</td>
                    <td>520,100,000</td>
                </tr>
                <tr>
                    <td>-10%</td>
                    <td>619,600,000</td>
                    <td>583,400,000</td>
                    <td>550,400,000</td>
                    <td>520,100,000</td>
                    <td>520,100,000</td>
                </tr>
            </table>
        </div>
        
        <div class="cBr"></div>
        <div class="selBtn2">
            <a href="javascript:void(0);" class="btnB">继续</a>
        </div>
        ';
    return new Response($data);
		//return $this->render('AppBundle:default:measuring.html.twig', array('data'=>$data));
	}
}
