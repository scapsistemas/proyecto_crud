<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use \Statickidz\GoogleTranslate;
use Zend\View\Model\JsonModel;


class IndexController extends AbstractActionController
{

    public function indexAction()
    {       
        $cmd='curl -v -H "Accept: application/json" -H "Content-type: application/json" -X GET  https://app.alegra.com/api/v1/items/ -u "scap_sistemas@hotmail.com:629f616b6d6d0fd06171"';
        exec($cmd,$result);
        $cadena = implode($result);
        $cadenamod='{"productos":'. $cadena . '}';          
        //echo $cadenamod;
        $cantprod=json_decode($cadena, true); 
        $productos=json_decode($cadenamod, true); 
                     
///traduce español a ingles
$source = 'es';
$target = 'en';
$trans = new GoogleTranslate();

for ($x=0;$x<count($cantprod); $x++){
         $nombre_espa=$productos['productos'][$x]['name'];         
         $descrip_espa=$productos['productos'][$x]['description'];     
         $nombre_ingles = $trans->translate($source, $target, $nombre_espa);             
         
         $cadena=str_replace($nombre_espa, $nombre_ingles, $cadena);
     
         $descrip_ingles = $trans->translate($source, $target, $descrip_espa);             
         $cadena=str_replace($descrip_espa, $descrip_ingles, $cadena);
     }
    
     $cadena=str_replace(' ', '~', $cadena);
     $result = explode(' ', $cadena); 
     
    //Calcular Moneda
     $url = "http://www.apilayer.net/api/live?access_key=2ee955be1b2748659092a746ab7eff9e";
     $resultdolar = file_get_contents($url);    
      $decode=json_decode($resultdolar, true);  
    //echo count($decode);
 
 
     $resultdolar='[{"USDCOP":'. $decode['quotes']['USDCOP'] . '}]';     
     $resultdolar = explode(' ', $resultdolar); 

    
     return new ViewModel(
         array('Productos' => $result,                
                'dolar' => $resultdolar
             )                                
       );
}    
        public function deleteAction()
    {        
       $id  = (int) $this->params()->fromRoute("id",0);                
       $cmd='curl -v -H "Accept: application/json" -H "Content-type: application/json" -X DELETE  https://app.alegra.com/api/v1/items/' . $id .  ' -u "scap_sistemas@hotmail.com:629f616b6d6d0fd06171"';
       exec($cmd,$result);      
    }
     
    public function modiAction()
    {          
   $id  = (int) $this->params()->fromRoute("id",0);
   
                if ($this->getRequest()->isPost()){                    
                     $ref=$this->request->getPost('ref');
                     $name=$this->request->getPost('name'); 
                     $description=$this->request->getPost('description'); 
                     $price=$this->request->getPost('price'); 
                     $status=$this->request->getPost('status'); 
                }
    //Traduce de ingles a español y guarda en Alegra
     $source = 'en';
     $target = 'es';
     $trans = new GoogleTranslate();
     $name = $trans->translate($source, $target, $name);    
     $description = $trans->translate($source, $target, $description);
                                             
       $cmd='curl -v -H "Accept: application/json" -d "{\"reference\": \"'. $ref . '\", \"name\": \"'. $name .'\",\"description\": \"'. $description .'\", \"price\": \"' . $price .'\",\"status\": \"' . $status . '\"}" -X PUT https://app.alegra.com/api/v1/items/' . $id. ' -u "scap_sistemas@hotmail.com:629f616b6d6d0fd06171"';                         
       exec($cmd,$result);
    }


    public function insertaAction()
    {           
if ($this->getRequest()->isPost()){
                 $ref=$this->request->getPost('ref');
                 $name=$this->request->getPost('name'); 
                 $description=$this->request->getPost('description'); 
                 $price=$this->request->getPost('price'); 
                 $status=$this->request->getPost('status'); 
                }
                                     
    //Traduce de ingles a español
     $source = 'en';
     $target = 'es';
     $trans = new GoogleTranslate();
     $name = $trans->translate($source, $target, $name);      
     $cmd='curl -v -H "Accept: application/json" -d "{\"reference\": \"'. $ref . '\",\"name\": \"'. $name .'\",\"description\": \"'. $description .'\",\"status\": \"' . $status . '\", \"price\": \"' . $price . '\"}" -X POST https://app.alegra.com/api/v1/items -u "scap_sistemas@hotmail.com:629f616b6d6d0fd06171"';                         
     exec($cmd,$result);             
    }

   
    
    
public function traduceAction()
{   
     //require_once ('vendor/autoload.php');
     $source = 'es';
     $target = 'en';
     $text = 'mundo fantastico';
     $trans = new GoogleTranslate();
     $textIngles = $trans->translate($source, $target, $text);    
     
    // echo $textIngles;
     $result = new JsonModel(array(
         'success'=>true,     
         'resultado'=>$textIngles,    
     ));
     return $result;
}      
}
