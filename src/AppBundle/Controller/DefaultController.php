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
		$session = $request->getSession();
		$session->set('type',null);
		return $this->render('AppBundle:default:types.html.twig');
	}
	/**
	 * @Route("/measuring", name="_measuring")
	 */
	public function measuringAction(Request $request)
	{
		$session = $request->getSession();
		if( null != $request->get('type')){
			$session->set('type', $request->get('type'));
		}
		if( null == $session->get('type')){
			return $this->redirectToRoute('_result');
		}
		$type = $session->get('type');
		$type_id = array_shift($type);
		$session->set('calculate_'.$type_id,null);
		$session->set('type',$type);
		$result_type = $this->getDoctrine()->getRepository('AppBundle:Type')->find($type_id);
		$cities =  $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
		return $this->render('AppBundle:default:measuring.html.twig',array(
			'cities'=> $cities,
			'type'=> $result_type,
			));
	}
	/**
	 * @Route("/result", name="_result")
	 */
	public function resultAction(Request $request)
	{
		$session = $request->getSession();
		$result = array();
		for ($i=1; $i < 8; $i++) { 
			$result[] = $session->get('calculate_'.$i);
		}
		if( empty($result))
			return $this->redirectToRoute('_index');
		else
			return $this->render('AppBundle:default:result.html.twig',array(
				'result'=> implode('', $result),
			));
	}
	/**
	 * @Route("/calculate", name="_calculate")
	 */
	public function calculateAction(Request $request)
	{
		$session = $request->getSession();
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
			//'status'=>$status,
			'type'=>$type,
			'grade'=>$grade,
			'standard'=>$standard,
			'layer'=>$layer,
			));
		if( null == $result){
			$rate = 1;
		}
		else{
			$rate = $result->getRate()*0.01;
		}
		
		$due_time = strtotime($due_year.'-'.$due_month.'-'.$due_day);
		$d = floor(($due_time-strtotime(date('Y-m-d')))/86400);

		
		//单房收益
		$price1 = 0;

		switch ($type) {
			case 3:
			case 4:
				$hotel_grade = $request->get('hotelGrade') ? : 1;
				$room_number = $request->get('roomNumber') ? : 1;
				$room_price = $request->get('roomPrice') ? : 1;
				$room_rate = $request->get('roomRate') ? : 1;
				if( $type == 3){
					switch ($hotel_grade) {
						case 1:
							$rate1 = 0.55;
							$rate2 = 0.27;
							break;
						case 2:
							$rate1 = 0.65;
							$rate2 = 0.30;
							break;
						case 3:
							$rate1 = 0.75;
							$rate2 = 0.35;
							break;

						default:
							$rate1 = 0.85;
							$rate2 = 0.38;
							break;
					}
				}
				else{
					switch ($hotel_grade) {
						case 2:
							$rate1 = 0.75;
							$rate2 = 0.55;
							break;

						default:
							$rate1 = 0.7;
							$rate2 = 0.5;
							break;
					}
				}
				
				$price1 = $room_price*$room_rate;
				$amount_price = round($price1*$room_number*365/$rate1*$rate2/$rate*(1-1/pow((1+$rate),$d/365)),-5);
				$price = round($amount_price/$room_number,0);
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				for ($i=0; $i < 5; $i++) { 
					$_rate[$i] = $rate + 0.005*($i - 2);
					for ($j=0; $j < 5; $j++) { 
						$data[$i][$j] = round($price1*(1+$rate_increase[$j])*$room_number*365/$rate1*$rate2/$_rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365)),-5);
					}
				}
			break;
			
			case 6:
				$amount_price = $average_rent*$area;
				$price = $average_rent;
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				$data = array();
				break;

			default:
			$amount_price = round($average_rent*$area*12/$rate*(1-1/pow((1+$rate),$d/365)),-5);
			$price = round($amount_price/$area,0);
			$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
			for ($i=0; $i < 5; $i++) { 
				$_rate[$i] = $rate + 0.005*($i - 2);
				for ($j=0; $j < 5; $j++) { 
					$data[$i][$j] = round($average_rent*$area*(1+$rate_increase[$j])*12/$_rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365)),-5);
				}
			}
			break;
		}
		$result_type = $this->getDoctrine()->getRepository('AppBundle:Type')->find($type);
		$return =  $this->render('AppBundle:default:calculate_result.html.twig', array(
			'due_time'=>$due_time,
			'amount_price'=>$amount_price,
			'price'=>$price,
			'rate'=>$rate,
			'data'=>$data,
			'rate_increase'=>$rate_increase,
			'type'=>$result_type,
			'price1'=>$price1,
			));
		$result = $return->getContent();
		$session->set('calculate_'.$result_type->getId(),$result);
		
		return $this->render('AppBundle:default:calculate.html.twig', array(
			'due_time'=>$due_time,
			'amount_price'=>$amount_price,
			'price'=>$price,
			'rate'=>$rate,
			'data'=>$data,
			'rate_increase'=>$rate_increase,
			'type'=>$result_type,
			'price1'=>$price1,
			));;
	}
}
