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
        $average_rent = $request->get('averageRent') ? : 1;
        $due_year = $request->get('dueYear') ? : date('Y');
        $due_month = $request->get('dueMonth') ? : date('m');
        $due_day = $request->get('dueDay') ? : date('d');
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
		$rate = $result->getRate()*0.01;
        $due_time = strtotime($due_year.'-'.$due_month.'-'.$due_day);
        $d = floor(($due_time-strtotime(date('Y-m-d')))/86400);

        if( 0 == $d ){
            return new Response('参数不正确，无法计算');
        }
        $amount_price = round($average_rent*$area*12/$rate*(1-1/pow((1+$rate),$d/365)),-5);
        $price = round($amount_price/$area,0);
        $rate2 = array(0.1,0.05,0,-0.05,-0.1);
        for ($i=0; $i < 5; $i++) { 
            $_rate[$i] = $rate + 0.005*($i - 2);
            //$_rate[$i] = $rate;
            for ($j=0; $j < 5; $j++) { 
                $data[$i][$j] = round($average_rent*$area*(1+$rate2[$j])*12/$_rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365)),-5);
                //$data[$i][$j] = round($average_rent*$area*(1+$rate2[$j])*12/$rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365)),-5);
            }
        }
		
        //return new Response($data);
		return $this->render('AppBundle:default:calculate.html.twig', array(
            'due_time'=>$due_time,
            'amount_price'=>$amount_price,
            'price'=>$price,
            'rate'=>$rate,
            'data'=>$data,
            'rate2'=>$rate2,
        ));
	}
}
