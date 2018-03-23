<?php
include '../../config/config.inc.php';
include '../../init.php';
if (!defined('_PS_VERSION_'))
    exit;
    $actions = isset($_POST["actions"]) ? $_POST["actions"] : "getP";
    if($actions == "getP"){
$category = new Category(Context::getContext()->shop->getCategory(),(int)Context::getContext()->language->id);
$nb = 10000;
$products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 10));
?>
<!-- Modal -->
<div id="productsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Aggiungi un Prodotto al tuo Outfit</h4>
      </div>
      <div class="modal-body">
        <h2>Scegli il prodotto da questa lista</h2>
        <table class="table table-bordered">
           <thead>
             <tr>
               <th>ID</th>
               <th>Immagine</th>
               <th>Nome</th>
               <th>Azioni</th>
             </tr>
           </thead>
           <tbody>
             <?php
             foreach($products as $prodotto){
                $image = Product::getCover((int)$prodotto["id_product"]);
                $image = new Image($image['id_image']);
                $imagePath = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath()."-medium_default.jpg";
               ?>
               <tr <?php if(in_array($prodotto["id_product"],$_POST["ids"])){
                  ?>
                  class="was_added"
                  <?php
               }
               ?>
               >
                 <td><?=$prodotto["id_product"]?></td>
                 <td class="product_image"><img src="<?=$imagePath?>" style="width:100px;" /></td>
                 <td class="product_name"><?=$prodotto["name"]?></td>
                 <td><button type="button" class="btn btn-success" data-product-id="<?=$prodotto["id_product"]?>">Aggiungi</button></td>
               </tr>
               <?php
             }
              ?>
           </tbody>
         </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
      </div>
    </div>

  </div>
