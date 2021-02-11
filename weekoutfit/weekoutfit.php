<?php

if (!defined('_PS_VERSION_'))
    exit;

/*
 * /views/css for the css files
 * /views/js for the javascript files
   /views/templates/front for the files used by the controller module.
 * /views/templates/hooks for the files used by the module’s hooks.
 * The configuration cache file (config.xml)
 * The module’s 16x16 logo (module_name.jpg)
 *
 */


class Weekoutfit extends Module {

    public function __construct() {
        $this->name = 'weekoutfit';
        $this->version = '0.5';
        $this->author = 'DGCAL SRL';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Outfit of the Week');
        $this->description = $this->l('Crea un modulo che inserisce nell\'homepage una foto collegata con dei prodotti.');
        $this->tab = 'Admin';
        $this->tabClassName = 'AdminSize';
        $this->tabParentName = 'AdminCatalog'; //in this example you add subtab under Tools tab. You may also declare new tab here
    }
    public function getConfigFieldsValues()
{
    return array(
      'PS_WEEKOUTFIT_STATUS' => Tools::getValue('PS_WEEKOUTFIT_STATUS', Configuration::get('PS_WEEKOUTFIT_STATUS') == '1' ? 1 : 0)
    );
}
public function setMedia(){
  $this->addJqueryUI();
}
public function getContent()
{
  $this->context->controller->addCSS($this->_path.'css/weekoutfit_admin.css');
  $this->context->controller->addJs('https://code.jquery.com/ui/1.12.0/jquery-ui.min.js');
  $this->context->controller->addJs($this->_path.'js/weekoutfit.js');
    if (Tools::isSubmit('submitForm'))
    {
  $my_value = ( (int)Tools::getValue('PS_WEEKOUTFIT_STATUS') == 1 ) ? '1' : '0';
  if (Configuration::updateValue('PS_WEEKOUTFIT_STATUS', $my_value))
            $echo .= $this->displayConfirmation($this->l('Impostazioni Aggiornate'));

    }
    $echo .= $this->renderForm();
    $echo .=
    '
    Tornare alla base
    <div class="load_admin">
    <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
    ';
     $currentOutfit = Configuration::get('PS_WEEKOUTFIT');
    if(isset($currentOutfit) && !empty($currentOutfit) && count(json_decode($currentOutfit)) > 0){
      $decoded = json_decode($currentOutfit);
      $htmlstruct = "";
      $pointstruct = "";
      $progr = 1;
      $outfit_id = $decoded->outfit_id;
      foreach($decoded->points as $punto){
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
      $echo .=
      '
      <div class="container_import">
       </div>
       ';

       $echo .=
       '
       <script>
       loadCurrentOutfit();
        </script>
       ';


    }else{
    $echo .=
    '
    <div class="container_import">
    <div class="row">
    <div class="col-sm-6">
    <label for="weekoutfit_name">Nome Outfit</label>
    <input class="form-control" name="weekoutfit_name" id="weekoutfit_name">
    </div>
    <div class="col-sm-6">
    <label for="weekoutfit_desc">Descrizione Outfit</label>
    <input class="form-control" name="weekoutfit_desc" id="weekoutfit_desc">
    </div>
    </div>
    <div class="row">
      <div class="col-sm-7">
      <h1 class="text-center">Scegli la foto dell\'outfit</h1>
      <div class="uploader">
        <div class="uploader_controls">
          <a class="btn btn-success addpoint" href="javascript:void(0)" title="Aggiungi prodotto">+</a>
        </div>
        <div class="uploader_inner">
          <input type="file" name="weekoutfit_file" id="weekoutfit_file" class="weekoutfit_file" value="Scegli la foto" />
        </div>
      </div>
      </div>
      <div class="col-sm-5">
      <h1 class="text-center">Collega i prodotti alla foto</h1>
      <div class="collegatore">
      </div>
      </div>
      </div>
      <div class="row">
  		<button type="button"  id="saveOutfit" class="btn btn-success pull-right">
  		<i class="process-icon-save"></i> Salva
  		</button>
      </div>
     </div>
     ';
     $echo .=
     '
     <script>
      initUploader();
      function imageIsLoaded(e) {
        let src = e.target.result;
        $(".uploader_inner").html("<img src="+src+" style=\'width:100%;\' />");
        $(".uploader_controls").show();
        initAdd();
      }
      initSaveOutfit();
      </script>
     ';
}
    return $echo;
}

    public function renderForm()
{

    $fields_form = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Impostazioni'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(

        array(
            'type' => 'switch',
            'label' => $this->l('Attivo'),
            'name' => 'PS_WEEKOUTFIT_STATUS',
            'is_bool' => true,
            'desc' => $this->l('Scegli se mostrare o nascondere il modulo dalla Homepage.'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('1')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('0')
                )
            ),
        )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        ),
    );

    $helper = new HelperForm();
    $helper->module = $this;
    $helper->show_toolbar = false;
    $helper->table = $this->table;
    $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    $helper->default_form_language = $lang->id;
    $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    $this->fields_form = array();

    $helper->identifier = $this->identifier;
    $helper->submit_action = 'submitForm';
    $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->tpl_vars = array(
        'fields_value' => $this->getConfigFieldsValues(),
        'languages' => $this->context->controller->getLanguages(),
        'id_language' => $this->context->language->id
    );

    return $helper->generateForm(array($fields_form));
}



