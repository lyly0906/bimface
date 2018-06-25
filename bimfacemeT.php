<?php
//error_reporting(E_ALL);

header('Content-Type: application/json');   
header('Content-Type: text/html;charset=utf-8'); 
header("Access-Control-Allow-Origin: *");
$appkey = 'MLar06b234TIv6pOQzMMidIBDhlUXGpW';
$appsecret = 'OdNJlWsTOKFLpoYipRTCNlGRJeushzqe';
$accessTokenUrl = 'https://api.bimface.com/oauth2/token';
$compareStatusUrl = 'https://api.bimface.com/compare';
$viewTokenUrl = 'https://api.bimface.com/view/token';
$metaDataUrl = 'https://file.bimface.com/metadata';
$compareDataUrl = 'https://api.bimface.com/data/compare?compareId=';
$elementDataUrl = 'https://api.bimface.com/data/element/id?fileId=';
$propertyDataUrl = 'https://api.bimface.com/data/element/property';

$thingshubTokenUrl = 'http://192.168.1.120:9990/api/auth/oauth/token';
$thingshubUrl = 'http://192.168.1.174:9990/api/bim/details/details';
$thingshumDeviceUrl = 'http://192.168.1.174:9990/api/bim/details/getTskvs';

$logindata = array(
'username'=>'bguanliyuan01', 
'password'=>'123456', 
'grant_type'=>'password', 
'scope'=>'server'); 

$postdata = http_build_query($logindata);
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-type:application/x-www-form-urlencoded\r\n".
					"Authorization:Basic dGhpbmdzaHViOnRoaW5nc2h1Yg==",
        'content' => $postdata
    )
);


$logincontext = stream_context_create($options);


$token = array('http' => array('header' => "Authorization: Basic ".base64_encode($appkey.':'.$appsecret),'method' => 'POST'));
$thingstoken = array('http' => array('header' => "X-Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI1ODkyNjMzMEBxcS5jb20iLCJzY29wZXMiOlsiVEVOQU5UX0FETUlOIl0sInVzZXJJZCI6IjNmZWIyMzQwLWRmMDMtMTFlNy04MWI1LTI1NWI1MWNhZTRjMSIsImVuYWJsZWQiOnRydWUsImlzUHVibGljIjpmYWxzZSwidGVuYW50SWQiOiI0ZWRhZWJlMC1jMmM3LTExZTctYTFiZS1mYjkwNWY1OTMxNmQiLCJjdXN0b21lcklkIjoiMTM4MTQwMDAtMWRkMi0xMWIyLTgwODAtODA4MDgwODA4MDgwIiwiaXNzIjoidGhpbmdzYm9hcmQuaW8iLCJpYXQiOjE1MjgyNTUyOTgsImV4cCI6MTUzNzI1NTI5OH0.KieTMjFNxaQGLgF2NtIkR3N65hipR9viOM2HymaRNJS0eoNaDwbZkppHbyA7T-HTX8uirwGTRsBQIPVS_smy9A"));

$thingshub = array('http' => array('header' => "Authorization: Bearer ".tokenThingshubOk($thingshubTokenUrl,false,$logincontext)));

$thingsCustomerUrl = 'http://www.ulyncthings.com:8080/api/tenant/devices?limit=100&textSearch=';

$tokencontext = stream_context_create($token);
$thingsToken = stream_context_create($thingstoken);
$thingsHub = stream_context_create($thingshub);

$func = $_REQUEST['fun'];
$whereFile = $_REQUEST['file'] ? $_REQUEST['file'] : null;
$thingsTeleUrl = 'http://www.ulyncthings.com:8080/api/plugins/telemetry/DEVICE/'.$whereFile.'/values/timeseries?keys=';

$fileId = $_REQUEST['id'] ? $_REQUEST['id'] : null;
$callback = $_REQUEST['callback'];
$objectId = $_REQUEST['objectId'] ? $_REQUEST['objectId'] : null;
$data = $_REQUEST['data'] ? $_REQUEST['data'] : null;

