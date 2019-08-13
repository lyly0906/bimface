<?php
error_reporting(E_ALL);
header('Content-Type: application/json');   
header('Content-Type: text/html;charset=utf-8'); 
header("Access-Control-Allow-Origin: *");
$appkey = ''; //'';
$appsecret = ''; //'';
$accessTokenUrl = 'https://api.bimface.com/oauth2/token';
$compareStatusUrl = 'https://api.bimface.com/compare';
$viewTokenUrl = 'https://api.bimface.com/view/token';
$metaDataUrl = 'https://file.bimface.com/metadata';
$compareDataUrl = 'https://api.bimface.com/data/compare?compareId=';
$token = array('http' => array('header' => "Authorization: Basic ".base64_encode($appkey.':'.$appsecret),'method' => 'POST'));



$tokencontext = stream_context_create($token);
$func = $_REQUEST['fun'];
$whereFile = $_REQUEST['file'] ? $_REQUEST['file'] : null;
$fileId = $_REQUEST['id'] ? $_REQUEST['id'] : null;
$callback = $_REQUEST['callback'];

$hdrs = array('http' => array('header' => "Authorization: bearer ".tokenOk($accessTokenUrl,$callback,$tokencontext)));
$context = stream_context_create($hdrs);
switch($func){
	
	case 'backBimStatus':
		call_user_func_array($func,array($compareStatusUrl,$compareId,$context));
		break;
	case 'backFollowViewToken':
		call_user_func_array($func,array($viewTokenUrl,$whereFile,$fileId,$context));
		break;
	case 'backMetaData':
		call_user_func_array($func,array($metaDataUrl,$fileId,$context));
		break;
	case 'token':
		call_user_func_array($func,array($accessTokenUrl,$callback,$tokencontext));
		break;
	case 'compareData':
		call_user_func_array($func,array($compareDataUrl,$callback,$fileId,$context));
		break;
}

function token($accessTokenUrl,$callback,$tokencontext){
	$x = file_get_contents($accessTokenUrl, 0, $tokencontext);
	//$arr = json_decode($x,true);
	echo $callback.'('.$x.')';
}

function tokenOk($accessTokenUrl,$callback,$tokencontext){
	$x = file_get_contents($accessTokenUrl, 0, $tokencontext);
	$arr = json_decode($x,true);
	return $arr['data']['token'];
}

function backBimStatus($compareStatusUrl,$compareId,$context){
	$x = file_get_contents($compareStatusUrl.'?compareId='.$compareId, 0, $context);
	//$arr = json_decode($x,true);
	echo $x;
}

function backFollowViewToken($viewTokenUrl,$whereFile,$fileId,$context){
	$x = file_get_contents($viewTokenUrl.'?'.$whereFile.'='.$fileId, 0, $context);
	echo $x;
}

function backMetaData($metaDataUrl,$fileId,$context){
	$x = file_get_contents($metaDataUrl.'?fileId='.$fileId, 0, $context);
	echo $x;
}

function compareData($compareDataUrl,$callback,$fileId,$context){
	$x = file_get_contents($compareDataUrl.$fileId, 0, $context);
	$x = json_decode($x,true);
	$result = array();
	foreach ($x['data'][0]['categories'] as $key=>$val){
		$categoryId = $val['categoryId'];
		$categoryName = $val['categoryName'];
		$elements = $val['elements'];
		foreach($elements as $k=>$v){
			$v['categoryId'] = $categoryId;
			$v['categoryName'] = $categoryName;
			$result[] = $v;
		}
	}

	$r['newItems'] = array();
	$r['deleteItems'] = array();
	$r['changeItems'] = array();
	foreach ($result as $ky=>$vy){
		if($vy['diffType']=='NEW'){
			$r['newItems'][]=$vy;
		}else if($vy['diffType']=='DELETE'){
			$r['deleteItems'][]=$vy;
		}else if($vy['diffType']=='CHANGE'){
			$r['changeItems'][]=$vy;
		}
	}

	$r['newItems'] = array_val_chunk($r['newItems']);

	$r['deleteItems'] = array_val_chunk($r['deleteItems']);
	$r['changeItems'] = array_val_chunk($r['changeItems']);
	$data = array('changeItems'=>$r['changeItems'],'deleteItems'=>$r['deleteItems'],'newItems'=>$r['newItems']);
	$info = array("code"=>'success',"message"=>'','data'=>$data);
	echo $callback.'('.json_encode($info).')';
}

function array_val_chunk($array){
    $result = array();
    foreach ($array as $key => $value) {
        $result[$value['categoryId'].'#'.$value['categoryName']][] = $value;
    }
    $res_rt = array();
    $total = 0;
    foreach ($result as $k=>$v){
        $res = array();
        $name = explode("#",$k);
        $res['categoryId'] = $name[0];
        $res['categoryName'] = $name[1];
        foreach ($v as $kv=>$vv){
            unset($vv['categoryId']);
            unset($vv['categoryName']);
            $res['elements'][] = $vv;
        }
        $total = $total + count($res['elements']);
        $res['itemCount']=count($res['elements']);
        array_push($res_rt,$res);

    }
    $rst['categories'] = $res_rt;
    $rst['itemCount'] = $total;
    $rst['specialtyId'] = '';
    $rst['specialtyName'] = '';
    return $rst;
}


/*$x = file_get_contents('https://api.bimface.com/data/compare?compareId=1309486452802496', 0, $context);
$arr = json_decode($x,true);
foreach($arr['data'] as $k=>$r){
	foreach($r['categories'] as $kk=>$rr){
		echo $rr['categoryName'].'<br>';
		foreach($rr['elements'] as $kkk=>$rrr){
			echo '&nbsp;'.$rrr['diffType'].':';
			echo '&nbsp;'.$rrr['name'].'<br>';
		}
	}
}	*/
	
?>