public function hookDisplayTopColumn($params)
{
  $outfits = json_decode(Configuration::get('PS_WEEKOUTFIT'));
  foreach($outfits as $outfit){
    foreach($outfit->points as $key=>$punto){
      $pid = $punto->product_id;
      $info = new Product((int)$pid);
      $name = $info->name;
      $image = Product::getCover((int)$pid);
      $image = new Image($image['id_image']);
      $imagePath = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath()."-home_default.jpg";
      $price = $info->price;
      $link = $info->getLink();
      $punto->product_name = $name[1];
      $punto->product_image = $imagePath;
      $punto->product_price = $price;
      $punto->product_link = $link;
    }
  }
  $this->context->smarty->assign(
      array(
          'weekoutfit_status' => Configuration::get('PS_WEEKOUTFIT_STATUS'),
          'outfits' => $outfits
      )
  );
  return $this->display(__FILE__, 'weekoutfit.tpl');
}
public function hookDisplayHeader($params)
{
  $this->context->controller->addCSS($this->_path.'css/weekoutfit.css', 'all');
  $this->context->controller->addJs($this->_path.'js/weekoutfit.front.js');
}





















    public function install() {
      if ( Shop::isFeatureActive() ){
  Shop::setContext( Shop::CONTEXT_ALL );
 }
        if (!parent::install()
        || !$this->registerHook('displayTopColumn')
        || !$this->registerHook( 'header' )
          )
            return false;
            $currentid = Tab::getIdFromClassName('AdminOutfit');
            if (!$currentid ) {
              $tab = new Tab();
              $tab->active = 1;
              $tab->class_name = "AdminOutfit";
              $tab->name = array();
              foreach (Language::getLanguages() as $lang){
                $tab->name[$lang['id_lang']] = "Outfit of the Week";
              }
              $tab->id_parent = '0';
              $tab->module = $this->name;
              $tab->add();
            }
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()
         )
            return false;
            $currentid = Tab::getIdFromClassName('AdminOutfit');
            if($currentid){
              $tab = new Tab($currentid);
              $tab->delete();
            }
            if(Configuration::get('PS_WEEKOUTFIT_STATUS') || Configuration::get('PS_WEEKOUTFIT')){
              Configuration::deleteByName('PS_WEEKOUTFIT_STATUS');
              Configuration::deleteByName('PS_WEEKOUTFIT');
            }
        return true;
    }


}
