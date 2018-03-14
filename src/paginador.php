<?php
/* Title : Ajax Pagination with jQuery & PHP
Example URL : http://www.sanwebe.com/2013/03/ajax-pagination-with-jquery-php */

//continue only if $_POST is set and it is a Ajax request
//	
//	include '../config/sivisae_class.php';  //include config file
//        $consulta = new sivisae_consultas();
//	//Get page number from Ajax POST
//        $pagina = $_POST["page"];
//        $auditor = $_POST['auditor'];
//        echo $pagina;
//	if(isset($_POST["page"])){
//		$page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
//		if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
//	}else{
//		$page_number = 1; //if there's no page number, set it to 1
//	}
//	$item_per_page = 10;
//	//get total number of records from database for pagination
//	$results = $consulta->cantEstudiantesAsignados($auditor);
//	$get_total_rows = $results[0]; //hold total records in variable
//	//break records into pages
//	$total_pages = ceil($get_total_rows/$item_per_page);
//	
//	//get starting position to fetch the records
//	$page_position = (($page_number-1) * $item_per_page);
//	
//	//SQL query that will fetch group of records depending on starting position and item per page. See SQL LIMIT clause
//	$results = $consulta->estudiantesAsignados($auditor, $page_position, $item_per_page);
//	
//	//Display records fetched from database.
//	
//	echo '<ul class="contents">';
//	while($row = mysql_fetch_assoc($results)) {
//		echo '<li>';
//		echo  $row['cedula']. '. <strong>' .$row['nombre'].'</strong> &mdash; '.$row['descripcion'];
//		echo '</li>';
//	}  
//	echo '</ul>';
//	
//	
//	echo '<div align="center">';
//	/* We call the pagination function here to generate Pagination link for us. 
//	As you can see I have passed several parameters to the function. */
//	echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
//	echo '</div>';

################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination">';
        
        $right_links    = $current_page + 5; 
        $previous       = $current_page - 1; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link
        
        if($current_page > 1){
			$previous_link = ($previous==0)?1:$previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="Primero">&laquo;</a></li>'; //first link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Anterior">&lt;</a></li>'; //previous link
                for($i = ($current_page-4); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li><a href="#" data-page="'.$i.'" title="P&aacute;gina '.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; //set first link to false
        }
        
        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="P&aacute;gina '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
				$next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Siguiente">&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="&Uacute;ltimo">&raquo;</a></li>'; //last link
        }
        
        $pagination .= '</ul>'; 
    }
    return $pagination; //return pagination links
}
?>

