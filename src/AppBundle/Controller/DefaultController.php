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
		for ($i=1; $i < 8; $i++) { 
			$result[] = $session->set('calculate_'.$i,null);
			$result[] = $session->set('amount_price_'.$i,0);
		}
		$session->set('amount_price', 0);
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
		$type_id = current($type);
		//$type_id = array_shift($type);
		//$session->set('calculate_'.$type_id,null);
		//$session->set('type',$type);
		$amount_price = 0;
		for ($i=1; $i < 7; $i++) { 
			if( null != $session->get('amount_price_'.$i))
				$amount_price += $session->get('amount_price_'.$i);
		}
		$session->set('amount_price', $amount_price);

		$result_type = $this->getDoctrine()->getRepository('AppBundle:Type')->find($type_id);
		$cities =  $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
		if($type_id == 2)
			$date1 = array(date('Y')+40,date('n'),date('j'));
		elseif($type_id == 7)
			$date1 = array(date('Y')+2,date('n'),date('j'));
		else
			$date1 = array(date('Y')+50,date('n'),date('j'));
		return $this->render('AppBundle:default:measuring.html.twig',array(
			'cities'=> $cities,
			'type'=> $result_type,
			'date1' => $date1,
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
		$price2 = 0;
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
				$room_rate = $request->get('roomRate') ? (int)$request->get('roomRate') : 1;
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
				
				$price1 = $room_price*$room_rate/100;
				$amount_price = $price1*$room_number*365/$rate1*$rate2/$rate*(1-1/pow((1+$rate),$d/365));
				$price = $amount_price/$room_number;
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				$price2 = $amount_price/$area;
				for ($i=0; $i < 5; $i++) { 
					$_rate[$i] = $rate + 0.005*($i - 2);
					for ($j=0; $j < 5; $j++) { 
						$data[$i][$j] = $price1*(1+$rate_increase[$j])*$room_number*365/$rate1*$rate2/$_rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365));
					}
				}
			break;
			
			case 6:
				$amount_price = $average_rent*$area;
				$price = $average_rent;
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				$data = array();
				break;

			case 7:
				$completion_rate = $request->get('completionRate') ? (int)$request->get('completionRate')*0.01 : 1;
				$total_costs = $request->get('totalCosts') ? : 1;
				$c17 = $d/365;
				if($d < 365){
					$rate = 0.0435;
				}
				elseif ($d <= 365*2+1) {
					$rate = 0.0475;
				}
				else{
					$rate = 0.049;
				}
				//c2 = amount_price
				//c8 = total_costs
				//c10 = completion_rate;
				$c2 = $session->get('amount_price');
				$c8 = $total_costs;
				$c10 = $completion_rate;
				$p1 = pow((1+$rate),$c17*0.5);
				$p2 = pow((1+$rate),$c17);
				$amount_price = ($c2*0.93-$c8*(1-$c10)*1.05-$c8*(1-$c10)*1.05*($p1-1)-$c8*(1-$c10)*1.05*0.1*$c17)/(1+0.1*$c17+($p2-1));
				//(C2×0.93−C8×(1−C10)×(1.05)−C8×(1−C10)×(1.05)×(p1−1)−C8×(1−C10)×(1.05)×10%×C17)÷(1+10%×C17+(p2−1))
				//(C2×0.93−C8×(1−C10)×(1.05)−C8×(1−C10)×(1.05)×((1+B22)^(C17×0.5)−1)−C8×(1−C10)×(1.05)×10%×C17)÷(1+10%×C17+((1+B22)^C17−1))
				$price = $amount_price/$session->get('amount_price');
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				$data = array();
				break;

			default:
				$amount_price = $average_rent*$area*12/$rate*(1-1/pow((1+$rate),$d/365));
				$price = $amount_price/$area;
				$rate_increase = array(0.1,0.05,0,-0.05,-0.1);
				for ($i=0; $i < 5; $i++) { 
					$_rate[$i] = $rate + 0.005*($i - 2);
					for ($j=0; $j < 5; $j++) { 
						$data[$i][$j] = $average_rent*$area*(1+$rate_increase[$j])*12/$_rate[$i]*(1-1/pow((1+$_rate[$i]),$d/365));
					}
				}
				break;
		}

		$types = $session->get('type');
		foreach ($types as $key => $value) {
			if($value == $type){
				unset($types[$key]);
			}
			//$session->set('calculate_'.$value,null);
			$session->set('type',$types);
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
			'price2'=>$price2,
			));
		$result = $return->getContent();
		$session->set('calculate_'.$result_type->getId(),$result);
		$session->set('amount_price_'.$result_type->getId(),$amount_price);

		
		return $this->render('AppBundle:default:calculate.html.twig', array(
			'due_time'=>$due_time,
			'amount_price'=>$amount_price,
			'price'=>$price,
			'rate'=>$rate,
			'data'=>$data,
			'rate_increase'=>$rate_increase,
			'type'=>$result_type,
			'price1'=>$price1,
			'price2'=>$price2,
			));;
	}
}