$hdrs = array('http' => array('header' => "Authorization: bearer ".tokenOk($accessTokenUrl,$callback,$tokencontext)));
$context = stream_context_create($hdrs);
switch($func){
	
	case 'backBimStatus':
		call_user_func_array($func,array($compareStatusUrl,$compareId,$context));
		break;
	case 'backFollowViewToken':
		call_user_func_array($func,array($viewTokenUrl,$whereFile,$fileId,$context,$callback));
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
	case 'thingsTele':
		call_user_func_array($func,array($thingsTeleUrl,$callback,$fileId,$thingsToken));
		break;
	case 'backFile':
		call_user_func_array($func,array($fileId,$db));
		break;
	case 'backFileObject':
		call_user_func_array($func,array($elementDataUrl,$fileId,$db,$context,$propertyDataUrl));
		break;
	case 'backFileObjectMysql':
		call_user_func_array($func,array($objectId,$fileId,$db,$context,$propertyDataUrl,$callback));
		break;
	case 'backFileObjectIn':
		call_user_func_array($func,array($elementDataUrl,$fileId,$db,$context,$callback));
		break;
	case 'backThingsDevices':
		call_user_func_array($func,array($thingsCustomerUrl,$callback,$thingsToken));
		break;  
	case 'saveThingsDevices':
		call_user_func_array($func,array($data,$callback,$objectId,$fileId,$db));
		break;
	case 'backBimDevices':
		call_user_func_array($func,array($callback,$objectId,$fileId,$db));
		break;
    case 'thingsHubFile':
		call_user_func_array($func,array($thingshubUrl,$callback,$fileId,$thingsHub));
		break;
    case 'thingsHubDevice':
		call_user_func_array($func,array($thingshumDeviceUrl,$callback,$fileId,$objectId,$thingsHub));
		break;		
}
function thingsHubDevice($thingshumDeviceUrl,$callback,$fileId,$objectId,$thingsHub){
	$x = file_get_contents($thingshumDeviceUrl.'?fileId='.$fileId.'&objectId='.$objectId, 0, $thingsHub);
	//$arr = json_decode($x,true);
	echo $callback.'('.$x.')';		
}

function thingsHubFile($thingshubUrl,$callback,$fileId,$thingsHub){
	$x = file_get_contents($thingshubUrl.'?fileId='.$fileId, 0, $thingsHub);
	//$arr = json_decode($x,true);
	echo $callback.'('.$x.')';	
}

function backBimDevices($callback,$objectId,$fileId,$db){
         $res = [];
	$_fileId = backFile($fileId,$db);
                $_objectId = backObjectId($_fileId,$objectId,$db);
                $sql = "select GROUP_CONCAT(keyId SEPARATOR ',') as keyids,clientId,bimobject2client.`name`,objectJson from bimobject2client left join bimfile2object ON bimobject2client.b2oId = bimfile2object.b2oId where bimobject2client.b2oId = ".$_objectId." group by clientId";
                $arr = $db->getAll($sql);
	       if($arr){
			foreach($arr as $k=>$r){
				$res['objectJson'] = json_decode(base64_decode($r['objectJson']),true);
                                		$res['data'][] = array('keyids'=>$r['keyids'],'clientId'=>$r['clientId'],'name'=>$r['name']);
                           		 }
                		$res['code'] = 'success';
		}else{
			$res['code'] = 'error';
		}
	       
                echo $callback.'('.json_encode($res).')';
}

function saveThingsDevices($data,$callback,$objectId,$fileId,$db){
	$arr = json_decode($data,true);
	$_fileId = backFile($fileId,$db);
                $_objectId = backObjectId($_fileId,$objectId,$db);
	$re = [];
                foreach($arr as $r){
                                $clientId = explode('|',$r)[0];
                                $key =  explode('|',$r)[1];
                                $name = explode('|',$r)[2];
		$sql = "select * from bimobject2client where b2oId=".$_objectId." and clientId='".$clientId."' and keyId='".$key."'";
                                $res = $db->getRow($sql);
		if(count($res) == 0){
			$insert_data = ['b2oId'=>$_objectId,'clientId'=>$clientId,'keyId'=>$key,'name'=>$name];
                                                 $res = $db->insert('bimobject2client',$insert_data);
                                }
                }
                $re['code'] = 'success';
                $re['message'] = '';
                $re['data'] = '';
 	echo $callback.'('.json_encode($re).')';
}

function backThingsDevices($thingsCustomerUrl,$callback,$thingsToken){
	$x = file_get_contents($thingsCustomerUrl, 0, $thingsToken);
	$arr = json_decode($x,true);
                $re = [];
             
                foreach($arr['data'] as $k=>$r){
	       $result['id'] = $r['id']['id'];
                       $result['name'] = $r['name'];
                       $devicesKey = file_get_contents('http://www.ulyncthings.com:8080/api/plugins/telemetry/DEVICE/'. $r['id']['id'] .'/keys/timeseries', 0, $thingsToken);
                       $result['key'] = json_decode($devicesKey,true);
                       $res[] = $result;
	}
                $re['code'] = 'success';
                $re['message'] = '';
                $re['data'] = $res;                
	echo $callback.'('.json_encode($re).')';
}