</div>
<script>
$(document).ready(function(){
  if ( $.fn.dataTable.isDataTable( '#productsModal table' ) ) {
    table = $('#example').DataTable();
}
else {
    table = $('#productsModal table').DataTable({
      "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json"
        }
    });
}
});
</script>
<?php
}else if($actions == "saveOutfit"){
  function genId(){
    return rand(1111111111,9999999999);
  }
  $imagefrom = str_replace("data:image/jpeg;base64,","",$_POST["image"],$repOc);
  $outfit_id = isset($_POST["outfit_id"]) ? $_POST["outfit_id"] : genId();
  $points = $_POST["points"];
  $weekoutfit_name = $_POST["weekoutfit_name"];
  $weekoutfit_desc = $_POST["weekoutfit_desc"];
  foreach($points as $key=>$point){
    $info = new Product((int)$point["product_id"]);
    $name = $info->name;
    $image = Product::getCover((int)$point["product_id"]);
    $image = new Image($image['id_image']);
    $imagePath = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath()."-home_default.jpg";
    $price = $info->price;
    $link = $info->getLink();
    $points[$key]["product_name"] = $name[1];
    $points[$key]["product_image"] = $imagePath;
    $points[$key]["product_price"] = $price;
    $points[$key]["product_link"] = $link;
  }
    if($repOc != 0){
  $imageData = base64_decode($imagefrom);
  $source = imagecreatefromstring($imageData);
  $imageName = rand(111111111111,999999999999999).".jpg";
  $imageNameDestination = "../../img/".$imageName;
  imagejpeg($source,$imageNameDestination,70);
  }else{
    $imageName = str_replace(Context::getContext()->shop->getBaseURL(true)."img/","",$_POST["image"]);
  }
  if(!empty(Configuration::get('PS_WEEKOUTFIT'))){
    $outfits = json_decode(Configuration::get('PS_WEEKOUTFIT'));
  }else{
    $outfits = [];
  }
  $outfit = ["outfit_id"=>$outfit_id,"image"=>$imageName,"weekoutfit_name"=>$weekoutfit_name,"weekoutfit_desc"=>$weekoutfit_desc,"points"=>$points];
  array_push($outfits,$outfit);
  $encoded = json_encode($outfits);
  if(Configuration::updateValue('PS_WEEKOUTFIT', $encoded)){
    imagedestroy($source);
    echo "1";
  }else{
    echo "0";
  }
}else if($actions == "deleteOutfit"){
  $id = $_POST["id"];
  $outfits =  json_decode(Configuration::get('PS_WEEKOUTFIT'));
  for($i=0;$i<count($outfits);$i++){
    if($outfits[$i]->outfit_id == $id){
      unset($outfits[$i]);
      $outfits = array_values($outfits);
      $encoded = json_encode($outfits);
      if(Configuration::updateValue('PS_WEEKOUTFIT', $encoded)){
        echo "1";
      }else{
        echo "0";
      }
    }
  }
}else if($actions == "loadCurrentOutfit"){
  $currentOutfit = Configuration::get('PS_WEEKOUTFIT');
 if(isset($currentOutfit) && !empty($currentOutfit)){
   $decoded = json_decode($currentOutfit);
   $htmlstruct = "";
   $pointstruct = "";
   $progr = 1;
   $outfit_id = $decoded->outfit_id;
   $outfit_number = count($decoded);
   ?>
   <div class="row" style="margin-bottom:20px;">
     <button type="button" class="btn btn-success pull-right addNewOutfit">
     AGGIUNGI NUOVO OUTFIT
     </button>
   </div>
   <table class="table table-bordered">
      <thead>
        <tr>
          <th>Immagine</th>
          <th>Outfit ID</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php
        for($i=0;$i<$outfit_number;$i++){
          $image = $decoded[$i]->image;
          $imagePath =Context::getContext()->shop->getBaseURL(true)."img/".$image;
          ?>
          <tr>
            <td><img src="<?=$imagePath?>" style="width:200px;" /></td>
            <td><?=$decoded[$i]->outfit_id?></td>
            <td><a class="btn btn-primary viewOutfit" href="javascript:void(0)" data-outfit-id="<?=$decoded[$i]->outfit_id?>">VISUALIZZA</a>
              <a class="btn btn-danger deleteOutfit" href="javascript:void(0)" data-outfit-id="<?=$decoded[$i]->outfit_id?>">ELIMINA</a>
            </td>
          </tr>
          <?php
        }
         ?>
      </tbody>
    </table>
    <script>
    $(".addNewOutfit").on("click",function(){
      $(".container_import").html(blankOutfitScheme());
      initUploader();
      function imageIsLoaded(e) {
        let src = e.target.result;
        $(".uploader_inner").html("<img src="+src+" style=\'width:100%;\' />");
        $(".uploader_controls").show();
        initAdd();
      }
      initSaveOutfit();
    });
    </script>
   <?php
 }
}else if($actions == "loadOutfitFromId"){
  $id = $_POST["id"];
  $outfits =  json_decode(Configuration::get('PS_WEEKOUTFIT'));
  foreach($outfits as $outfit){
    if($outfit->outfit_id == $id){
      $progr = 1;
      foreach($outfit->points as $punto){
        $product_id = $punto->product_id;
        $xPer = $punto->xPercent;
        $yPer = $punto->yPercent;
        $info = new Product((int)$product_id);
        $name = $info->name;
        $image = Product::getCover((int)$product_id);
        $image = new Image($image['id_image']);
        $imagePath = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath()."-medium_default.jpg";
        $htmlstruct .= '<div class="row" data-xPercent="'.$xPer.'" data-yPercent="'.$yPer.'" data-pid="'.$product_id.'"><div class="col-xs-4 thumb"><img src="'.$imagePath.'" style="width:100px;"></div><div class="col-xs-6 pinfo">'.$name[1].'</div><div class="col-sm-2 actions"><a class="btn btn-danger elimina" href="javascript:void(0)" data-delete-product-id="'.$product_id.'">ELIMINA</a></div></div>';
        $pointstruct .= '<div data-pid="'.$product_id.'" class="point point'.$progr.'" style="left:'.$xPer.'%;top:'.$yPer.'%;" data-xPercent="'.$xPer.'" data-yPercent="'.$yPer.'"></div>';
        $progr++;
      }
      ?>
      <div class="row">
        <a href="javascript:void()" onclick="location.reload(true)">Torna indietro</a>
      </div>
      <div class="row">
      <div class="col-sm-6">
        <label for="weekoutfit_name">Nome Outfit</label>
      <input class="form-control" name="weekoutfit_name" id="weekoutfit_name" value="<?=$outfit->weekoutfit_name?>">
      </div>
      <div class="col-sm-6">
        <label for="weekoutfit_desc">Descrizione Outfit</label>
      <input class="form-control" name="weekoutfit_desc" id="weekoutfit_desc" value="<?=$outfit->weekoutfit_desc?>">
      </div>
      </div>
      <div class="row">
        <div class="col-sm-7">
        <h1 class="text-center">Foto Outfit</h1>
        <div class="uploader">
          <div class="uploader_controls" style="display:block;">
            <a class="btn btn-success addpoint" href="javascript:void(0)" title="Aggiungi prodotto">+</a>
          </div>
          <div class="uploader_inner">
            <img src="<?=Context::getContext()->shop->getBaseURL(true)."img/".$outfit->image?>" style="width:100%;" />
            <?=$pointstruct?>
          </div>
        </div>
        </div>
        <div class="col-sm-5">
        <h1 class="text-center">Prodotti collegati alla Foto</h1>
        <div class="collegatore">
        <?=$htmlstruct?>
        </div>
        </div>
        </div>
        <div class="row">
        <button type="button" class="btn btn-success pull-right saveOutfit" data-outfit-id="<?=$outfit->outfit_id?>">
        <i class="process-icon-save"></i> Salva
        </button>
        </div>
      <?php
    }
  }
}else if($actions == "saveOutfitFromId"){
  $id = $_POST["id"];
  $outfits =  json_decode(Configuration::get('PS_WEEKOUTFIT'));
  for($i=0;$i<count($outfits);$i++){
    if($outfits[$i]->outfit_id == $id){
      unset($outfits[$i]);
      $image = str_replace("data:image/jpeg;base64,","",$_POST["image"],$repOc);
      $points = $_POST["points"];
      $weekoutfit_name = $_POST["weekoutfit_name"];
      $weekoutfit_desc = $_POST["weekoutfit_desc"];
      foreach($points as $key=>$point){
        $info = new Product((int)$point["product_id"]);
        $name = $info->name;
        $image = Product::getCover((int)$point["product_id"]);
        $image = new Image($image['id_image']);
        $imagePath = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath()."-home_default.jpg";
        $price = $info->price;
        $link = $info->getLink();
        $points[$key]["product_name"] = $name[1];
        $points[$key]["product_image"] = $imagePath;
        $points[$key]["product_price"] = $price;
        $points[$key]["product_link"] = $link;
      }
      $imageName = str_replace(Context::getContext()->shop->getBaseURL(true)."img/","",$_POST["image"]);
      $outfit = ["outfit_id"=>$id,"image"=>$imageName,"weekoutfit_name"=>$weekoutfit_name,"weekoutfit_desc"=>$weekoutfit_desc,"points"=>$points];
      $outfits[$i] = $outfit;
      $encoded = json_encode($outfits);
      if(Configuration::updateValue('PS_WEEKOUTFIT', $encoded)){
        echo "1";
      }else{
        echo "0";
      }
    }
  }
}
 ?>
