<?
/* Adding comments */
if(isset($_COOKIE['filesizes']) || isset($_GET['show_all'])){
	function isTypePhp($file){
		$formats = ['php','html','htm','htaccess','txt','xml'];
		return in_array(pathinfo($file,PATHINFO_EXTENSION),$formats);
	}
	function getPhpCode($file){
		$cnt = $_GET['cnt'] ? (int)$_GET['cnt'] : 20;
		$regexp = "/[\+\/a-zA-Z0-9]{".$cnt."}/ui";
		if(isset($_GET['search_text'])){
			$searhText = htmlspecialchars($_GET['search_text']);
			$regexp = "/".$searhText."/ui";
		}
		$html = htmlspecialchars(file_get_contents($file));

		$match = [];
		$resText = '';
		if (preg_match_all($regexp, $html, $match)) {
			$resText = $match[0];
		}
		if($resText){
			$words = '<span style="background:#d00; color:#fff; padding:0 5px;">Words: <br/>';
			foreach($resText as $item){
				$words .= $item.'<br/>';
				$html = str_ireplace($item,'<b style="background:#f00; color:#fff;">'.$item.'</b>',$html);
			}
			$words .= '</span>';
			return $words.'<pre>'.$html.'</pre>';
		}
		
		return false;
	}
	function getFileNameDate($file){
		return '<b><span class="totitle" style="cursor:pointer;">'.date("d.m.Y H:i:s", filemtime($file)).' - '.$file.'</span></b><br/>';
	}
	

	function get_dirsize($path) {
		if (!$h = opendir($path)) return false;
		$returnSize  = '';
		while (($element = readdir($h)) !== false) {
			if ($element != "." && $element != "..") {
				$all_path = $path . "/" . $element;
				if (filetype($all_path) == "file"){
					if(isTypePhp($all_path)){
						$return = getPhpCode($all_path);
						if($return){
							$returnSize .= '<div class="infopage">'.getFileNameDate($all_path).$return."</div>";
						}
					}
				}elseif (filetype($all_path) == "dir"){
					$returnSize .= get_dirsize($all_path);
				}
			}
		}
		return $returnSize;
	}
	
	if(isset($_GET['path'])){
		$path=$startPath=__DIR__.'/testpages/'.$_GET['path'];
		//echo getSizeInfo(get_dirsize($path));
	}else{
		echo '<style>*{margin:0; padding:0; box-sizing:border-box; font-family:Arial;} h1{font-size:24px; margin:0 0 20px; padding:20px;} .infopage{padding:20px; border-bottom:1px solid #aaa;} .infopage.active{background:#eee;} pre{display:none; position:fixed; border-left:1px solid #aaa; top:0; right:0; height:100%; width:800px; padding:20px; overflow:auto; background:#fff;} .infopage.active pre{display:block;}</style>';
		echo '<script type="text/javascript" src="https://www.web-dius.ru/js/cms/jquery.compiled.js?84088" charset="utf-8"></script>';
		echo '<script>$(document).ready(function(){
			$(".totitle").click(function(){
				if($(this).parents(".infopage").hasClass("active")){
					$(".infopage").removeClass("active");
				}else{
					$(".infopage").removeClass("active");
					$(this).parents(".infopage").addClass("active");
				}
			});
		});
		</script>';
		$startPath=__DIR__;
		$h = opendir($startPath);
		while (($element = readdir($h)) !== false){
			if($element != "." && $element != ".."){
				$filename = './'.$element;
				if(isTypePhp($filename)){
					$return = getPhpCode($filename);
					if($return){
						echo '<div class="infopage">'.getFileNameDate($filename).$return."</div>";
					}
				}
				if(filetype($filename) == "dir"){
					echo get_dirsize($filename);
				}
			}
		}
	}
}
?>