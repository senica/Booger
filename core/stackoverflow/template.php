<?php require(ASSETS.'/no_direct.php'); ?>
<?php
if(!empty($content->questions)){
foreach($content->questions as $c){
	echo '<div class="core-stackoverflow question-wrapper">';
		echo '<div class="core-stackoverflow score-wrapper">';
			echo '<div class="core-stackoverflow score">'.$c->score.'</div>';
			echo '<div class="core-stackoverflow score-text">votes</div>';
			echo '<div class="core-stackoverflow answer-count '.(($c->answer_count <= 0) ? 'inactive' : 'active').'">'.$c->answer_count.'</div>';
			echo '<div class="core-stackoverflow answer-text '.(($c->answer_count <= 0) ? 'inactive' : 'active').'">answers</div>';
		echo '</div>';
		echo '<div class="core-stackoverflow summary-wrapper">';
			if(isset($_GET['id'])){
				echo '<h3 class="core-stackoverflow title"><a href="http://www.stackoverflow.com/questions/'.$c->question_id.'" target="_blank">'.$c->title.'</a></h3>';
			}else{
				echo '<h3 class="core-stackoverflow title"><a href="?id='.$c->question_id.'">'.$c->title.'</a></h3>';
			}
			if(isset($_GET['id'])){
				echo '<div class="core-stackoverflow body">'.$c->body.'</div>';
			}else{
				echo '<div class="core-stackoverflow body">'.substr(strip_tags($c->body), 0, 100).'</div>';
			}
			echo '<div class="core-stackoverflow tags-wrapper">Tags: ';
				foreach($c->tags as $tag){
					echo '<span>'.$tag.'</span>';	
				}
			echo '</div>';
			echo '<div class="core-stackoverflow account-wrapper">';
				echo '<div class="core-stackoverflow created-on">asked on '.date("M d, Y H:i:s", $c->creation_date).'</div>';
				echo '<div class="core-stackoverflow author">by '.$c->owner->display_name.'</div>';
			echo '</div>';
			if(isset($_GET['id']) && !empty($c->comments)){
				echo '<div class="core-stackoverflow comments-wrapper">';
					echo '<div class="core-stackoverflow comments-header">Comments:</div>';
					foreach($c->comments as $co){
						echo '<div class="core-stackoverflow comment">'.$co->body.' <span class="core-stackoverflow comment-author">-'.$co->owner->display_name.'</span></div>';	
					}
				echo '</div>';	
			}
		echo '</div>';
	echo '</div>';
	//Display Answers
	if(isset($_GET['id'])){
		echo '<div class="core-stackoverflow answer-header">'.$c->answer_count.' '.(($c->answer_count <= 0) ? 'Answer' : 'Answers').'</div>';
		if(!empty($c->answers)){
		foreach($c->answers as $a){
			echo '<div class="core-stackoverflow question-wrapper">';
				echo '<div class="core-stackoverflow score-wrapper">';
					echo '<div class="core-stackoverflow score">'.$a->score.'</div>';
					echo '<div class="core-stackoverflow score-text">votes</div>';
					if($a->accepted == true){
						echo '<div class="core-stackoverflow answer-count accepted active"></div>';
						echo '<div class="core-stackoverflow answer-text active">accepted</div>';
					}
				echo '</div>';
				echo '<div class="core-stackoverflow summary-wrapper">';
					echo '<div class="core-stackoverflow body">'.$a->body.'</div>';
					echo '<div class="core-stackoverflow tags-wrapper"></div>';
					echo '<div class="core-stackoverflow account-wrapper">';
						echo '<div class="core-stackoverflow created-on">asked on '.date("M d, Y H:i:s", $a->creation_date).'</div>';
						echo '<div class="core-stackoverflow author">by '.$a->owner->display_name.'</div>';
					echo '</div>';
					if(!empty($a->comments)){
						echo '<div class="core-stackoverflow comments-wrapper">';
							echo '<div class="core-stackoverflow comments-header">Comments:</div>';
							foreach($a->comments as $ao){
								echo '<div class="core-stackoverflow comment">'.$ao->body.' <span class="core-stackoverflow comment-author">-'.$ao->owner->display_name.'</span></div>';	
							}
						echo '</div>';	
					}
				echo '</div>';
			echo '</div>';
		}}
		
		echo '<div class="core-stackoverflow answer-this"><a href="http://www.stackoverflow.com/questions/'.$c->question_id.'#new-answer" target="_blank">Answer This Question On StackOverflow.com</a></div>';
	}
}
}else{
	echo '<b>There were no results to display, or the connection to StackOverflow.com has been lost.</b>';
}	

if(empty($_GET['id'])){
	$pages = $bg->paginate($content->total, $content->page, $content->pagesize, 7);
	echo '<div class="core-stackoverflow pages-wrapper">';
	if(!empty($pages)){
	foreach($pages as $p){
		if(is_numeric($p)){
			//Allow for search results to be posted back
			echo '<a href="?'.((!empty($_GET['search'])) ? 'search='.urlencode($_GET['search']).'&' : '').'pg='.$p.'" class="'.(($p == $content->page) ? 'active': '').'">'.$p.'</a>';	
		}else{
			echo '<span>'.$p.'</span>';	
		}
	}
	}
	echo '</div>';
}
?>