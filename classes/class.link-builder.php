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
	
	
	public function set_ingredents($ingredents){
		$this->link_ingredents = $ingredents;
		$this->set_title();
		$this->set_href();
		$this->set_keyPhrase();
	}
	
	public function get_prepared_url(){
		$this->link = "<a href='$this->href' title='$this->title'>$this->keyPhrase</a>";
		return $this->link;
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
}