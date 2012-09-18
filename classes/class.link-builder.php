<?php
/*
 * builds links
 * */

class aLinks_linksbuilder{
	private  $link = '';
	private  $link_ingredents = array();
	private  $title = '';
	private  $href = '';
	private  $keyPhrase = '';
	private  $is_amazon = false;
	private  $randomness;
	private  $option;
	private  $exchange;
	
	
	public function set_ingredents($ingredents){
		$this->link_ingredents = $ingredents;
		$this->set_title();
		$this->set_href();
		$this->set_keyPhrase();
		$this->set_option();
		$this->set_randomness();
		$this->set_exchange();
	}
	
	public function get_prepared_url(){
		$link = $this->keyPhrase;
		
		switch($this->option){
			case "1" :
				$link = '<a href="' . $this->href . '"> ' . $this->keyPhrase . '</a>';
				break;
			case "2" :
				$link = $this->keyPhrase. ' ' . $this->href;
				break;
			case "3" :
				$link = '<a href="' . $this->href . '"> ' . $this->exchange . '</a>';
				break;
			case "4" :
							
		}
		
		return $link;
	}
	
	
	private function set_title(){
		$this->title = $this->link_ingredents['title'];
	}
	
	private function set_href(){
		$this->href = $this->link_ingredents['href'];
	}
	
	private function set_keyPhrase(){
		$this->keyPhrase = $this->link_ingredents['keyphrase'];
	}	
	
	private function set_option(){
		$this->option = $this->link_ingredents['settings']['options'];
	}
	
	private function set_randomness(){
		$this->randomness = $this->link_ingredents['settings']['randomness'];
	}
	
	private function set_exchange(){
		$this->exchange = $this->link_ingredents['exchange'];
	}
}