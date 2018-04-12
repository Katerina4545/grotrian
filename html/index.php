<?
if (isset ($_REQUEST['pagetype']) && $_REQUEST['pagetype'] == "spectrumpng"){
	header("Content-type: image/png;");
	require_once("configure.php");
	require_once("includes/atom.php");
	$atom = new Atom;
	$element_id=$_REQUEST['element_id'];
	$atom->Load($element_id);
	$atom_sys = $atom->GetAllProperties();
	echo $atom_sys['SPECTRUM_IMG'];
	exit;
}
	header('Content-Type: text/html; charset=windows-1251'); 
	global $smarty, $dictionary, $element_types;
	//session_start();

	require_once("configure.php");
	require_once("includes/elementlist.php");
	require_once("includes/atom.php");
	require_once("includes/atomlist.php");
	require_once("includes/levellist.php");
	require_once("includes/transitionlist.php");
	require_once("includes/sourcelist.php");
	require_once("includes/spectrum.php");
	require_once("includes/user.class.php");

	// require_once("includes/counter.php");
	// $counter = new Counter;
	// $counter->Create();
	///

	//print_r($result);
	
	//���������� ����� �����������
	require_once("includes/localization.class.php");
	//���������� �������
	require_once("dictionary/dictionary.inc");
	
	$l10n = new Localization($dictionary);
	$elements = new ElementList;
	//��������� ������� ��������� � ��������������� ������� � ��. ���������� = 0;	
	$elements->LoadPereodicTable($l10n->locale,0);
	$table=$elements->GetItemsArray();
	$smarty->assign('periodic_table',$table);
	
	//��������� ����. ���������� �������  ��. ���������� = 0;	
	//$elements->LoadMaxLevelsNUM(0);
	//$maxLevels=$elements->GetItemsArray();
	//$smarty->assign('MaxLevels',$maxLevels[0]["MAXLEVELS_NUM"]);
	
	
	//print_r($l10n->locale);
	//print_r($l10n->dictionary);
	//print_r($_COOKIE);
	//���� � ������ ������� ���� id �������� � ��� �����
	//print_r($_REQUEST);

	if((isset($_REQUEST['pagetype'])) && ($_REQUEST['pagetype']!="articles") 
	&& ($_REQUEST['pagetype']!="bibliography") && ($_REQUEST['pagetype']!="sources") 
	&& (isset($_REQUEST['element_id'])) && (is_numeric($_REQUEST["element_id"])))	
	{	
		$element_id=$_REQUEST['element_id'];
		
		//����
		$ion_list = new ElementList;
		$ion_list->Loadions($element_id);		

		//���� ������ �������� ������ �����
		$ions=$ion_list->GetItemsArray();
		if ($ions)	{
			//�������� ��� ��������
			$elname=$ions[0]['ELNAME'];		

			//�������� ������ ����� ���������(�� �������) � �������� ��� � smarty()			
			
			$smarty->assign('element_types', $element_types);
			//���� ����� ������ � �������� � ������� ��� ������

			$atom = new Atom;
			$atom->Load($element_id);

			$atom_sys = $atom->GetAllProperties();
			$atom_name = $elname;
			if ($atom_name !='H' && $atom_name !='D' && $atom_name !='T' )
				$atom_name .= ' ' . numberToRoman(intval($atom_sys['IONIZATION']) + 1);
			$smarty->assign('atom', $atom_sys);
			
			$ichi = '1S/'.$elname;
			$ichi .= !empty($atom_sys['IONIZATION']) ? "/q+".$atom_sys['IONIZATION'] : "";
			//$ichi_key = hash('sha256',$ichi);
			
			$smarty->assign('ichi', $ichi);
			//$smarty->assign('ichi_key', $ichi_key);

			$e_count = intval($atom_sys['Z']) - intval($atom_sys['IONIZATION']);
			$smarty->assign('e_count', $e_count);

			//������
			$level_list = new LevelList;
			// ����� � ������ ����� �������
			$level_count = $level_list->LoadCount($element_id);			
			$smarty->assign('level_count', $level_count);

		
			//��������
			$transition_list = new TransitionList;
			// ����� � ������ ����� ���������
			$transition_count = $transition_list->LoadCount($element_id);
			$smarty->assign('transition_count', $transition_count);			
			
			//�������� ������������
//			$smarty->assign('book_count', 0);		
		
			$smarty->assign("bodyclass","elements");	
			
	
		}
	}

	//���� �� �������� ��� ��������
	if(isset($_REQUEST['pagetype'])){
	$pagetype=$_REQUEST['pagetype'];

	//���� ���� ���������� �� ���������� � �� - ��������� 
	if(isset($_REQUEST['interface'])){
		include "includes/auth.php"; 	
		$interface = "edit";	
		$page_type = "view";					
	
	}	else $interface="view";
	
	//���� ���� ���������� �� ��������
	if (isset($elname))		
	//� ����������� �� ���� �������� ��������� ������� ���������
	switch ($pagetype) 
	{
		case "element": {

			// 	���� ������ ���������	
			$transition_list->LoadWithLevels($element_id);
			$transitions = $transition_list->GetItemsArray();

			$page_type = "view_element.tpl";
			$head = "element_description";
			$title = "element_description";
			$headline = "element_description";
			$bodyclass = "element";
			$header_type = "header.tpl";
			$footer_type = "footer.tpl";


			if (isset($_POST['export'])) {
				$spectrum = new Spectrum();
				$spectrum->export($transitions, $elname);
			}
    		
    		break;
    	}

		case "compare" : {
			// 	���� ������ ���������	
			$transition_list->LoadWithLevels($element_id);
			$transitions=$transition_list->GetItemsArray();
			// ���� json ������ ���� ���� � ����� ��� � ������
			$spectrum= new Spectrum();			
			$smarty->assign('spectrum_json',$spectrum->getSpectraSVG($transitions,0,1599900000));

			$spectrum_json_uploaded = 0;
			
			if ((isset($_FILES['file']) && !$_FILES['file']['error']) || isset($_REQUEST['standard_file'])) {
				if (isset($_REQUEST['standard_file'])) {
					$file = $_REQUEST['standard_file'];

					switch ($file) {
						case 1:
							$_FILES['file']['tmp_name'] = 'files/hghe500.csv';
							break;
						case 2:
							$_FILES['file']['tmp_name'] = 'files/Cu-Zn-hollow cathode-300msec.csv';
							break;
						case 3:
							$_FILES['file']['tmp_name'] = 'files/DDS030-170msec.csv';
							break;
						case 4:
							$_FILES['file']['tmp_name'] = 'files/DDS030-36msec.csv';
							break;
						case 5:
							$_FILES['file']['tmp_name'] = 'files/DDS030-470msec.csv';
							break;
						case 6:
							$_FILES['file']['tmp_name'] = 'files/DVS25-1000msec.csv';
							break;
						case 7:
							$_FILES['file']['tmp_name'] = 'files/DVS25-245msec.csv';
							break;
						case 8:
							$_FILES['file']['tmp_name'] = 'files/DVS25-500msec.csv';
							break;
						case 9:
							$_FILES['file']['tmp_name'] = 'files/Hg-hollowcathode-21msec.csv';
							break;
						case 10:
							$_FILES['file']['tmp_name'] = 'files/Home-125msec.csv';
							break;
						case 11:
							$_FILES['file']['tmp_name'] = 'files/Home-25msec.csv';
							break;
						case 12:
							$_FILES['file']['tmp_name'] = 'files/Na-1700msec.csv';
							break;
						case 13:
							$_FILES['file']['tmp_name'] = 'files/Na-170msec.csv';
							break;
						case 14:
							$_FILES['file']['tmp_name'] = 'files/Ta-hollow cathode-62msec.csv';
							break;
						case 15:
							$_FILES['file']['tmp_name'] = 'files/Ta-Kortek-hollowcathode-90msec.csv';
							break;
						case 16:
							$_FILES['file']['tmp_name'] = 'files/Cz - 50 ms.csv';
							break;
						case 17:
							$_FILES['file']['tmp_name'] = 'files/Cz - 100 ms.csv';
							break;
						case 18:
							$_FILES['file']['tmp_name'] = 'files/Cz - 1000 ms.csv';
							break;
					}

				}  

				$spectrum_json_uploaded = $spectrum->parse_file($_FILES['file']);
			}

			$level_list = new LevelList;
			$level_list->LoadBase($element_id);
			$levels_array = $level_list->GetItemsArray();
			$smarty->assign('base_level', $levels_array[0]['CONFIG']);

			$smarty->assign('spectrum_json_uploaded', $spectrum_json_uploaded);  

    		$page_type="compare_element.tpl";
			$head="Compare_spectra";
			$title="Compare_spectra";
			$headline="Compare_spectra";
			$bodyclass="compare";
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";

			break;
		}

	    case "levels": {
	    
	    	//����� � ������ ������ �������
			$level_list->Load($element_id);
			
			$smarty->assign('level_list',$level_list->GetItemsArray());
				    
	    	//��������� ��� ������� � �������� ��������    		
			$page_type="view_levels.tpl"; 
    		$head="Atomic_levels";
    		$title="Atomic_levels";
    		$headline="Atomic_levels";
    		$bodyclass="levels"; 
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
    	
		case "addlevels": {
	    //print_r($_GET);
			if (isset($_GET['attribute2']) || isset($_GET['attribute3'])){
				$level_list->LoadFiltered($element_id,$_GET['attribute2'], isset($_GET['attribute3'])?$_GET['attribute3']:null);
			} else $level_list->Load($element_id);
			
	    	//����� � ������ ������ �������		
			//$level_list->Load($element_id);
			if (isset($_GET['attribute1']))	$smarty->assign('transition_id',$_GET['attribute1']);
			if (isset($_GET['attribute2']))	$smarty->assign('position',$_GET['attribute2']);
			
			//print_r($level_list->GetItemsArray());
			$smarty->assign('level_list',$level_list->GetItemsArray());
				    
	    	//��������� ��� ������� � �������� ��������    		
			$page_type="add_levels.tpl"; 
    		$head="Atomic_levels";
    		$title="Atomic_levels";
    		$headline="Atomic_levels";
    		$bodyclass="levels"; 
    		$header_type="iframe_header.tpl";
    		$footer_type="iframe_footer.tpl";
    		break;
    	}
    	
		case "transitions": {

		    // ����� � ������ ������ ���������	
			$transition_list->LoadWithLevels($element_id);
			$smarty->assign('transition_list',$transition_list->GetItemsArray());
				
    		//��������� ��� ������� � �������� ��������    		
			$page_type="view_transitions.tpl"; 
    		$head="Atomic_transitions";
    		$title="Atomic_transitions";
    		$headline="Atomic_transitions";
    		$bodyclass="transitions"; 
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";
    		break;    		

    	}    	

    	case "diagram": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type="view_diagram.tpl"; 
    		$head="Grotrian_Charts";
    		$title="Grotrian_Charts";
    		$headline="Atomic_charts";
    		$bodyclass="diagram";
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}

        case "spectrum": {
            $transition_list->LoadWithLevels($element_id);
            $transitions=$transition_list->GetItemsArray();
            // ���� json ������ ���� ���� � ����� ��� � ������
            $spectrum= new Spectrum();
            if (isset($_REQUEST['auto'])){
                $smarty->assign('auto', true);
                $atomNext = new Atom;
                $atomNext->LoadNext($element_id);
                $atomNext_sys = $atomNext->GetAllProperties();
                $smarty->assign('next_element_id', $atomNext_sys['ID']);
            }
            $smarty->assign('spectrum_json',$spectrum->getSpectraSVG($transitions,0,1599900000));

            $level_list = new LevelList;
            $level_list->LoadBase($element_id);
            $levels_array = $level_list->GetItemsArray();
            $smarty->assign('base_level', $levels_array[0]['CONFIG']);

            //��������� ��� ������� � �������� ��������
            $page_type="view_spectrum.tpl";
            $head="Spectrogram";
            $title="Spectrogram";
            $headline="Spectrogram";
            $bodyclass="spectrum";
            $header_type="header.tpl";
            $footer_type="footer.tpl";
            break;
        }
		case "circle": {
			$transition_list->LoadGroupForCircleSpectrum($element_id);
			$transitions=$transition_list->GetItemsArray();
			// ���� json ������ ���� ���� � ����� ��� � ������
			$spectrum= new Spectrum();
			if (isset($_REQUEST['auto'])){
				$smarty->assign('auto', true);
				$atomNext = new Atom;
				$atomNext->LoadNext($element_id);
				$atomNext_sys = $atomNext->GetAllProperties();
				$smarty->assign('next_element_id', $atomNext_sys['ID']);
			}

			$grouped_levels = new LevelList();
			$grouped_levels->Load($element_id);
			$grouped_levels_arr=$grouped_levels->GetItemsArray();
			$smarty->assign('levels_json',$spectrum->getLevelsSVG($grouped_levels_arr));
			$smarty->assign('spectrum_json',$spectrum->getSpectraSVG($transitions,0,1599900000));

			$level_list = new LevelList;
			$level_list->LoadBase($element_id);
			$levels_array = $level_list->GetItemsArray();
			$smarty->assign('base_level', $levels_array[0]['CONFIG']);

			//��������� ��� ������� � �������� ��������
			$page_type="view_circle.tpl";
			$head="Spectrogram";
			$title="Spectrogram";
			$headline="Spectrogram";
			$bodyclass="circle";
			$header_type="header.tpl";
			$footer_type="footer.tpl";
			break;
		}
        case "cf": {


            $transition_list->LoadWithLevels($element_id);
            $transitions=$transition_list->GetItemsArray();

            $max =  $transition_list->LoadMaxIntensity($element_id);

			$max_i = $max;//$max[0]['max_i'];
			$max_e = 0;
			$min_e['l'] = 100000;
			$min_e['u'] = 100000;
			foreach ($transitions as $item) {
				if($item['upper_level_energy'] <= $atom_sys['IONIZATION_POTENCIAL'] and $item['INTENSITY'] > 0) {
					$test = $item['upper_level_termmultiply'] * 1;
					if ($test) {
						$arr_tmp['x'] = $item['upper_level_energy'];
						$arr_tmp['y'] = $item['lower_level_energy'];
						//            $arr_tmp['x'] = $item['LOWER_ENERGY'];
						//            $arr_tmp['y'] = $item['UPPER_ENERGY'] - $item['LOWER_ENERGY'];
						$arr_tmp['bgc'] = ['R' => 0, 'G' => 189, 'B' => 232];

					} else {
						$arr_tmp['x'] = $item['lower_level_energy'];
						$arr_tmp['y'] = $item['upper_level_energy'];
						//            $arr_tmp['x'] = $item['UPPER_ENERGY'] - $item['LOWER_ENERGY'];
						//            $arr_tmp['y'] = $item['LOWER_ENERGY'];
						$arr_tmp['bgc'] = ['R' => 227, 'G' => 30, 'B' => 36];
					}
					//        $arr_tmp['x'] = $item['LOWER_ENERGY'];
					//        $arr_tmp['y'] = $item['UPPER_ENERGY'];
					//        $arr_tmp['i'] = $item['WAVELENGTH'];

					$arr_tmp['intensity'] = $item['INTENSITY'];
					$r = 0;
					$g = 0;
					$b = 0;
					$a = 1;
					//        $a = $item['INTENSITY'] / $max_i;
					if($item['INTENSITY'] < $max_i / 2){
						$r = (int)($item['INTENSITY'] / $max_i * 255 );
						$b = 255;
					}else{
						$r = 255;
						$b = 255 - (int)($item['INTENSITY'] / $max_i) * 255;

					}
					//        $arr_tmp['c'] = ['R' => $color, 'G' => $color, 'B' => $color];

					$arr_tmp['c'] = ['R' => $r, 'G' => $g, 'B' => $b, "A" => $a];
					$config = "{$item['lower_level_config']} - {$item['upper_level_config']}";
					$rg = ['/\@{([1-9])}/i','/\~{(.*?)}/i','/\@{([0])}/i'];
					$rp = ['<sup>$1</sup>','<sub>$1</sub>','&deg;'];
					$arr_tmp['lu'] = iconv('cp1251'
						, 'UTF-8'
						,'CONFIG:' . preg_replace($rg, $rp, $config) . "<br>ENERGY: {$item['lower_level_energy']} - {$item['upper_level_energy']}<br>INTENSITY: {$item['INTENSITY']}<br>WAVELENGTH: {$item['WAVELENGTH']}<br>�������: ".($test ? '' : '��').'������'
					);
					$value_mltrm = $item['lower_level_termprefix'] == $item['upper_level_termprefix'] ? $item['upper_level_termprefix'] * 1 : 'f';
					$value_mltrm = ($value_mltrm > 5) ? 6 : $value_mltrm;

					//        $arr_tmp['pointStyle'] = $pointStyle[$value_mltrm];
					$arr_tmp['pointStyle'] = $value_mltrm;

					$data_for_graph[] = $arr_tmp;

					if($max_e < $item['upper_level_energy']){
						$max_e = $item['upper_level_energy'];
					}
					if($min_e['l'] > $item['lower_level_energy']){
						$min_e['l'] = $item['lower_level_energy'];
					}
					if($min_e['u'] > $item['upper_level_energy']){
						$min_e['u'] = $item['upper_level_energy'];
					}
				}
			}
			//echo '<pre>'.print_r($data_for_graph[0], true).'</pre>';
            //print_r(json_encode($data_for_graph));
            $smarty->assign('dataline', json_encode($data_for_graph));
			$smarty->assign('max_e', $max_e);
			$smarty->assign('min_e', json_encode($min_e));



            // ���� json ������ ���� ���� � ����� ��� � ������
            $spectrum= new Spectrum();
            if (isset($_REQUEST['auto'])){
                $smarty->assign('auto', true);
                $atomNext = new Atom;
                $atomNext->LoadNext($element_id);
                $atomNext_sys = $atomNext->GetAllProperties();
                $smarty->assign('next_element_id', $atomNext_sys['ID']);
            }
            $smarty->assign('spectrum_json',$spectrum->getSpectraSVG($transitions,0,1599900000));

            $level_list = new LevelList;
            $level_list->LoadBase($element_id);
            $levels_array = $level_list->GetItemsArray();
            $smarty->assign('base_level', $levels_array[0]['CONFIG']);

            //��������� ��� ������� � �������� ��������
            $page_type="view_cf.tpl";
            $head="Spectrogram";
            $title="Spectrogram";
            $headline="Spectrogram";
            $bodyclass="cf";
            $header_type="header.tpl";
            $footer_type="footer.tpl";
            break;
        }
	    case "newdiagram": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type="view_new_diagram.tpl"; 
    		$head="Grotrian_Charts";
    		$title="Grotrian_Charts";
    		$headline="Atomic_charts";
    		$bodyclass="new_diagram";
    		$header_type="top_header.tpl";
    		$footer_type="bottom_footer.tpl";
    		break;
    	}

    	default: {
			header("HTTP/1.0 404 Not Found");
			exit;
    		//������
    		$level_list = new LevelList;
    		// ����� � ������ ����� �������
    		$level_count = $level_list->LoadCount();
    		$smarty->assign('level_count', $level_count);
    		
    		
    		//��������
    		$transition_list = new TransitionList;
    		// ����� � ������ ����� ���������
    		$transition_count = $transition_list->LoadCount();
    		$smarty->assign('transition_count', $transition_count);
    		
    		$page_type=$l10n->locale."/index.tpl"; 
    		$head="Information_system_Electronic_structure_of_atoms";
    		$title="About_project";
    		$headline="About_project";
    		$bodyclass="index"; 
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
	} else
	//���� ��� ���������� �� ��������
	switch ($pagetype) {
		case "stats": {

			$atom_list = new AtomList;

			$atom_count = $atom_list->LoadCountByIonization();
			echo "����� ������� ������ �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonization(0);
			echo "����������� ������ �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonization(0, ">");
			echo "����� �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithLevels();
			echo "������� ������ � �������� �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithLevels(0);
			echo "����������� ������ � ��������  �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithLevels(0, ">");
			echo "����� ������� � ��������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithTransitions();
			echo "������� ������ � ���������� �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithTransitions(0);
			echo "����������� ������ � ����������  �������: " . $atom_count . "<br>\r\n";

			$atom_count = $atom_list->LoadCountByIonizationWithTransitions(0, ">");
			echo "����� ������� � ����������: " . $atom_count . "<br>\r\n";

			$level_list = new LevelList;
			$level_count = $level_list->LoadCountByIonization();
			echo "����� �������: " . $level_count . "<br>\r\n";

			$level_count = $level_list->LoadCountByIonization(0);
			echo "������� ����������� ������: " . $level_count . "<br>\r\n";

			$level_count = $level_list->LoadCountByIonization(0, ">");
			echo "������� �����: " . $level_count . "<br>\r\n";

			$level_count = $level_list->LoadClassifiedCountByIonization();
			echo "����� ������������������ �������: " . $level_count . "<br>\r\n";

			$level_count = $level_list->LoadClassifiedCountByIonization(0);
			echo "������������������ ������� ����������� ������: " . $level_count . "<br>\r\n";

			$level_count = $level_list->LoadClassifiedCountByIonization(0, ">");
			echo "������������������ ������� �����: " . $level_count . "<br>\r\n";

			$transition_list = new TransitionList;
			$transition_count = $transition_list->LoadCountByIonization();
			echo "����� ���������: " . $transition_count . "<br>\r\n";

			$transition_count = $transition_list->LoadCountByIonization(0);
			echo "��������� ����������� ������: " . $transition_count . "<br>\r\n";

			$transition_count = $transition_list->LoadCountByIonization(0, ">");
			echo "��������� �����: " . $transition_count . "<br>\r\n";

			$transition_count = $transition_list->LoadClassifiedCountByIonization();
			echo "����� ������������������ ���������: " . $transition_count . "<br>\r\n";

			$transition_count = $transition_list->LoadClassifiedCountByIonization(0);
			echo "������������������ ��������� ����������� ������: " . $transition_count . "<br>\r\n";

			$transition_count = $transition_list->LoadClassifiedCountByIonization(0, ">");
			echo "������������������ ��������� �����: " . $transition_count . "<br>\r\n";

			exit;
			break;
		}

		case "index": {
	    	
	    	//������
	    	$level_list = new LevelList;
	    	// ����� � ������ ����� �������
	    	$level_count = $level_list->LoadCount();
	    	$smarty->assign('level_count', $level_count);
	    	
	    	
	    	//��������
	    	$transition_list = new TransitionList;
	    	// ����� � ������ ����� ���������
	    	$transition_count = $transition_list->LoadCount();
	    	$smarty->assign('transition_count', $transition_count);
	    	
    		$page_type=$l10n->locale."/index.tpl"; 
    		$head="Information_system_Electronic_structure_of_atoms";
    		$title="About_project";
    		$headline="About_project";
    		$bodyclass="index"; 
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
    	
		case "links": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type=$l10n->locale."/links.tpl";
    		$head="Other_resources_for_atomic_spectroscopy";
    		$title="Other_resources_for_atomic_spectroscopy";
    		$headline="Other_resources_for_atomic_spectroscopy";
    		$bodyclass="links";
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
    	
		case "team": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type=$l10n->locale."/team.tpl";
    		$head="Project_team";
    		$title="Project_team";
    		$headline="Project_team";
    		$bodyclass="team";
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
    	
    	
		case "sponsors": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type=$l10n->locale."/sponsors.tpl";
    		$head="Sponsors";
    		$title="Sponsors";
    		$headline="Sponsors";
    		$bodyclass="sponsors";
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
    	
		case "awards": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type=$l10n->locale."/awards.tpl";
    		$head="Awards";
    		$title="Awards";
    		$headline="Awards";
    		$bodyclass="awards";
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}   

    	case "periodictable": {
    		//��������� ��� ������� � �������� ��������    	
    			
			$page_type="view_periodictable.tpl";
    		$head="Periodic_Table";
    		$title="Periodic_Table";
    		$headline="Periodic_Table";
    		$bodyclass="periodictable";
    		$header_type="header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	} 

		case "login": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type="login.tpl";
    		$head="Information_system_Electronic_structure_of_atoms";
    		$title="About_project";
    		$headline="About_project";
    		$bodyclass="index"; 
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}

		case "logout": {
			session_start();
			session_destroy();
			header("Location: /");
			break;
		}

		case "bibliography": {	
			$source_list = new SourceList;	
			
			if(isset($_REQUEST['element_id']) && is_numeric($_REQUEST["element_id"])){
				$source_id=$_REQUEST["element_id"];

				$source_list->Load($source_id);			
				$BiblioItem = $source_list->GetItemsArray();
				$smarty->assign('BiblioItem',$BiblioItem[0]);	
				$source_list->GetAuthors($source_id);
				$smarty->assign('Authors',$source_list->GetItemsArray());				
				$page_type="view_bibliolink.tpl"; 
			} else {		
				
				//$source_list->LoadAll();
				$smarty->assign('SourceList',$source_list->GetItemsArray());   		
				$page_type="view_bibliography.tpl"; 
    			$head="Bibliography";
    			$title="Bibliography";
    			$headline="Bibliography";
    			$bodyclass="bibliography";
    			$header_type="header.tpl";
    			$footer_type="footer.tpl";
			}
    		break;
    	}  	
    	
		case "articles": {
    		//��������� ��� ������� � �������� ��������    		
			$page_type=$l10n->locale."/articles.tpl"; 
    		$head="Articles";
    		$title="Articles";
    		$headline="Articles";
    		$bodyclass="index";
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
			
    		if(isset($_REQUEST['element_id']) && is_numeric($_REQUEST["element_id"])){
    			$page_type=$l10n->locale."/articles/".$_REQUEST["element_id"].".tpl";
				if (!empty($page_type)){ 
    				$header_type="index_header.tpl";
    				$footer_type="footer.tpl";

    				if($_REQUEST["element_id"]>2)
					{
						header("HTTP/1.0 404 Not Found");
						exit;
						header('location: /'.$l10n->locale.'/articles');
					}
				}
			}
    		break;
    	}

		default: {
			header("HTTP/1.0 404 Not Found");
			exit;
			//������
			$level_list = new LevelList;
			// ����� � ������ ����� �������
			$level_count = $level_list->LoadCount();
			$smarty->assign('level_count', $level_count);
			
			
			//��������
			$transition_list = new TransitionList;
			// ����� � ������ ����� ���������
			$transition_count = $transition_list->LoadCount();
			$smarty->assign('transition_count', $transition_count);
			
    		$page_type=$l10n->locale."/index.tpl"; 
    		$head="Information_system_Electronic_structure_of_atoms";
    		$title="About_project";
    		$headline="About_project";
    		$bodyclass="index"; 
    		$header_type="index_header.tpl";
    		$footer_type="footer.tpl";
    		break;
    	}
	}		
		$localDictionary=$l10n->localize;
		//������������ ������� �������� ���������� ����� � ������� ��� �����������
		$smarty->register_modifier("toRoman","numberToRoman");
		
		$smarty->assign('interface',$l10n->interface);
		$smarty->assign('locale',$l10n->locale);		
		$smarty->assign('l10n',$localDictionary);

		$smarty->assign('cur_en_date', date("F j, Y"));
		$smarty->assign('cur_year', date("Y"));

		if(isset($head))$smarty->assign('head',$localDictionary[$head]);
		// var_dump($localDictionary);
		//if(isset($title))$smarty->assign("title",$localDictionary[$title]);
		// var_dump($title . '_title');
		if(isset($title))$smarty->assign("title",$localDictionary[$title . '_title'] . (isset($elname)?(" � ". $atom_name):("")));
		
		if(isset($headline))$smarty->assign('headline',$localDictionary[$headline]);		
		
		if (isset($element_id)) $smarty->assign('layout_element_id',$element_id);	
		if (isset($elname)) $smarty->assign('layout_element_name',$elname);
		if (isset($elname)) $smarty->assign('atom_name', $atom_name);
		if (isset($ions)) $smarty->assign('ions',$ions);
		
		if (isset($bodyclass))	$smarty->assign("bodyclass",$bodyclass);
		if (isset($pagetype))	$smarty->assign("pagetype",$pagetype);	
		
				
		if(isset($header_type)) $smarty->display("$interface/".$header_type);

		switch ($pagetype) {
			case 'diagram':
			case 'spectrum':
			case 'compare':
            case 'circle':
            case 'cf':
			case 'links':
			case 'team':
			case 'sponsors':
			case 'awards':
			case 'articles':
			case 'periodictable':
			case 'index':
				$smarty->display("view/".$page_type);
				break;
			case 'element':
			case 'levels':
			case 'transitions':
			case 'bibliography':
			default:
				$smarty->display("$interface/".$page_type);
		}



		//print_r($_REQUEST);
		if(isset($footer_type)) $smarty->display("$interface/".$footer_type);
	}
?>
