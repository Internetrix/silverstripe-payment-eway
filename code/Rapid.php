<?php

class Rapid extends Page_Controller {
	
	/**
	 * Controller action for showing payment form
	 *
	 * @param SS_HTTPRequest
	 */
	public function pay($request) {

		return array(
			'Title' => 'Enter your credit card details',
			'Content' => '',
			'Form' => $this->PayForm()
		);
	}

	/**
	 * Return the payment form
	 */
	public function PayForm() {
		
		$request = $this->getRequest();
		$response = Session::get('EwayResponse');

		$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
		$years = range(date('y'), date('y') + 10); //Note: years beginning with 0 might cause issues

		$amount 	= $response->Payment->TotalAmount;
		$amount 	= number_format($amount / 100, 2);
		$currency 	= $response->Payment->CurrencyCode;
		
		$fields = new FieldList(
			HiddenField::create('EWAY_ACCESSCODE', '', $response->AccessCode),
			TextField::create('PayAmount', 'Amount', $amount . ' ' . $currency)->setDisabled(true),
			$nameField = TextField::create('EWAY_CARDNAME', 'Card holder'),
			$numberField = TextField::create('EWAY_CARDNUMBER', 'Card Number'),
			$expMonthField = DropdownField::create('EWAY_CARDEXPIRYMONTH', 'Expiry Month', array_combine($months, $months)),
			$expYearField = DropdownField::create('EWAY_CARDEXPIRYYEAR', 'Expiry Year', array_combine($years, $years)),

			// TextField::create('EWAY_CARDSTARTMONTH', 'Valid from month', ''), //UK only
			// TextField::create('EWAY_CARDSTARTYEAR', 'Valid from year', ''), //UK only
			// TextField::create('EWAY_CARDISSUENUMBER', 'Issue number', ''),
			
			$cvnField = TextField::create('EWAY_CARDCVN', 'CVN Number'),
			HiddenField::create('FormActionURL', '', $response->FormActionURL)
		);
		
		//Test data
		if (Director::isDev()) {
			$nameField->setValue('Test User');
			$numberField->setValue('4444333322221111');
			$expMonthField->setValue('12');
			$expYearField->setValue(date('y') + 1);
			$cvnField->setValue('123');
		}

		$actions = new FieldList(
			FormAction::create('', 'Process')	
		);

		$form = new Form($this, 'PayForm', $fields, $actions);
		$form->setFormAction($response->FormActionURL);
		
		Requirements::javascript(FRAMEWORK_DIR .'/thirdparty/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
		Requirements::javascript('payment-eway/javascript/eway-form.js');
		
		$this->extend('updatePayForm', $form);
		
		return $form;
	}

}