function backFile($fileId,$db){
	$sql = "select * from bimfile where fileId='".$fileId."'";
                 $res = $db->getRow($sql);
                 $result = [];
                 if(count($res) == 0){
	      $insert_data = ['fileId'=>$fileId];
                      $res = $db->insert('bimfile',$insert_data);
                      $msg = $res;
	 }else{
	      $msg = $res['id'];
                 }
          return $msg;
}

function backFileObjectIn($elementDataUrl,$fileId,$db,$content,$callback){
                $_fileId = backFile($fileId,$db);
	$x = file_get_contents($elementDataUrl.$fileId, 0, $content);
                $arr = json_decode($x,true);
	foreach($arr['data'] as $r){
		$sql = "select * from bim2object where fileId='".$_fileId."' and objectId='".$r."'";
                                $res = $db->getRow($sql);
                                if(count($res) == 0){
			$insert_data = ['fileId'=>$_fileId,'objectId'=>$r];
                                                $res = $db->insert('bim2object',$insert_data);
                                }
                }
                echo $callback.'('.$x.')';
}

function backFileObject($elementDataUrl,$fileId,$db,$content,$propertyDataUrl){
                $_fileId = backFile($fileId,$db);
	$x = file_get_contents($elementDataUrl.$fileId, 0, $content);
                $arr = json_decode($x,true);
                foreach($arr['data'] as $r){
		$sql = "select * from bimfile2object where fileId='".$_fileId."' and objectId='".$r."'";
                                $res = $db->getRow($sql);
                                 if(count($res) == 0){
                                      $objectStr = file_get_contents($propertyDataUrl.'?fileId='.$fileId.'&elementId='.$r, 0, $content);
                                      $objectArr = json_decode($objectStr,true);
                                      $insert_data = ['fileId'=>$_fileId,'objectId'=>$r,'name'=>$objectArr['data']['name'],'objectJson'=>$objectStr];
                                      $res = $db->insert('bimfile2object',$insert_data);
                                  }
                }
	$result['code'] = 'success';
                 $result['message'] = $msg;
                 $result['data'] = null;
                 echo json_encode($result);
}

function backObjectId($_fileId,$objectId,$db){
	$sql = "select * from bim2object where fileId=".$_fileId." and objectId='".$objectId."'";
                $res = $db->getRow($sql);
                return $res['id'];
}

function backFileObjectMysql($objectId,$fileId,$db,$content,$propertyDataUrl,$callback){
                $_fileId = backFile($fileId,$db);
	       $sql = "select bf.*,bo.* from bimfile2object bf left join bim2object bo on bf.b2oId = bo.id where bo.fileId=".$_fileId." and bo.objectId='".$objectId."'";
                $_objectId = backObjectId($_fileId,$objectId,$db);
                $res = $db->getRow($sql);
                $result = base64_decode($res['objectJson']);
                                 if(count($res) == 0){
                                      $objectStr = file_get_contents($propertyDataUrl.'?fileId='.$fileId.'&elementId='.$objectId, 0, $content);
                                      $objectArr = json_decode($objectStr,true);
                                      $insert_data = ['b2oId'=>$_objectId,'name'=>$objectArr['data']['name'],'objectJson'=>base64_encode($objectStr)];
                                      $res = $db->insert('bimfile2object',$insert_data);
                                      $result = $objectStr;
                                  }
         
         echo $callback.'('.$result.')';
}


function thingsTele($thingsTeleUrl,$callback,$fileId,$thingsToken){
	$x = file_get_contents($thingsTeleUrl.$fileId, 0, $thingsToken);
	//$arr = json_decode($x,true);
	echo $callback.'('.$x.')';
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

function tokenThingshubOk($thingshubTokenUrl, $callback, $logincontext){
	$result = file_get_contents($thingshubTokenUrl, false, $logincontext);
	$arr = json_decode($result,true);
	return $arr['access_token'];
}

function backBimStatus($compareStatusUrl,$compareId,$context){
	$x = file_get_contents($compareStatusUrl.'?compareId='.$compareId, 0, $context);
	//$arr = json_decode($x,true);
	echo $x;
}

function backFollowViewToken($viewTokenUrl,$whereFile,$fileId,$context,$callback){
	$x = file_get_contents($viewTokenUrl.'?'.$whereFile.'='.$fileId, 0, $context);
	echo $callback.'('.$x.')';
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