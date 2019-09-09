<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ExchangeController extends AbstractController
{
	/**
	 * @Route("/", name="exchange")
	 */
	public function index(Request $request)
	{
		$date = $request->request->get('date');

		$date_now = date('Y-m-d');
		// $date_now = '2019-09-08';

		$render_array = [
			'last_date' => $date_now,
		];

		if ( !is_null($date) ) {

			$preg_match = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);

			if ( !$preg_match || $date > $date_now ) {

				$render_array['validation_message'] = 'Proszę wprowadzić poprawną datę.';
				
				return $this->render('exchange.html.twig', $render_array);
			}

			$render_array['last_date'] = $date;

			$url_start = 'http://api.nbp.pl/api/exchangerates/rates/c/usd/';
			$url = $url_start . $date . '/' . $date_now;

			$content = @file_get_contents($url);

			$object = json_decode($content);

			if ( !empty($object->rates) ) {

				$rates = $object->rates;
				$rates = array_reverse($rates);

				$render_array['rates'] = $rates;
				
				return $this->render('exchange.html.twig', $render_array);
			}
			else {
				$render_array['alert_message'] = 'Nie udało się pobrać danych.';
				
				return $this->render('exchange.html.twig', $render_array);
			}
		}

		return $this->render('exchange.html.twig', $render_array);
	}

}
