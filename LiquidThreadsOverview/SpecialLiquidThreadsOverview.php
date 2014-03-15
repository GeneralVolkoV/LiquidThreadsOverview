<?php
class SpecialLiquidThreadsOverview extends IncludableSpecialPage {
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
	
	//Parameters for including this special page:
	//{{Special:LiquidThreadsOverview/limit/showsummarized/namespace}}
	//limit          = maximum number of threads (default 50)
	//showsummarized = also show summarized threads (default false)
	//namespace      = numerical id of namespace for filtering (default all)
	function execute( $par ) {
		global $wgOut, $wgNamespaces, $wgParser, $wglqtoCss, $wglqtoUseIcons, $wglqtoIconType;
		
		if (!$this->including())
			$this->setHeaders();
		$output='';
		
		$parex=explode("/",$par);
		$limit=50;
		$summarized=' AND (thread_summary_page IS NULL)';
		if(is_numeric($parex[0]))
			$limit=$parex[0];
		if(isset($parex[1]))
			if(trim($parex[1])==true)
				$summarized='';
		
		$namespacerestriction='';

		if(isset($parex[2]))
			if(is_numeric($parex[2]))
				$namespacerestriction=' AND (thread_article_namespace='.$parex[2].')';
		
		
		$dbr = wfGetDB( DB_SLAVE );
		//SELECT *
		//FROM prefix_thread
		//WHERE (thread_parent IS NULL OR thread_ancestor=thread_id) AND (thread_type<>2)
		//ORDER BY thread_modified DESC
		$res = $dbr->select(
			'thread',
			'*',
			$conds = '(thread_parent IS NULL OR thread_ancestor=thread_id) AND (thread_type<>2)'.$summarized.$namespacerestriction,
			__METHOD__,
			array( 'ORDER BY' => 'thread_modified DESC', 'LIMIT' => $limit )
		);
		$output.='<table width="100%" class="'.$wglqtoCss.'"><tr>';
		if($wglqtoUseIcons)
			$output.='<th style="text-align:left;">&nbsp;</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-page').'</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-topic').'</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-author').'</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-answers').'</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-created').'</th>';
		$output.='<th style="text-align:left;">'.wfMessage( 'lqto-modified').'</th>';
		if($summarized=='')
			$output.='<th style="text-align:left;">'.wfMessage( 'lqto-summarized').'</th>';
		$lang=new Language();
		foreach( $res as $row ) {
			$namespace=$lang->getNsText($row->thread_article_namespace);
			$article=str_replace("_"," ",$row->thread_article_title);
			$pagelink=Linker::link(Title::newFromText($namespace.':'.$article),$article);
			$threadlink=Linker::link(Title::newFromText($namespace.':'.$article.'#'.$row->thread_subject.'_'.$row->thread_id),$row->thread_subject);
			$userlink=Linker::link(Title::newFromText($lang->getNsText(NS_USER).':'.$row->thread_author_name),$row->thread_author_name);
			$icontitletext=$lang->getNsText(NS_FILE).':Icon '.$namespace.'.'.$wglqtoIconType;
			$icontitle=Title::newFromText($icontitletext);
			//$iconlink= Linker::makeImageLink($wgParser,$icontitle,wfLocalFile($icontitle),null,Array('height'=>'20px','width'=>'20px'));
			$iconlink=$wgParser->parse("[[$icontitletext|20x20px|link=]]",$wgParser->getTitle(),new ParserOptions())->getText();

			$output.="</tr><tr>";
			if($wglqtoUseIcons) {
				$output.="<td>$iconlink</td>";
			}

			$output.="<td>$pagelink</td>";
			$output.="<td>$threadlink</td>";
			$output.="<td>$userlink</td>";
			$output.="<td>".$row->thread_replies.'</td>';
			$output.="<td>".$this->formattime($row->thread_created).'</td>';
			$output.="<td>".$this->formattime($row->thread_modified).'</td>';
			if($summarized=='') {
				$s=wfMessage( 'lqto-summaryno');
				if($row->thread_summary_page!=null)
					$s=wfMessage( 'lqto-summaryyes');
				$output.="<td>$s</td>";
			}
		}
		$output.="</tr></table>";
 
		$wgOut->addHTML( $output );
	}
}