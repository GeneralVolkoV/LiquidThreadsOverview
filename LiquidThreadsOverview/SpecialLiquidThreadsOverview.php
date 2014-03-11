<?php
class SpecialLiquidThreadsOverview extends SpecialPage {
	function __construct() {
		parent::__construct( 'LiquidThreadsOverview' );
	}
	
	function formattime($time) {
		$y=substr($time, 0,4);
		$m=substr($time, 4,2);
		$d=substr($time, 6,2);
		$h=substr($time, 8,2);
		$i=substr($time,10,2);
		$s=substr($time,12,2);
		return "$y-$m-$d $h:$i:$s";
	}
 
	function execute( $par ) {
		global $wgOut, $wgNamespaces;
		
		$this->setHeaders();
		
		$dbr = wfGetDB( DB_SLAVE );
		//SELECT *
		//FROM prefix_thread
		//WHERE (thread_parent IS NULL OR thread_ancestor=thread_id) AND (thread_type<>2)
		//ORDER BY thread_modified DESC
		$res = $dbr->select(
			'thread',
			'*',
			$conds = '(thread_parent IS NULL OR thread_ancestor=thread_id) AND (thread_type<>2) AND (thread_summary_page IS NULL)',
			__METHOD__,
			array( 'ORDER BY' => 'thread_modified DESC' )
		);
		$output='{| width="100%" class="tabellehuebsch sortable"';
		$output.="\n!".'style="text-align:left;"|&nbsp;';
		$output.="\n!".'style="text-align:left;"|Seite';
		$output.="\n!".'style="text-align:left;"|Thema';		
		$output.="\n!".'style="text-align:left;"|Autor';		
		$output.="\n!".'style="text-align:left;"|Antworten';		
		$output.="\n!".'style="text-align:left;"|Erzeugt';		
		$output.="\n!".'style="text-align:left;"|Modifiziert';		
		$lang=new Language();
		foreach( $res as $row ) {
			$namespace=$lang->getNsText($row->thread_article_namespace);
			$article=str_replace("_"," ",$row->thread_article_title);
			$pagelink=$namespace.':'.$article;
			$threadlink=$pagelink.'#'.$row->thread_subject.'_'.$row->thread_id;
			$output.="\n|-";
			$output.="\n||[[Datei:Icon ".$namespace.".svg|20x20px|link=]]";
			$output.="\n||[[".$pagelink."|".$article."]]";
			$output.="\n||[[".$threadlink."|".$row->thread_subject."]]";
			$output.="\n||[[".$lang->getNsText(NS_USER).':'.$row->thread_author_name.'|'.$row->thread_author_name.']]';
			$output.="\n||".$row->thread_replies;
			$output.="\n||".$this->formattime($row->thread_created);
			$output.="\n||".$this->formattime($row->thread_modified);
		}
		$output.="\n|}";
 
		$wgOut->addWikiText( $output );
	}
}